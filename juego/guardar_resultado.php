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
        $ins = $conn->prepare("INSERT INTO palabras (palabra) VALUES (?)");
        if ($ins) {
            $ins->bind_param("s", $palabra);
            if ($ins->execute()) {
                $id_palabra = $conn->insert_id;
            }
            $ins->close();
        }
    }
    $check->close();
}

// Insertar partida
if ($id_palabra !== null) {
    $sql = "INSERT INTO partidas (id_usuario, id_palabra, adivinada, intentos, pistas_usadas) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $idUsuario, $id_palabra, $adivinada, $intentos, $pistas);
    $okPartida = $stmt->execute();
    $stmt->close();
} else {
    $sql = "INSERT INTO partidas (id_usuario, id_palabra, adivinada, intentos, pistas_usadas) VALUES (?, NULL, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $idUsuario, $adivinada, $intentos, $pistas);
    $okPartida = $stmt->execute();
    $stmt->close();
}

// ======================
// LÓGICA DE RACHA
// ======================
if ($adivinada) {
    // Ganó
    $sql = "UPDATE usuarios 
            SET racha = 1,
                racha_actual = racha_actual + 1,
                mejor_racha = GREATEST(mejor_racha, racha_actual + 0),
                puntaje = puntaje + ?,
                monedas = monedas + ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $puntaje, $monedas, $idUsuario);
    $okUpdate = $stmt->execute();
    $stmt->close();
} else {
    // Perdió
    $sql = "UPDATE usuarios 
            SET racha = 0,
                racha_actual = 0,
                puntaje = puntaje + ?,
                monedas = monedas + ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $puntaje, $monedas, $idUsuario);
    $okUpdate = $stmt->execute();
    $stmt->close();
}

// Obtener totales nuevos
$newTotals = null;
$sel = $conn->prepare("SELECT puntaje, monedas, racha, racha_actual, mejor_racha FROM usuarios WHERE id = ? LIMIT 1");
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
    "nuevas_monedas" => $newTotals ? (int)$newTotals['monedas'] : null,
    "racha" => $newTotals ? (int)$newTotals['racha'] : null,
    "racha_actual" => $newTotals ? (int)$newTotals['racha_actual'] : null,
    "mejor_racha" => $newTotals ? (int)$newTotals['mejor_racha'] : null
]);
