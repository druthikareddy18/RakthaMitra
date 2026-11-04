<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
if (!empty($_SESSION["user_id"])) {
    echo json_encode([
        "logged_in" => true,
        "email" => $_SESSION["user_email"] ?? "",
        "name" => $_SESSION["user_name"] ?? ""
    ]);
} else {
    echo json_encode(["logged_in" => false]);
}
?>