<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}
if (empty($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please sign in again."]);
    exit;
}

$user_id = (int)$_SESSION["user_id"];
$name = trim($_POST["name"] ?? "");
$blood_group = trim($_POST["blood_group"] ?? "");
$city = trim($_POST["city"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$availability = trim($_POST["availability"] ?? "Available");
$allowed_groups = ["", "A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

if ($name === "" || !in_array($blood_group, $allowed_groups, true) ||
    !in_array($availability, ["Available", "Not available"], true) ||
    ($phone !== "" && !preg_match('/^[0-9+() -]{7,20}$/', $phone))) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Please check the profile details."]);
    exit;
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $user_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare(
        "UPDATE donors SET phone = ?, blood_group = ?, city = ?, availability = ?
         WHERE user_id = ?"
    );
    $stmt->bind_param("ssssi", $phone, $blood_group, $city, $availability, $user_id);
    $stmt->execute();
    $stmt->close();

    $check = $conn->prepare("SELECT id FROM donors WHERE user_id = ? LIMIT 1");
    $check->bind_param("i", $user_id);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();

    if (!$exists) {
        if ($phone === "" || $blood_group === "" || $city === "") {
            throw new Exception("Add phone, blood group and city before saving.");
        }
        $stmt = $conn->prepare(
            "INSERT INTO donors (user_id, phone, blood_group, city, availability)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issss", $user_id, $phone, $blood_group, $city, $availability);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    $_SESSION["user_name"] = $name;
    echo json_encode(["success" => true, "message" => "Profile updated."]);
} catch (Throwable $e) {
    $conn->rollback();
    error_log($e->getMessage());
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>