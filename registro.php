<?php
require_once 'conexion.php';
session_start();
header('Content-Type: application/json');

// ======================
// 🔹 Captura y validación básica
// ======================
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$pass = trim($_POST['contraseña'] ?? '');

if (empty($username) || empty($email) || empty($pass)) {
    echo json_encode(["ok" => false, "error" => "Faltan campos obligatorios."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["ok" => false, "error" => "El correo electrónico no es válido."]);
    exit;
}

// ======================
// 🔹 Filtro de malas palabras en username
// ======================
$palabrotas = [
    "PUTA","PUTO","MOGOLICO","CULO","CONCHA","VERGA","CHOTA",
    "GARCHA","PIJA","MALPARIDO","BOLUDO","PITO",
    "IDIOTA","BOBO","PELOTUDO","TROLA","TORLITA","PETERA",
    "ZORRA","PENE","PENDEJO","MARICON","MARICA","GIL","COGER","TETA","ZORRITA","PUTITA",
    "FOLLAR","COJER","PELOTUDO","GIL","TARADO","ESTUPIDO","IMBECIL","PELOTUDO","IDIOTA","CAGON",
    "PENE","VAGINA"
];
$username_upper = strtoupper($username);

foreach ($palabrotas as $mala) {
    if (str_contains($username_upper, $mala)) {
        echo json_encode(["ok" => false, "error" => "Tu nombre de usuario contiene palabras inapropiadas."]);
        exit;
    }
}

// ======================
// 🔹 Verificar si ya existe usuario o email
// ======================
$sql = "SELECT id FROM usuarios WHERE username = ? OR email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    echo json_encode(["ok" => false, "error" => "El usuario o el email ya están registrados."]);
    $stmt->close();
    exit;
}
$stmt->close();

// ======================
// 🔹 Insertar nuevo usuario
// ======================
$hashed = password_hash($pass, PASSWORD_DEFAULT);

$insert = $conn->prepare("INSERT INTO usuarios (username, contraseña, email) VALUES (?, ?, ?)");
if (!$insert) {
    echo json_encode(["ok" => false, "error" => "Error interno al preparar la consulta."]);
    exit;
}

$insert->bind_param("sss", $username, $hashed, $email);
$ok = $insert->execute();

if ($ok) {
    $_SESSION['id'] = $insert->insert_id;
    echo json_encode(["ok" => true, "msg" => "Registro exitoso."]);
} else {
    echo json_encode(["ok" => false, "error" => "Error al registrar el usuario."]);
}
$insert->close();
?>
