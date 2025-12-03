# RFID Attendance System (PHP Backend)

## Quick setup (XAMPP)
1. Copy the folder `rfid-final-php` into `C:/xampp/htdocs/` (or your Apache docroot).
2. Start Apache and MySQL via XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/` and import `db/setup.sql` to create tables and sample data.
4. Open `http://localhost/rfid-final-php/login.html` to login (default admin will be created with password `admin123` on first login).
5. Configure Arduino Python script: update SERIAL_PORT and API_URL, then run `python arduino_to_php.py` to forward scanned roll numbers to the server.

