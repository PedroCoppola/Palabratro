<?php
require_once '../conexion.php';
session_start();

header('Content-Type: application/json');

$id_usuario = $_SESSION['id'] ?? null;
$palabra = strtoupper(trim($_POST['palabra'] ?? ''));

// Validación básica
if (empty($palabra) || strlen($palabra) !== 5 || !preg_match('/^[A-ZÑÁÉÍÓÚÜ]+$/u', $palabra)) {
    echo json_encode(["ok" => false, "msg" => "Formato inválido."]);
    exit;
}

// ¿ya existe en diccionario?
$stmt = $conn->prepare("SELECT palabra FROM palabras WHERE UPPER(palabra) = ?");
$stmt->bind_param("s", $palabra);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    echo json_encode(["ok" => false, "msg" => "Esa palabra ya existe en el diccionario."]);
    exit;
}
$stmt->close();

// ¿ya fue sugerida?
$stmt2 = $conn->prepare("SELECT id FROM sugerencias_palabras WHERE palabra = ?");
$stmt2->bind_param("s", $palabra);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->num_rows > 0) {
    echo json_encode(["ok" => false, "msg" => "Esa palabra ya fue sugerida antes."]);
    exit;
}
$stmt2->close();

// Insertar
$stmt3 = $conn->prepare("INSERT INTO sugerencias_palabras (palabra, id_usuario) VALUES (?, ?)");
$stmt3->bind_param("si", $palabra, $id_usuario);
$ok = $stmt3->execute();

echo json_encode([
    "ok" => $ok,
    "msg" => $ok ? "¡Gracias por tu sugerencia!" : "Error al guardar la sugerencia."
]);
