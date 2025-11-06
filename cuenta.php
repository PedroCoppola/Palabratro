<?php
session_start();
require 'conexion.php';

$usuario = null;

// Determinar qué perfil mostrar
if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']); // desde el ranking
} elseif (isset($_SESSION['id'])) {
    $id_usuario = $_SESSION['id']; // perfil propio
} else {
    header("Location: login.html");
    exit;
}

// Consultar datos del usuario
$query = "SELECT username, email, monedas, puntaje, pfp, fecha_creacion, racha_actual, mejor_racha 
          FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    die("⚠️ Usuario no encontrado.");
}

$stmt->close();
$conn->close();

// Definir ruta de imagen de perfil (con fallback)
$pfp_ruta = (!empty($usuario['pfp']) && file_exists($usuario['pfp'])) 
    ? htmlspecialchars($usuario['pfp']) 
    : 'img/default.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($usuario['username']); ?></title>
    <link rel="stylesheet" href="css/cuenta.css">
</head>
<body>

<div class="tarjeta usuario">
    <!-- Imagen de perfil -->
    <img src="<?php echo $pfp_ruta; ?>" alt="Foto de perfil" class="pfp">

    <!-- Nombre de usuario -->
    <h2 id="usuario"><?php echo htmlspecialchars($usuario['username']); ?></h2>

    <!-- Puntaje -->
    <p class="puntaje"><b>Puntos:</b> <?php echo number_format($usuario['puntaje'], 0, ',', '.'); ?></p>

    <!-- Info del usuario -->
    <div class="info-cajas">
        <div class="caja ancho-completo">
            <p><b>Email:</b></p>
            <span><?php echo htmlspecialchars($usuario['email']); ?></span>
        </div>

        <div class="caja">
            <p><b>Monedas:</b></p>
            <span><?php echo number_format($usuario['monedas']); ?></span>
        </div>

        <div class="caja">
            <p><b>Racha actual:</b></p>
            <span id="racha_actual"><?php echo (int)$usuario['racha_actual']; ?> partidas</span>
        </div>

        <div class="caja">
            <p><b>Mejor racha:</b></p>
            <span id="mejor_racha"><?php echo (int)$usuario['mejor_racha']; ?> partidas</span>
        </div>

        <div class="caja ancho-completo">
            <p><b>Fecha de creación:</b></p>
            <span><?php echo htmlspecialchars($usuario['fecha_creacion']); ?></span>
        </div>
    </div>

    <!-- Botón volver -->
    <?php if (isset($_GET['id'])): ?>
        <a href="ranking.php" class="btn-volver">⬅ Volver al ranking</a>
    <?php else: ?>
        <a href="index.php" class="btn-volver">⬅ Volver al inicio</a>
    <?php endif; ?>
</div>

</body>
</html>
