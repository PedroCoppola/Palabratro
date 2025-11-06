<?php
require_once '../conexion.php';
session_start();

$id_usuario = $_SESSION['id'] ?? null;
$palabra = strtoupper(trim($_POST['palabra'] ?? ''));

// Validaciones básicas
if (empty($palabra) || strlen($palabra) !== 5 || !preg_match('/^[A-ZÑÁÉÍÓÚÜ]+$/u', $palabra)) {
    header("Location: ../index.php?msg=error_formato");
    exit;
}

// Verificar si ya está en el diccionario
$stmt = $conn->prepare("SELECT palabra FROM palabras WHERE UPPER(palabra) = ?");
$stmt->bind_param("s", $palabra);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    header("Location: index.php?msg=ya_existe");
    exit;
}
$stmt->close();

// Verificar si ya fue sugerida
$stmt2 = $conn->prepare("SELECT id FROM sugerencias_palabras WHERE palabra = ?");
$stmt2->bind_param("s", $palabra);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->num_rows > 0) {
    header("Location: ../index.php?msg=sugerida");
    exit;
}
$stmt2->close();

// Insertar la sugerencia
$stmt3 = $conn->prepare("INSERT INTO sugerencias_palabras (palabra, id_usuario) VALUES (?, ?)");
$stmt3->bind_param("si", $palabra, $id_usuario);
$ok = $stmt3->execute();

if ($ok) {
    header("Location: index.php?msg=ok");
} else {
    header("Location: index.php?msg=error_bd");
}
exit;
?>
