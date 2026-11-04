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

$request_id = filter_input(INPUT_POST, "request_id", FILTER_VALIDATE_INT);
$status = trim($_POST["status"] ?? "");
$allowed = ["Request Created", "Donor Response Received", "Donor Found", "Request Fulfilled"];
$user_id = (int)$_SESSION["user_id"];

if (!$request_id || !in_array($status, $allowed, true)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid request ID or status."]);
    exit;
}

try {
    $check = $conn->prepare("SELECT status FROM blood_requests WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $request_id, $user_id);
    $check->execute();
    $old = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$old) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Request not found in your account."]);
        exit;
    }

    if ($old["status"] !== $status) {
        $conn->begin_transaction();
        $update = $conn->prepare("UPDATE blood_requests SET status = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("sii", $status, $request_id, $user_id);
        $update->execute();
        $update->close();

        $messages = [
            "Request Created" => "Your blood request #" . $request_id . " is now marked as Request Created.",
            "Donor Response Received" => "A donor response has been recorded for blood request #" . $request_id . ".",
            "Donor Found" => "A donor has been marked as found for blood request #" . $request_id . ".",
            "Request Fulfilled" => "Blood request #" . $request_id . " has been marked as fulfilled."
        ];
        $message = $messages[$status];
        $notice = $conn->prepare(
            "INSERT INTO notifications (user_id, request_id, message) VALUES (?, ?, ?)"
        );
        $notice->bind_param("iis", $user_id, $request_id, $message);
        $notice->execute();
        $notice->close();
        $conn->commit();
    }

    echo json_encode(["success" => true, "message" => "Request status updated."]);
} catch (Throwable $e) {
    if ($conn->errno === 0) { /* transaction state may already be closed */ }
    try { $conn->rollback(); } catch (Throwable $ignored) {}
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Could not update request status."]);
}
?>
