<?php
require_once 'conexion.php';
session_start();

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$pass = trim($_POST['contraseña'] ?? '');

if (empty($username) || empty($email) || empty($pass)) {
  die("Error: Faltan campos obligatorios.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die("Error: Correo electrónico no válido.");
}

// Comprobar si el usuario o correo ya existen
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ? LIMIT 1");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
  die("Error: Usuario o correo ya registrados.");
}
$stmt->close();

// Insertar usuario nuevo
$hash = password_hash($pass, PASSWORD_DEFAULT);
$insert = $conn->prepare("INSERT INTO usuarios (username, contrasena, email, monedas, puntaje) VALUES (?, ?, ?, 0, 0)");
$insert->bind_param("sss", $username, $hash, $email);

if ($insert->execute()) {
  $_SESSION['id'] = $conn->insert_id;
  header("Location: registro.html?ok=1");
  exit;
} else {
  die("Error: No se pudo crear la cuenta.");
}
?>
