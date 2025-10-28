<?php
$host = "mysql-lucas12.alwaysdata.net"; // Servidor
$usuario = "lucas12";                    // Usuario de la BD
$clave = "Pepe123";                // Contraseña de la BD
$bd = "lucas12_palabrato";          // Nombre de la base (reemplazá esto)

// Crear conexión
$conn = new mysqli($host, $usuario, $clave, $bd);
// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>