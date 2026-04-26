<?php
const APP_SESSION_TIMEOUT = 1800;
const LOGIN_THROTTLE_WINDOW = 900;
const LOGIN_THROTTLE_MAX_ATTEMPTS = 5;
const LOGIN_THROTTLE_BLOCK_SECONDS = 300;
const LOGIN_THROTTLE_DELAY_AFTER = 3;
const LOGIN_THROTTLE_MAX_DELAY = 3;
const PRESCRIPTION_MAX_UPLOAD_BYTES = 5242880;

function app_session_is_secure(): bool {
    return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
}

function start_secure_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = app_session_is_secure();
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', $secure ? '1' : '0');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function restart_secure_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    start_secure_session();
}

start_secure_session();
if (isset($_SESSION['last_activity']) && (time() - (int)$_SESSION['last_activity']) > APP_SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    restart_secure_session();
    $_SESSION['flash_error'] = 'Session expired. Please log in again.';
}
$_SESSION['last_activity'] = time();

function app_base(): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $pos = strpos($script, '/public/');
    if ($pos !== false) {
        return substr($script, 0, $pos + 7);
    }
    return rtrim(dirname($script), '/');
}

function url_for(string $path = ''): string {
    $base = rtrim(app_base(), '/');
    $path = ltrim($path, '/');
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function redirect_to(string $path): never {
    header('Location: ' . url_for($path));
    exit;
}

function redirect_to_role_dashboard(string $role): never {
    if ($role === 'admin') {
        redirect_to('admin/admin_dashboard.php');
    }
    if ($role === 'pharmacist') {
        redirect_to('pharmacist/pharmacist_dashboard.php');
    }
    redirect_to('products.php');
}

function esc($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = 'Please log in first.';
        redirect_to('login.php');
    }
}

function requireRole(string $role): void {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('Access denied');
    }
}

function flash_message(string $key): ?string {
    if (!isset($_SESSION[$key])) {
        return null;
    }
    $value = $_SESSION[$key];
    unset($_SESSION[$key]);
    return $value;
}

function login_throttle_store_path(): string {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        error_log('Unable to create login throttle directory: ' . $dir);
    }
    return $dir . '/login_throttle.json';
}

function with_login_throttle_store(callable $callback) {
    $path = login_throttle_store_path();
    $handle = fopen($path, 'c+');
    if ($handle === false) {
        error_log('Unable to open login throttle store.');
        $data = [];
        return $callback($data);
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        error_log('Unable to lock login throttle store.');
        $data = [];
        return $callback($data);
    }

    try {
        rewind($handle);
        $raw = stream_get_contents($handle);
        $data = json_decode($raw ?: '{}', true);
        if (!is_array($data)) {
            $data = [];
        }

        $result = $callback($data);

        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode($data, JSON_PRETTY_PRINT));
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        return $result;
    } catch (Throwable $e) {
        flock($handle, LOCK_UN);
        fclose($handle);
        throw $e;
    }
}

