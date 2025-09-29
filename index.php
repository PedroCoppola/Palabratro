<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Palabrato</title>
  <link rel="stylesheet" href="css/stylo.css">
</head>
<body>

<div class="layout">

  <div class="hoja">
    <?php
    if (isset($_SESSION['id'])) {
        $id_usuario = $_SESSION['id'];

        // Traer datos del usuario logueado (ahora sí incluimos fecha_creacion)
        $sql = "SELECT username, puntaje, monedas, racha, fecha_creacion FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        ?>

        <div class="tarjeta usuario">
          <h2 id="usuario"><?php echo $usuario['username']; ?></h2>
          <p>Puntaje: <span id="puntaje"><?php echo $usuario['puntaje']; ?></span></p>
        </div>
        <div class="info">
          <h2>Estadísticas</h2>
          <p><strong>Monedas:</strong> <?php echo $usuario['monedas']; ?></p>
          <p><strong>Racha:</strong> <?php echo $usuario['racha']; ?></p>
          <p><strong>Fecha de creación:</strong> <?php echo $usuario['fecha_creacion']; ?></p>
        </div>

        <?php
    } else {
        echo '<p class="no-session">Che, iniciá sesión <a href="login.html">acá</a></p>';
    }
    ?>
  </div>

  <div class="container">
    <img src="img/logo_trans.png" alt="Logo de Palabrato" class="logo" width="400px">
    <div class="btns">
      <button class="btn carta btn-jugar" onclick="location.href='juego/index.php'">🎮 JUGAR</button>
      <button class="btn carta btn-ranking" onclick="location.href='ranking.php'">🏆 RANKING</button>
    </div>
  </div>

</div>

</body>
</html>
