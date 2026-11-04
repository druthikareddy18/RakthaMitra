<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request method.");
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$stmt = $conn->prepare(
    "SELECT id, name, email, password FROM users WHERE email = ?"
);

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user["password"])) {

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["name"];
    $_SESSION["user_email"] = $user["email"];

    echo "LOGIN_SUCCESS:" . $user["email"] . ":" .
         rawurlencode($user["name"]);

} else {
    http_response_code(401);
    echo "Incorrect email or password.";
}
?>