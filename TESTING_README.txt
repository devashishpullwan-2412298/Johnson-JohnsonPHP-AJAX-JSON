J&J Pharmacy Testing

1. Place the project folder inside your web root.
2. Import database_online_pharmacy.sql into a MySQL database named online_pharmacy.
3. Update src/config/db.php only if your DB name, user, or password differ.
4. Open /public/login.php in your browser.

Demo logins after importing the included SQL dump:
- Admin: admin@system.com / admin123
- Pharmacist: jane@pharmacy.com / pharma123
- Customer: bomboclat@hotmail.com / user12345

Suggested local folder name for fast testing:
- j_jpharmacy_testing

If you keep special characters/spaces in the folder name, the generated links still work, but localhost URLs are easier if you use an underscore-based folder name.
