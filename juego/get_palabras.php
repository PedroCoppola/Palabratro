<?php
include("../conexion.php");

// Traer id + palabra
$sql = "SELECT id, palabra FROM palabras";
$result = $conn->query($sql);

$palabras = [];
while ($row = $result->fetch_assoc()) {
    $palabras[] = [
        'id' => (int)$row['id'],
        'palabra' => $row['palabra']
    ];
}

header('Content-Type: application/json');
echo json_encode($palabras);
