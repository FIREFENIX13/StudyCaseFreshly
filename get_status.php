<?php
include 'conndb.php';

$sql = "SELECT DISTINCT name FROM order_state_lang ORDER BY name ASC";
$result = $conn->query($sql);

$status = [];

$status = ["Aceptados y en proceso"]; // opción para "Pago aceptado" y "Preparación en proceso"

while ($row = $result->fetch_assoc()) {
    $status[] = $row['name'];
}

header('Content-Type: application/json');
echo json_encode($status);
?>