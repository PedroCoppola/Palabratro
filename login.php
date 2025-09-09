<?php
session_start(); // no te olvides de iniciar la sesión

require_once "conexion.php";


// Obtener datos del formulario
$username = $_POST['username'];
$contraseña = $_POST['contraseña'];

// Evitar inyecciones SQL con consultas preparadas (más seguro que real_escape_string)
$stmt = $conn->prepare("SELECT id, username, contraseña FROM usuarios WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    // Verifica la contraseña usando password_verify
    if (password_verify($contraseña, $usuario['contraseña'])) {
        // Si la contraseña es correcta
        $_SESSION['usuario'] = $usuario['username'];
        $_SESSION['id'] = $usuario['id'];

        header("Location: index.php");
        exit;
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Usuario no encontrado.";
}

$stmt->close();
$conn->close();
?>
