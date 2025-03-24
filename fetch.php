<?php
include 'conndb.php';
header('Content-Type: application/json');

// Filtros desde el frontend
$country = isset($_GET['country']) ? $conn->real_escape_string($_GET['country']) : '';
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Prueba de Mapeo de estados 
/*$estadoTexto = [
    '0' => 'Pendiente',
    '1' => 'Enviado',
    '2' => 'Entregado'
];*/

/*$sql = "
SELECT 
  o.id_order AS OrderId,
  o.date_add AS DateOrder,
  CONCAT(a.firstname, ' ', a.lastname) AS CustomerFullname,
  TRIM(
    CONCAT(
        a.address1,
        IF(a.address2 != '', CONCAT(' ', a.address2), ''),
        IF(a.city != '', CONCAT(', ', a.city), '')
    )
  ) AS CustomerAdress,
  c.name AS OrderCountry,
  GROUP_CONCAT(CONCAT(od.product_name, ' x', od.product_quantity) SEPARATOR ', ') AS ProductsOrdered,
  o.current_state AS OrderStatus 
FROM orders o
JOIN address a ON o.id_address_delivery = a.id_address
JOIN country_lang c ON a.id_country = c.id_country
JOIN order_detail od ON o.id_order = od.id_order
JOIN order_state_lang osl ON o.current_state = osl.id_order_state
WHERE 1=1";*/


$sql = "
SELECT
  o.id_order AS OrderId,
  o.date_add AS DateOrder,
  CONCAT(a.firstname, ' ', a.lastname) AS CustomerFullname,
  CONCAT(a.address1, ', ', a.address2, ', ', a.city) AS CustomerAdress,
  cl.name AS OrderCountry,
  GROUP_CONCAT(od.product_name SEPARATOR ', ') AS ProductsOrdered,
  osl.name AS OrderStatus
FROM orders o
JOIN address a ON o.id_address_delivery = a.id_address
JOIN country_lang cl ON a.id_country = cl.id_country
JOIN order_state_lang osl ON o.current_state = osl.id_order_state
JOIN order_detail od ON o.id_order = od.id_order
WHERE 1=1 -- WHERE o.current_state IN (2, 3) -- pago aceptado o preparación
";

    //  CONCAT(a.address1, ' ', a.address2, ', ', a.city) AS CustomerAdress,
    //  GROUP_CONCAT(p.name SEPARATOR ', ') AS ProductsOrdered,  // pruebas
    //  o.current_state AS OrderStatus                           // pruebas
    //  osl.name AS OrderStatus
    //  JOIN order_state_lang osl ON o.current_state = osl.id_order_state


//Parámetros para los filtros
$params = [];
$types = "";

/*if ($country !== '') {
    $sql .= " AND c.name = '$country'";
}*/

// Filtro por país
if (!empty($country)) {
    $sql .= " AND cl.name = ?";
    $params[] = $country;
    $types .= "s";
}


/*if ($status !== '') {
    
    //$estadoReverso = array_flip($estadoTexto);
    //$statusValue = isset($estadoReverso[$status]) ? $estadoReverso[$status] : $status;
    //$sql .= " AND o.current_state = '$statusValue'";
    $sql .= " AND osl.name = '$status'";
}*/

// Filtro por estado
if ($status === "Aceptados y en proceso") {
    $sql .= " AND o.current_state IN (2, 3)";
} elseif (!empty($status) && $status !== "Todos") {
    $sql .= " AND osl.name = ?";
    $params[] = $status;
    $types .= "s";
}
/*if (!empty($status)) {
    if ($status === "Aceptados y en proceso") {
        // Mostrar solo "Pago aceptado" (2) y "Preparación en proceso" (3)
        $sql .= " AND o.current_state IN (2, 3)";
    } else {
        $sql .= " AND osl.name = ?";
        $params[] = $status;
        $types .= "s";
    }
} else {
    // Si no se selecciona ningún estado, también mostrar por defecto los pendientes
    $sql .= " AND o.current_state IN (2, 3)";
}*/

$sql .= " GROUP BY o.id_order ORDER BY o.date_add DESC";



// Ejecutar consulta
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

//$result = $conn->query($sql);


//Preparar Respuesta
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'OrderId' => $row['OrderId'],
        'DateOrder' => $row['DateOrder'],
        'CustomerFullname' => $row['CustomerFullname'],
        'CustomerAdress' => $row['CustomerAdress'],
        'OrderCountry' => $row['OrderCountry'],
        'ProductsOrdered' => $row['ProductsOrdered'],
        'OrderStatus' => $row['OrderStatus']
    ];
}

/*while ($row = $result->fetch_assoc()) {
    $estado = $row['OrderStatus'];
    $row['OrderStatus'] = isset($estadoTexto[$estado]) ? $estadoTexto[$estado] : $estado;
    $orders[] = $row;
}*/

header('Content-Type: application/json');
echo json_encode($orders);
?>