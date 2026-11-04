
<?php
require_once "db.php";

header("Content-Type: application/json");

$sql = "SELECT 
            u.name,
            d.phone,
            d.blood_group,
            d.city,
            d.age,
            d.gender,
            d.availability
        FROM donors d
        INNER JOIN users u ON d.user_id = u.id
        WHERE d.availability = 'Available'
        ORDER BY u.name ASC";

$result = $conn->query($sql);

$donors = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $donors[] = $row;
    }
}

echo json_encode($donors);
?>
