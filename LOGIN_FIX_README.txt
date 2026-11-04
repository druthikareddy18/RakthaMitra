RakthaMitra login patch

1. BACK UP your existing index.html and login.php first.
2. Copy the supplied index.html and login.php into C:\xampp\htdocs\RakthaMitra_Profile\ and replace the old files.
3. Keep your existing db.php in the same folder. It should connect to database rakthamitra.
4. Start Apache and MySQL in XAMPP.
5. Open http://localhost/RakthaMitra_Profile/ (do not open index.html by double-clicking).
6. Sign in using an account already registered in the users table.

What this patch changes: handles whitespace/BOM in the PHP response, keeps the PHP session cookie on the fetch request, disables the button while signing in, uses mysqli bind_result (avoids dependency on get_result/mysqlnd), and redirects successful login to Home.

Important: only index.html, login.php and db.php were supplied for this review. Other required endpoints (session.php, register.php, find_donors.php, etc.) were not supplied, so the entire website cannot yet be verified.
