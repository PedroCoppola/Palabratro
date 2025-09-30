<?php
// NOTA IMPORTANTE: Si este archivo (index.php) está dentro de una carpeta (ej: /juego/), 
// la ruta correcta para la conexión debe ser require_once '../conexion.php';
// Si el archivo está en la raíz, debe ser require_once 'conexion.php';
// Para funcionar correctamente, vamos a descomentar la línea de conexión.
require_once '../conexion.php'; 
session_start(); // Asegúrate de iniciar la sesión

$usuario_logueado = false;
$usuario = null;
$id_usuario = null; // Inicializamos para que la constante de JS no tenga problemas

if (isset($_SESSION['id'])) {
    $id_usuario = $_SESSION['id'];
    
    // =================================================================
    // LÓGICA REAL DE BASE DE DATOS
    // =================================================================
    
    // Traer datos del usuario logueado con sus estadísticas
    $sql = "SELECT username, puntaje, monedas, racha_actual, mejor_racha, fecha_creacion, pfp
            FROM usuarios 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        
        // Si encontramos un usuario, está logueado
        $usuario_logueado = $usuario !== null;
    }

    
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo.css">
    <!-- Incluimos la fuente del juego para que los estilos sean sutiles y armónicos -->
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&display=swap" rel="stylesheet">


    <title>Palabratro</title>
    <!-- PLEASE NO CHANGES BELOW THIS LINE (UNTIL I SAY SO) -->
    <script language="javascript" type="text/javascript" src="libraries/p5.min.js"></script>
    <script language="javascript" type="text/javascript" src="palabratro.js"></script>
    <!-- OK, YOU CAN MAKE CHANGES BELOW THIS LINE AGAIN -->
    <style>
        /* CSS para el botón de ayuda y el modal (copiado de la respuesta anterior, simplificado) */

    </style>
</head>

<body>
    <!-- TARJETA DE ESTADO DE SESIÓN (SUPERIOR IZQUIERDA) -->
    <div class="tarjeta-sesion">
        <?php if ($usuario_logueado): ?>
            <!-- Mostrar datos del usuario -->
<div class="tarjeta usuario">
    <h2 id="usuario"><?php echo htmlspecialchars($usuario['username']); ?></h2>

    <b><p>Puntaje:</b> <span id="puntaje"><?php echo number_format($usuario['puntaje']); ?></span></p>
    <b><p>Monedas:</b> <span id="monedas"><?php echo number_format($usuario['monedas']); ?></span></p>

    <b><p>Racha actual:</b> 
        <strong><span id="racha_actual">
            <?php echo (int)$usuario['racha_actual']; ?>
        </span> partidas</strong>
    </p>

    <b><p>Mejor racha:</b> 
        <strong><span id="mejor_racha">
            <?php echo (int)$usuario['mejor_racha']; ?>
        </span> partidas</strong>
    </p>
</div>

        <?php else: ?>
            <!-- Mostrar botón de Login -->
            <h2>¡A Jugar!</h2>
            <p>Inicia sesión para guardar tu progreso, puntajes y monedas.</p>
            <a href="../login.php" class="btn-login">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
    
    
    <div id="game-container"></div> <!-- aquí se dibuja el canvas -->
    
<div id="overlay">
  <div class="overlay-box">
    <h2 id="overlay-titulo"></h2>
    <p id="overlay-mensaje"></p>
    <button id="btn-restart"><span>Jugar de nuevo</span></button>
  </div>
</div>


    <!-- Botón de ayuda y Modal -->
    <button id="btn-ayuda">?</button>
    <div id="ayuda-modal">
        <span class="close-btn">&times;</span>
        <h3>¿Cómo se juega?</h3>
        <p>Debes adivinar una palabra de 5 letras en un máximo de 6 intentos.</p>
        <p>En cada intento, las letras de la palabra ingresada cambiarán de color para darte pistas:</p>
        <ul>
            <li><span style="color: #4CAF50; font-weight: bold;">Verde</span> (Celeste en tu código p5) significa que la letra está en la palabra y en la posición correcta.</li>
            <li><span style="color: #FFC107; font-weight: bold;">Amarillo</span> significa que la letra está en la palabra pero en la posición incorrecta.</li>
            <li><span style="color: #9E9E9E; font-weight: bold;">Gris</span> es que la letra no se encuentra en la palabra.</li>
        </ul>
    </div>

    <script>
        // Lógica del modal de ayuda (la misma que ya tenías)
        const btnAyuda = document.getElementById('btn-ayuda');
        const ayudaModal = document.getElementById('ayuda-modal');
        const closeBtn = ayudaModal.querySelector('.close-btn');

        btnAyuda.addEventListener('click', () => {
            ayudaModal.style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            ayudaModal.style.display = 'none';
        });
        
        // *****************************************************************
        // VARIABLE GLOBAL DE JS PARA SABER EL ID DEL USUARIO
        // ESTO ES CLAVE PARA GUARDAR LA PARTIDA CON AJAX/FETCH
        const ID_USUARIO_ACTUAL = <?php echo $usuario_logueado ? (int)$id_usuario : 'null'; ?>;
        // *****************************************************************
    </script>
</body>

</html>
