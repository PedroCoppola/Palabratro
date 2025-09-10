<?php
session_start();

// Configura tus datos de conexión
$host = "localhost";
$db = "palabrato";
$user = "root";
$pass = ""; // o tu contraseña si la tienes

$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
    
?>