RAKTHAMITRA - DATABASE CONNECTION SETUP

Your ZIP already contains the HTML frontend and PHP endpoint files. The website's
JavaScript calls these endpoints:
- register.php, login.php, logout.php, session.php
- get_profile.php, update_profile.php
- find_donors.php, create_request.php, get_requests.php
- dashboard_stats.php, open_requests.php, respond_to_request.php
- get_notifications.php, mark_notification_read.php, update_request_status.php

BEFORE RUNNING:
1. Make a backup of your current RakthaMitra folder and database.
2. Start Apache and MySQL in XAMPP.
3. Open http://localhost/phpmyadmin/
4. Select the existing database named rakthamitra.
5. Click the SQL tab.
6. Open connect_all_tables.sql, copy all its contents, paste into SQL, and click Go.
7. Keep your existing db.php. It connects to localhost, user root, blank password,
   database rakthamitra.
8. Open the site through http://localhost/rakthamitra/ (adjust folder name if needed).

IMPORTANT:
- This setup script preserves existing users and donors records.
- It adds the availability column to donors if it is missing.
- It creates blood_requests, notifications and donor_responses if they do not exist.
- Do not import or replace the XAMPP mysql/data folder.
- If phpMyAdmin reports an SQL error, stop and share the exact error before changing
  any tables manually.
