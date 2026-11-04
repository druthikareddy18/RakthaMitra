<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

if (empty($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please sign in again."]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT u.name, u.email, d.blood_group, d.city, d.phone, d.availability
     FROM users u
     LEFT JOIN donors d ON d.user_id = u.id
     WHERE u.id = ? LIMIT 1"
);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Profile not found."]);
    exit;
}
$user["consent"] = "Yes";
echo json_encode(["success" => true, "profile" => $user]);
?>