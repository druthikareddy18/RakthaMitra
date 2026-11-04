<?php
session_start();
require_once "db.php";
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in to view notifications."]);
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$stmt = $conn->prepare(
    "SELECT id, request_id, message, is_read, created_at
     FROM notifications
     WHERE user_id = ?
     ORDER BY created_at DESC, id DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];
while ($row = $result->fetch_assoc()) $notifications[] = $row;
$stmt->close();

echo json_encode(["success" => true, "notifications" => $notifications]);
?>
