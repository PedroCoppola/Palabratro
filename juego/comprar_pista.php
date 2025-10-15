<?php
require_once '../conexion.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo json_encode(["ok" => false, "error" => "No estás logueado."]);
    exit;
}

$id_usuario = $_SESSION['id'];
$id_pista = isset($_POST['pista_id']) ? (int)$_POST['pista_id'] : 0;
$id_palabra = isset($_POST['id_palabra']) ? (int)$_POST['id_palabra'] : 0;

if (!$id_pista || !$id_palabra) {
    echo json_encode(["ok" => false, "error" => "Faltan datos."]);
    exit;
}

// === Traer pista ===
$stmt = $conn->prepare("SELECT * FROM pistas WHERE id = ? AND activa = 1");
$stmt->bind_param("i", $id_pista);
$stmt->execute();
$pista = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pista) {
    echo json_encode(["ok" => false, "error" => "Pista no encontrada o inactiva."]);
    exit;
}

// === Verificar monedas del usuario ===
$stmt = $conn->prepare("SELECT monedas FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    echo json_encode(["ok" => false, "error" => "Usuario no encontrado."]);
    exit;
}

$monedas_actuales = (int)$usuario['monedas'];
$precio = (int)$pista['valor_monedas'];

if ($monedas_actuales < $precio) {
    echo json_encode(["ok" => false, "error" => "No tenés suficientes monedas."]);
    exit;
}

// === Descontar monedas ===
$nuevasMonedas = $monedas_actuales - $precio;
$stmt = $conn->prepare("UPDATE usuarios SET monedas = ? WHERE id = ?");
$stmt->bind_param("ii", $nuevasMonedas, $id_usuario);
$stmt->execute();
$stmt->close();

// === Buscar valor real en la palabra ===
$valor_referencia = null;
$columna = $pista['columna_referencia'];

if ($columna) {
    $query = "SELECT `$columna` FROM palabras WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id_palabra);
        $stmt->execute();
        $res = $stmt->get_result();
        $fila = $res->fetch_assoc();
        if ($fila && isset($fila[$columna])) {
            $valor_referencia = $fila[$columna];
        }
        $stmt->close();
    }
}

// === Respuesta OK ===
echo json_encode([
    "ok" => true,
    "msg" => "Compraste la pista «{$pista['nombre']}» por {$precio} 🪙.",
    "nuevas_monedas" => $nuevasMonedas,
    "pista" => [
        "id" => (int)$pista['id'],
        "nombre" => $pista['nombre'],
        "descripcion" => $pista['descripcion_corta'],
        "columna" => $pista['columna_referencia'],
        "icono" => $pista['icono_simbolo'] ?? ''
    ],
    "valor_referencia" => $valor_referencia
]);
