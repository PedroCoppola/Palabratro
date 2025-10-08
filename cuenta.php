<?php
session_start(); // Asegúrate de iniciar la sesión
include 'conexion.php';

$usuario = null;

// Verificamos si hay un usuario logueado mediante su ID
if (isset($_SESSION['id'])) {
    $id_usuario = $_SESSION['id'];

    $query = "SELECT username, email, monedas, pfp, fecha_creacion, racha, racha_actual, mejor_racha 
              FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="css/cuenta.css">
</head>
<body>

<div class="tarjeta usuario">
    <img src="<?php echo htmlspecialchars($usuario['pfp']); ?>" alt="Foto de perfil" class="pfp">

    <h2 id="usuario"><?php echo htmlspecialchars($usuario['username']); ?></h2>

    <div class="info-cajas">
        <div class="caja ancho-completo">
            <p><b>Email:</b></p>
            <span><?php echo htmlspecialchars($usuario['email']); ?></span>
        </div>
      <div class="caja">
    <p>
        <b>Monedas:</b>
       
    </p>
    <span id="monedas"><?php echo number_format($usuario['monedas']); ?>  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="gold" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-moneda">
            <circle cx="12" cy="12" r="10"/>
            <text x="12" y="16" text-anchor="middle" font-size="12" fill="orange" font-family="Arial" font-weight="bold">$</text>
        </svg></span>
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

    <a href="index.php" class="btn-volver">Volver al inicio</a>
</div>


</body>
</html>
