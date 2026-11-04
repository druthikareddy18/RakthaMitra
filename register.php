
<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request method.");
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$phone = trim($_POST["phone"] ?? "");
$blood_group = trim($_POST["blood_group"] ?? "");
$city = trim($_POST["city"] ?? "");
$age = (int)($_POST["age"] ?? 0);
$gender = trim($_POST["gender"] ?? "");

$allowed_groups = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

if (
    $name === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($password) < 6 ||
    $phone === "" ||
    !in_array($blood_group, $allowed_groups, true) ||
    $city === ""
) {
    exit("Please enter valid registration details.");
}

if ($age < 18 || $age > 65) {
    exit("Please enter an age between 18 and 65.");
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    $stmt->execute();

    $user_id = $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare(
        "INSERT INTO donors
        (user_id, phone, blood_group, city, age, gender)
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "isssis",
        $user_id,
        $phone,
        $blood_group,
        $city,
        $age,
        $gender
    );
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo "Donor registered successfully!";
} catch (mysqli_sql_exception $e) {
    $conn->rollback();

    if ($e->getCode() == 1062) {
        echo "This email is already registered.";
    } else {
        error_log($e->getMessage());
        echo "Registration failed. Please try again.";
    }
}
?>
