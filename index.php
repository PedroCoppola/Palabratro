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

    // Traer datos del usuario logueado
    $sql = "SELECT username, puntaje, monedas, racha_actual, mejor_racha, fecha_creacion, pfp FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    // Definir la ruta de la PFP con fallback
    $pfp_ruta = !empty($usuario['pfp']) ? $usuario['pfp'] : 'img/default.jpg';
    ?>
    
    <div class="tarjeta usuario">
        <img src="<?php echo htmlspecialchars($pfp_ruta); ?>" alt="Foto de perfil" class="user-pfp">
        <h2 id="usuario"><?php echo htmlspecialchars($usuario['username']); ?></h2>
        <p>Puntaje: <span id="puntaje"><?php echo number_format($usuario['puntaje']); ?></span></p>
    </div>

    <div class="info">
        <h2>Estadísticas</h2>
        <p><strong>Monedas:</strong> <?php echo number_format($usuario['monedas']); ?></p>
<p><strong>Racha actual:</strong> 
    <?php 
        $rachaActual = (int)$usuario['racha_actual'];
        echo $rachaActual . ' ' . ($rachaActual === 1 ? 'partida' : 'partidas'); 
    ?>
</p>

<p><strong>Mejor racha:</strong> 
    <?php 
        $mejorRacha = (int)$usuario['mejor_racha'];
        echo $mejorRacha . ' ' . ($mejorRacha === 1 ? 'partida' : 'partidas'); 
    ?>
</p>

        <p><strong>Fecha de creación:</strong> <?php echo htmlspecialchars($usuario['fecha_creacion']); ?></p>
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