function current_login_throttle_key(): string {
    $ip = trim((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    return hash('sha256', $ip === '' ? 'unknown' : $ip);
}

function purge_expired_login_throttle_entries(array &$store): void {
    $now = time();
    foreach ($store as $key => $entry) {
        $lastFailedAt = (int)($entry['last_failed_at'] ?? 0);
        $blockedUntil = (int)($entry['blocked_until'] ?? 0);

        if ($blockedUntil > $now) {
            continue;
        }

        if ($lastFailedAt === 0 || ($now - $lastFailedAt) > LOGIN_THROTTLE_WINDOW) {
            unset($store[$key]);
        }
    }
}

function get_login_throttle_state(): array {
    return with_login_throttle_store(function (array &$store): array {
        purge_expired_login_throttle_entries($store);
        $entry = $store[current_login_throttle_key()] ?? [];

        return [
            'failures' => (int)($entry['failures'] ?? 0),
            'blocked_until' => (int)($entry['blocked_until'] ?? 0),
        ];
    });
}

function enforce_login_throttle(): void {
    $state = get_login_throttle_state();
    $now = time();

    if (($state['blocked_until'] ?? 0) > $now) {
        $retryAfter = (int)$state['blocked_until'] - $now;
        throw new RuntimeException('Too many login attempts. Please wait ' . $retryAfter . ' seconds and try again.');
    }

    $failures = (int)($state['failures'] ?? 0);
    if ($failures >= LOGIN_THROTTLE_DELAY_AFTER) {
        $delay = min($failures - LOGIN_THROTTLE_DELAY_AFTER + 1, LOGIN_THROTTLE_MAX_DELAY);
        sleep($delay);
    }
}

function register_failed_login_attempt(): void {
    with_login_throttle_store(function (array &$store): bool {
        purge_expired_login_throttle_entries($store);

        $key = current_login_throttle_key();
        $now = time();
        $entry = $store[$key] ?? ['failures' => 0, 'last_failed_at' => 0, 'blocked_until' => 0];

        if (($now - (int)$entry['last_failed_at']) > LOGIN_THROTTLE_WINDOW) {
            $entry['failures'] = 0;
            $entry['blocked_until'] = 0;
        }

        $entry['failures'] = (int)$entry['failures'] + 1;
        $entry['last_failed_at'] = $now;

        if ($entry['failures'] >= LOGIN_THROTTLE_MAX_ATTEMPTS) {
            $entry['blocked_until'] = $now + LOGIN_THROTTLE_BLOCK_SECONDS;
        }

        $store[$key] = $entry;

        return true;
    });
}

function clear_login_throttle(): void {
    with_login_throttle_store(function (array &$store): bool {
        purge_expired_login_throttle_entries($store);
        unset($store[current_login_throttle_key()]);
        return true;
    });
}

function complete_login(array $user): never {
    session_regenerate_id(true);
    clear_login_throttle();
    $_SESSION['user_id'] = (int)$user['user_id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['last_activity'] = time();
    redirect_to_role_dashboard((string)$user['role']);
}

function allowed_prescription_upload_types(): array {
    return [
        'pdf' => ['application/pdf'],
        'png' => ['image/png'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
    ];
}

function prescription_upload_dir(): string {
    return dirname(__DIR__) . '/uploads/prescriptions/';
}

function prescription_upload_relative_path(string $filename): string {
    return 'uploads/prescriptions/' . $filename;
}

function ensure_directory_exists(string $path, int $mode = 0755): void {
    if (!is_dir($path) && !mkdir($path, $mode, true) && !is_dir($path)) {
        throw new RuntimeException('Upload storage is unavailable right now.');
    }
}

function canonical_prescription_extension(string $mimeType, string $extension): string {
    if ($mimeType === 'image/jpeg') {
        return 'jpg';
    }
    if ($mimeType === 'image/png') {
        return 'png';
    }
    if ($mimeType === 'application/pdf') {
        return 'pdf';
    }
    return $extension;
}

function validate_prescription_upload(array $file): array {
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Please choose a prescription file to upload.');
    }
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('The file is too large. Please upload a file under 5 MB.');
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The upload could not be completed. Please try again.');
    }

    $tmpName = (string)($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('The upload could not be completed. Please try again.');
    }

    $size = (int)($file['size'] ?? 0);
    if ($size <= 0) {
        throw new RuntimeException('The uploaded file is empty.');
    }
    if ($size > PRESCRIPTION_MAX_UPLOAD_BYTES) {
        throw new RuntimeException('The file is too large. Please upload a file under 5 MB.');
    }

    $originalName = (string)($file['name'] ?? '');
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedTypes = allowed_prescription_upload_types();
    if (!isset($allowedTypes[$extension])) {
        throw new RuntimeException('Please upload a PDF, JPG, or PNG file.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        error_log('Unable to initialize fileinfo for prescription upload.');
        throw new RuntimeException('The upload could not be completed. Please try again.');
    }

    $mimeType = (string)(finfo_file($finfo, $tmpName) ?: '');
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes[$extension], true)) {
        throw new RuntimeException('Please upload a PDF, JPG, or PNG file.');
    }

    return [
        'tmp_name' => $tmpName,
        'extension' => canonical_prescription_extension($mimeType, $extension),
        'mime_type' => $mimeType,
        'size' => $size,
    ];
}

function store_prescription_upload(array $file): string {
    $validated = validate_prescription_upload($file);
    $uploadDir = prescription_upload_dir();
    ensure_directory_exists($uploadDir, 0755);

    $filename = bin2hex(random_bytes(16)) . '.' . $validated['extension'];
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($validated['tmp_name'], $targetPath)) {
        throw new RuntimeException('The upload could not be completed. Please try again.');
    }

    @chmod($targetPath, 0644);

    return prescription_upload_relative_path($filename);
}

