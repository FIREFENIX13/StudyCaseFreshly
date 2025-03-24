<?php
include 'conndb.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['OrderId']) || !isset($data['OrderStatus'])) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos."]);
    exit;
}

$orderId = intval($data['OrderId']);
$orderStatusName = $conn->real_escape_string($data['OrderStatus']);

// Buscar el ID del estado por su nombre
$result = $conn->query("SELECT id_order_state FROM order_state_lang WHERE name = '$orderStatusName'");
//$result = $conn->query("SELECT id_order_state FROM order_state_lang WHERE name = '$orderStatusName' AND id_lang = 1");
if ($result && $row = $result->fetch_assoc()) {
    $estadoId = $row['id_order_state'];
} else {
    http_response_code(400);
    echo json_encode(["error" => "Estado no válido."]);
    exit;
}


//$sql = "UPDATE orders SET current_state = '$orderStatus' WHERE id_order = $orderId";
$sql = "UPDATE orders SET current_state = '$estadoId' WHERE id_order = $orderId";

//echo "<script>console.log('Debug Objects: " . $sql . "' );</script>";

if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar pedido: " . $conn->error]);
}
?>