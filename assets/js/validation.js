function validateRegister() {
    const pass = document.getElementById("password").value;
    const confirm = document.getElementById("confirm_password").value;

    if (pass !== confirm) {
        alert("Passwords do not match!");
        return false;
    }
    return true;
}