RAKTHAMITRA - COMPLETE IN-APP NOTIFICATION UPDATE

This package adds:
- Notifications page in the website
- Unread notification count in the navigation bar
- Notification when a new blood request is created
- Notification when a request status changes
- Mark one notification as read
- Mark all notifications as read
- Notification data stored in MySQL

INSTALLATION
1. Make a backup copy of your current index.html and PHP files.
2. Open phpMyAdmin and select the rakthamitra database.
3. Open the SQL tab and run the complete contents of notifications_setup.sql.
4. Extract this ZIP into C:\xampp\htdocs\RakthaMitra_Profile.
5. Replace index.html, create_request.php and update_request_status.php.
6. Copy get_notifications.php and mark_notification_read.php into the same folder.
7. Do not delete or replace db.php, login.php, register.php, get_profile.php,
   update_profile.php, find_donors.php, get_requests.php, session.php or logout.php.
8. Confirm Apache and MySQL are running in XAMPP.
9. Open http://localhost/RakthaMitra_Profile/ and sign in.
10. Create a test request, open Notifications, and check the alert.
11. Change the request status and refresh Notifications to see the new alert.

IMPORTANT
- The SQL setup must be run before testing the notification feature.
- This is an in-app notification system, not SMS, WhatsApp, or email.
- The current website lets the signed-in requester update their own request status.
  A notification is created when that status is changed. It does not mean a donor
  independently responded or that a medical donation was verified.
- Confirm donor availability and medical suitability with an authorized blood bank
  or healthcare professional.
