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
    
    // Traer datos del usuario logueado
    $sql = "SELECT username, puntaje, monedas, racha, fecha_creacion FROM usuarios WHERE id = ?";
    
    // Usamos $conn que debería venir del require_once anterior
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close(); // Cerramos el statement
        
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
        #btn-ayuda {
            position: fixed; bottom: 20px; left: 20px; width: 40px; height: 40px;
            background-color: #003388; color: white; border: none; border-radius: 50%;
            font-size: 24px; cursor: pointer; display: flex; justify-content: center; align-items: center; z-index: 1000;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            font-family: Arial, sans-serif;
        }
        #ayuda-modal {
            position: fixed; top: 50%; left: 50px; transform: translateY(-50%); width: 300px;
            background-color: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1001; display: none;
            font-family: 'Gloria Hallelujah', cursive;
        }

        /* ESTILOS CORREGIDOS Y SIMPLIFICADOS PARA LA TARJETA DE SESIÓN */
        .tarjeta-sesion {
            position: fixed; /* ¡La clave para que no rompa el layout! */
            top: 20px;
            left: 20px;
            width: 250px; /* Más sutil */
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.95); /* Fondo casi blanco semi-transparente */
            border-radius: 10px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
            font-family: 'Gloria Hallelujah', cursive; 
            color: #000;
            z-index: 50;
            box-sizing: border-box;
            border: 2px solid #003388; /* Borde azul oscuro del juego */
        }
        
        .tarjeta-sesion h2 {
            font-size: 1.2rem;
            margin-top: 0;
            color: #003388; /* Color del texto principal */
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 5px;
        }
        .tarjeta-sesion p {
            font-size: 0.9rem;
            margin: 5px 0;
            text-align: left;
        }
        .tarjeta-sesion .btn-login {
            display: block;
            margin-top: 15px;
            padding: 10px;
            text-align: center;
            background-color: #cce5ff;
            color: #003388;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: 2px solid #003388;
            box-shadow: 2px 2px 0 #000;
        }

        /* Quitamos el .hoja estático que no querías */
        .hoja {
            /* Mantenemos este estilo por si lo usa el div flotante de la derecha */
            border-radius: 5px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%) rotate(-1deg);
            width: 410px;
            min-height: 500px;
            padding: 36px 26px;
            background: url("../img/hoja.jpg") no-repeat center center;
            background-size: cover;
            border: 2px dashed var(--azul);
            box-shadow: 6px 6px 20px rgba(0, 0, 0, 0.5);
            font-family: 'Gloria Hallelujah', cursive;
            color: #000;
            z-index: 6;
            box-sizing: border-box;
            margin-left: 40px;
        }
    </style>
</head>

<body>
    <!-- TARJETA DE ESTADO DE SESIÓN (SUPERIOR IZQUIERDA) -->
    <div class="tarjeta-sesion">
        <?php if ($usuario_logueado): ?>
            <!-- Mostrar datos del usuario -->
            <div class="tarjeta usuario">
                <h2 id="usuario"><?php echo htmlspecialchars($usuario['username']); ?></h2>
                <p>Puntaje: <span id="puntaje"><?php echo number_format($usuario['puntaje']); ?></span></p>
                <p>Monedas: <span id="monedas"><?php echo number_format($usuario['monedas']); ?></span></p>
            </div>
            <div class="info">
                <p>Racha: <strong><span id="racha"><?php echo (int)$usuario['racha']; ?> días</span></strong></p>
                <p>Miembro desde: <?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?></p>
            </div>
        <?php else: ?>
            <!-- Mostrar botón de Login -->
            <h2>¡A Jugar!</h2>
            <p>Inicia sesión para guardar tu progreso, puntajes y monedas.</p>
            <a href="../login.php" class="btn-login">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
    
    
    <div id="game-container"></div> <!-- aquí se dibuja el canvas -->
    
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
