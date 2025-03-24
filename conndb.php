<?php
// conndb.php
$host = 'localhost';
$user = 'freshlytest';
$pass = '';
$dbname = 'freshlytest';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>