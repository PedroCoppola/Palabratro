<?php
session_start();

// Configura tus datos de conexión
$host = "localhost";
$db = "palabrato";
$user = "root";
$pass = ""; // o tu contraseña si la tienes

// Conectar a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$username = $_POST['username'];
$contraseña = $_POST['contraseña'];

// Evitar inyecciones SQL
$username = $conn->real_escape_string($username);
$contraseña = $conn->real_escape_string($contraseña);

// Buscar usuario en la base de datos
$sql = "SELECT * FROM usuarios WHERE username = '$username' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    // Verifica la contraseña (texto plano, no recomendado en producción)
    if ($contraseña === $usuario['contraseña']) {
        $_SESSION['usuario'] = $usuario['username'];
        $_SESSION['id'] = $usuario['id'];
        header("Location: bienvenido.php");
        exit;
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Usuario no encontrado.";
}

$conn->close();
?>