function delete_prescription_upload(string $relativePath): void {
    $normalizedPath = str_replace('\\', '/', trim($relativePath));
    $prefix = 'uploads/prescriptions/';
    if (strpos($normalizedPath, $prefix) !== 0) {
        return;
    }

    $baseDir = realpath(prescription_upload_dir());
    $fullPath = realpath(dirname(__DIR__) . '/' . ltrim($normalizedPath, '/'));
    if ($baseDir === false || $fullPath === false) {
        return;
    }

    $basePrefix = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (strpos($fullPath, $basePrefix) !== 0) {
        return;
    }

    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}

function get_prescription_upload_context(PDO $pdo, int $orderId, int $userId): array {
    $stmt = $pdo->prepare('SELECT order_id, status FROM Orders WHERE order_id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if (!$order) {
        throw new RuntimeException('Unable to upload a prescription for that order.');
    }

    $status = strtolower(trim((string)($order['status'] ?? '')));
    if (strpos($status, 'pending') !== 0) {
        throw new RuntimeException('This order is not available for prescription upload.');
    }

    $stmt = $pdo->prepare('SELECT prescription_id, file_path, status FROM Prescriptions WHERE order_id = ? AND user_id = ? ORDER BY prescription_id DESC LIMIT 1');
    $stmt->execute([$orderId, $userId]);
    $prescription = $stmt->fetch();

    if ($prescription && ($prescription['status'] ?? '') === 'approved') {
        throw new RuntimeException('A prescription has already been accepted for this order.');
    }

    return [
        'order' => $order,
        'prescription' => $prescription ?: null,
    ];
}

function save_prescription_record(PDO $pdo, int $orderId, int $userId, string $filePath): ?string {
    $stmt = $pdo->prepare('SELECT prescription_id, file_path, status FROM Prescriptions WHERE order_id = ? AND user_id = ? ORDER BY prescription_id DESC LIMIT 1');
    $stmt->execute([$orderId, $userId]);
    $existing = $stmt->fetch();

    if (!$existing) {
        $stmt = $pdo->prepare("INSERT INTO Prescriptions (order_id, user_id, file_path, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$orderId, $userId, $filePath]);
        return null;
    }

    if (($existing['status'] ?? '') === 'approved') {
        throw new RuntimeException('A prescription has already been accepted for this order.');
    }

    $stmt = $pdo->prepare("UPDATE Prescriptions SET file_path = ?, status = 'pending', uploaded_at = NOW() WHERE prescription_id = ?");
    $stmt->execute([$filePath, (int)$existing['prescription_id']]);

    return (string)($existing['file_path'] ?? '');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

function verify_csrf_or_die(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        exit('CSRF validation failed');
    }
}

function post_int(string $key, int $min = 1): int {
    $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
    if ($value === false || $value === null || $value < $min) {
        throw new InvalidArgumentException("Invalid {$key}");
    }
    return (int)$value;
}

function get_int(string $key, int $min = 1): int {
    $value = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
    if ($value === false || $value === null || $value < $min) {
        throw new InvalidArgumentException("Invalid {$key}");
    }
    return (int)$value;
}

function post_string(string $key, int $maxLen = 255, bool $required = true): string {
    $value = trim((string)($_POST[$key] ?? ''));
    if ($required && $value === '') {
        throw new InvalidArgumentException("{$key} is required");
    }
    if (mb_strlen($value) > $maxLen) {
        throw new InvalidArgumentException("{$key} is too long");
    }
    return $value;
}

function post_enum(string $key, array $allowed): string {
    $value = trim((string)($_POST[$key] ?? ''));
    if (!in_array($value, $allowed, true)) {
        throw new InvalidArgumentException("Invalid {$key}");
    }
    return $value;
}
