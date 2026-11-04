<?php
session_start();
require_once "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in to view your requests."]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, patient_name, blood_group, units, hospital, city,
            urgency, status, created_at
     FROM blood_requests
     WHERE user_id = ?
     ORDER BY created_at DESC, id DESC"
);
$user_id = $_SESSION["user_id"];
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}
$stmt->close();

echo json_encode(["success" => true, "requests" => $requests]);
?>
