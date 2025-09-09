<?php
// Configura los datos de tu conexión
require_once "conexion.php";

// Obtener datos del formulario
$username = $_POST['username'];
$email = $_POST['email'];
$contraseña = $_POST['contraseña'];

// Validar que no estén vacíos
if (empty($username) || empty($email) || empty($contraseña)) {
    die("Todos los campos son obligatorios.");
}

// Verificar que no exista el usuario o email
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "El nombre de usuario o correo ya están registrados.";
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Hashear la contraseña
$contraseña_hash = password_hash($contraseña, PASSWORD_BCRYPT);

// Insertar nuevo usuario
$stmt = $conn->prepare("INSERT INTO usuarios (username, contraseña, email) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $contraseña_hash, $email);

if ($stmt->execute()) {
    echo "¡Cuenta creada con éxito! <a href='login.html'>Iniciar sesión</a>";
} else {
    echo "Error al crear la cuenta: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
