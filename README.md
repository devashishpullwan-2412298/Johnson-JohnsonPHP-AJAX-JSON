# Online Pharmacy - Full Working Project

## Overview
This project is a working online pharmacy web application built with:
- PHP (PDO) for server-side logic, authentication, validation, and CRUD
- HTML, CSS, JavaScript, jQuery, AJAX, JSON, and JSON Schema validation
- Session-based cart, orders, and prescription upload handling
- CSRF protection, password hashing, prepared statements, and session hardening

## Install / Run (local XAMPP)
1. Import `database_online_pharmacy.sql` into a MySQL database named `online_pharmacy`.
2. Adjust DB credentials in `src/config/db.php` only if your DB name, user, or password differ.
3. Place the project folder inside `C:\xampp\htdocs\`.
4. Ensure the `uploads/` folder is writable by the web server.
5. Open `http://localhost/j-jpharmacy_testing2-main/j-jpharmacy_testing2-main/public/login.php`.

## Project Structure
- `public/` : frontend pages and user-facing workflows
- `assets/` : CSS, JavaScript, and medicine images
- `src/` : PHP backend handlers and shared helpers
- `uploads/` : uploaded prescriptions
- `data/` : login throttle store and JSON showcase files

## Account Roles
- Public registration creates `customer` accounts only.
- `pharmacist` and `admin` accounts are created internally for demonstration and management purposes.
- Existing pharmacist and admin logins continue to work normally.

## Notes
- This is a demonstration app, not a production deployment.
- The project includes security hardening such as CSRF checks, login throttling, secure session defaults, safer upload validation, and role-based route protection.
- Additional production controls such as CSP, stricter deployment configuration, and deeper auditing would still be recommended.
