# Login & Registration System (PHP + MySQL)

## Run it (XAMPP)
1. Copy the `login-system` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open http://localhost/login-system/login.php
   The database `login_system` and `users` table are created automatically on first load.
   If your MySQL has a root password, edit the settings at the top of `config.php`.

## Use your Figma backgrounds
Export the two background images from Figma and save them as:
- `assets/img/login-bg.jpg`
- `assets/img/register-bg.jpg`

## Files
| File | Purpose |
|------|---------|
| login.php | Login form + validation |
| register.php | Sign-up form + validation |
| dashboard.php | Page shown after login (protected) |
| logout.php | Ends the session |
| config.php | DB connection, helpers, CSRF, flash messages |
| assets/css/style.css | All styling |
| assets/js/app.js | Show/hide password via lock icon |

## Validation implemented
- Required fields (both forms)
- Username: 3–20 letters, numbers, underscores; must be unique
- Email: valid format; must be unique
- Password: 8+ chars with upper, lower case and a number
- Confirm password must match
- Error messages under each field; success message on login page after registering
- Security: prepared statements, password_hash/verify, output escaping, CSRF token
