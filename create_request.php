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
    echo json_encode(["success" => false, "message" => "Please log in before submitting a request."]);
    exit;
}

$patient_name = trim($_POST["patient_name"] ?? "");
$blood_group = trim($_POST["blood_group"] ?? "");
$units = (int)($_POST["units"] ?? 0);
$hospital = trim($_POST["hospital"] ?? "");
$city = trim($_POST["city"] ?? "");
$required_date = trim($_POST["required_date"] ?? "");
$urgency = trim($_POST["urgency"] ?? "Planned");
$notes = trim($_POST["notes"] ?? "");

$allowed_groups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
$allowed_urgency = ["Urgent", "Within 24 hours", "Planned"];

if ($patient_name === "" || !in_array($blood_group, $allowed_groups, true) ||
    $units < 1 || $units > 4 || $hospital === "" || $city === "" ||
    !in_array($urgency, $allowed_urgency, true)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Please enter valid request details."]);
    exit;
}
if ($required_date === "") $required_date = null;

$user_id = (int)$_SESSION["user_id"];
$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "INSERT INTO blood_requests
         (user_id, patient_name, blood_group, units, hospital, city, required_date, urgency, notes)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ississsss",
        $user_id, $patient_name, $blood_group, $units,
        $hospital, $city, $required_date, $urgency, $notes
    );
    $stmt->execute();
    $request_id = $conn->insert_id;
    $stmt->close();

    $message = "Your blood request #" . $request_id . " has been created.";
    $notice = $conn->prepare(
        "INSERT INTO notifications (user_id, request_id, message) VALUES (?, ?, ?)"
    );
    $notice->bind_param("iis", $user_id, $request_id, $message);
    $notice->execute();
    $notice->close();

    $conn->commit();
    echo json_encode([
        "success" => true,
        "message" => "Blood request saved successfully!",
        "request_id" => $request_id
    ]);
} catch (Throwable $e) {
    $conn->rollback();
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Could not save your request. Check the database setup."]);
}
?>
