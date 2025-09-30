<?php
// guardar_resultado.php
require_once '../conexion.php';
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    echo json_encode(["ok" => false, "error" => "payload missing"]);
    exit;
}

// Validación mínima
$idUsuario = isset($input['idUsuario']) ? (int)$input['idUsuario'] : null;
$palabra = isset($input['palabra']) ? strtoupper(trim($input['palabra'])) : null;
$adivinada = isset($input['adivinada']) ? (int)$input['adivinada'] : 0;
$intentos = isset($input['intentos']) ? (int)$input['intentos'] : null;
$pistas = isset($input['pistas_usadas']) ? $input['pistas_usadas'] : '';
$puntaje = isset($input['puntaje']) ? (int)$input['puntaje'] : 0;
$monedas = isset($input['monedas']) ? (int)$input['monedas'] : 0;

// Autorización: que el session id coincida
if (!isset($_SESSION['id']) || $_SESSION['id'] != $idUsuario) {
    echo json_encode(["ok" => false, "error" => "No autorizado"]);
    exit;
}

// BUSCAR/CREAR palabra (si existe la tabla `palabras`)
$id_palabra = null;
$check = $conn->prepare("SELECT id FROM palabras WHERE palabra = ? LIMIT 1");
if ($check) {
    $check->bind_param("s", $palabra);
    $check->execute();
    $res = $check->get_result();
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id_palabra = (int)$row['id'];
    } else {
        // intentar insertar (si la tabla tiene solo columna palabra mínima)
        $ins = $conn->prepare("INSERT INTO palabras (palabra) VALUES (?)");
        if ($ins) {
            $ins->bind_param("s", $palabra);
            $okIns = $ins->execute();
            if ($okIns) $id_palabra = $conn->insert_id;
            $ins->close();
        }
    }
    $check->close();
}
// Si no existe la tabla o falló, $id_palabra queda null (es aceptable por FK ON DELETE SET NULL).

// Insertar partida
if ($id_palabra !== null) {
    $sql = "INSERT INTO partidas (id_usuario, id_palabra, adivinada, intentos, pistas_usadas) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["ok" => false, "error" => "Error prepare partidas"]);
        exit;
    }
    $stmt->bind_param("iiiss", $idUsuario, $id_palabra, $adivinada, $intentos, $pistas);
    $okPartida = $stmt->execute();
    $stmt->close();
} else {
    // insertar sin id_palabra
    $sql = "INSERT INTO partidas (id_usuario, id_palabra, adivinada, intentos, pistas_usadas) VALUES (?, NULL, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["ok" => false, "error" => "Error prepare partidas no-palabra"]);
        exit;
    }
    $stmt->bind_param("iiis", $idUsuario, $adivinada, $intentos, $pistas);
    $okPartida = $stmt->execute();
    $stmt->close();
}

// Actualizar usuario: sumar puntaje y monedas
$okUpdate = false;
$up = $conn->prepare("UPDATE usuarios SET puntaje = puntaje + ?, monedas = monedas + ? WHERE id = ?");
if ($up) {
    $up->bind_param("iii", $puntaje, $monedas, $idUsuario);
    $okUpdate = $up->execute();
    $up->close();
}

// Obtener totales nuevos para devolver
$newTotals = null;
$sel = $conn->prepare("SELECT puntaje, monedas FROM usuarios WHERE id = ? LIMIT 1");
if ($sel) {
    $sel->bind_param("i", $idUsuario);
    $sel->execute();
    $r = $sel->get_result();
    if ($r && $r->num_rows) {
        $newTotals = $r->fetch_assoc();
    }
    $sel->close();
}

echo json_encode([
    "ok" => ($okPartida && $okUpdate),
    "nuevo_puntaje" => $newTotals ? (int)$newTotals['puntaje'] : null,
    "nuevas_monedas" => $newTotals ? (int)$newTotals['monedas'] : null
]);
 