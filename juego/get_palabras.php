<?php
include("../conexion.php");

// Traer solo la columna "palabra"
$sql = "SELECT palabra FROM palabras";
$result = $conn->query($sql);

$palabras = [];
while ($row = $result->fetch_assoc()) {
    $palabras[] = $row['palabra'];
}

header('Content-Type: application/json');
echo json_encode($palabras);
