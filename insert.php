
<?php
include 'conndb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['date'];
    $nombre = $_POST['name'];
    $apellido = $_POST['lastname'];
    $direccion = $_POST['address'];
    //$ciudad = $_POST['city'];
    // Extraer ciudad desde la coma (si existe)
    $partes = explode(',', $direccion);
    $ciudad = isset($partes[1]) ? trim($partes[1]) : 'Sin ciudad';

    $pais_nombre = $_POST['country'];
    $productos = explode(',', $_POST['products']); // ejemplo: "Producto 1 x2, Producto 2 x1"
    $estado_nombre = $_POST['status'];

    // Obtener o insertar país
    $pais_sql = "SELECT id_country FROM country_lang WHERE name = '$pais_nombre'";
    $pais_result = $conn->query($pais_sql);
    if ($pais_result->num_rows > 0) {
        $row = $pais_result->fetch_assoc();
        $id_country = $row['id_country'];
    } else {
        if (!$conn->query("INSERT INTO country_lang (name) VALUES ('$pais_nombre')")) {
            echo json_encode(['error' => 'Error insertando país: ' . $conn->error]);
            exit;
        }
        $id_country = $conn->insert_id;
    }

    // Obtener el ID del estado desde su nombre
    $estado_sql = "SELECT id_order_state FROM order_state_lang WHERE name = '$estado_nombre' ";
    $estado_result = $conn->query($estado_sql);
    if ($estado_result->num_rows > 0) {
        $row = $estado_result->fetch_assoc();
        $estado_id = $row['id_order_state'];
    } else {
        $estado_id = 1; // Estado por defecto
    }

    // Insertar dirección
        //$conn->query("INSERT INTO address (id_customer, id_country, firstname, lastname, address1, address2, city) 
              //VALUES (1, $id_country, '$nombre', '$apellido', '$direccion', '', '')");
    if (!$conn->query("INSERT INTO address (id_customer, id_country, firstname, lastname, address1, address2, city) 
            VALUES (1, $id_country, '$nombre', '$apellido', '$direccion', '', '$ciudad')")) {
        echo json_encode(['error' => 'Error insertando dirección: ' . $conn->error]);
        exit;
    }
    $id_address = $conn->insert_id;

    // Insertar pedido
    //$conn->query("INSERT INTO orders (date_add, id_address_delivery, current_state) 
    //              VALUES ('$fecha', $id_address, '$estado_id')");

    if (!$conn->query("INSERT INTO orders (date_add, id_address_delivery, current_state) 
            VALUES ('$fecha', $id_address, $estado_id)")) {
        echo json_encode(['error' => 'Error insertando pedido: ' . $conn->error]);
        exit;
    }
    $id_order = $conn->insert_id;

    //echo "<script>console.log('Debug Objects: " . $conn . "' );</script>";

    // Insertar productos con cantidad ("Producto xCantidad" ,ejemplo: "Producto1 x2")
    foreach ($productos as $producto) {
        $producto = trim($producto);
        if (preg_match('/^(.*) x(\\d+)$/i', $producto, $matches)) {
            $nombre_producto = $matches[1];
            $cantidad = intval($matches[2]);
        } else {
            $nombre_producto = $producto;
            $cantidad = 1;
        }

        //$conn->query("INSERT INTO order_detail (id_order, product_name, product_quantity) 
        //              VALUES ($id_order, '$nombre_producto', $cantidad)");
        if (!$conn->query("INSERT INTO order_detail (id_order, product_name, product_quantity) 
                VALUES ($id_order, '$nombre_producto', $cantidad)")) {
            echo json_encode(['error' => 'Error insertando producto: ' . $conn->error]);
            exit;
        }
    }

    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>

