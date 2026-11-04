<?php
session_start();
require_once "db.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in first."]);
    exit;
}

$user_id = (int)$_SESSION["user_id"];

if (isset($_POST["mark_all"]) && $_POST["mark_all"] === "1") {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
} else {
    $notification_id = filter_input(INPUT_POST, "notification_id", FILTER_VALIDATE_INT);
    if (!$notification_id) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid notification."]);
        exit;
    }
    $stmt = $conn->prepare(
        "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param("ii", $notification_id, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Notification updated."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Could not update notification."]);
}
$stmt->close();
?>
