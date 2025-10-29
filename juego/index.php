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
</head>

<body>
    <!-- TARJETA DE ESTADO DE SESIÓN (SUPERIOR IZQUIERDA) -->
           <a href="../index.php" class="btn-volver">⬅ Volver</a>

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

<div id="contenedor-pistas-compradas">
  <h3>🎯 Pistas compradas esta ronda</h3>
</div>



    <button id="btn-tienda">🧩</button>

<div id="tienda-modal">
  <span class="close-btn">&times;</span>
  <h3>🧩 Tienda de Pistas</h3>
  <p>Usá tus monedas para conseguir ventajas.</p>

  <div id="lista-pistas">
    <!-- acá van las pistas desde PHP -->
    <?php
    require_once '../conexion.php';
    $pistas = $conn->query("SELECT * FROM pistas WHERE activa = 1 ORDER BY valor_monedas ASC");
    if ($pistas->num_rows > 0):
      while ($p = $pistas->fetch_assoc()): ?>
        <div class="pista-item" data-id="<?= $p['id'] ?>" data-columna="<?= $p['columna_referencia'] ?>" data-precio="<?= $p['valor_monedas'] ?>">
          <h4><?= $p['icono_simbolo'] ?> <?= htmlspecialchars($p['nombre']) ?></h4>
          <p><?= htmlspecialchars($p['descripcion_corta']) ?></p>
          <button class="btn-comprar">Comprar por <?= $p['valor_monedas'] ?> 🪙</button>
        </div>
      <?php endwhile;
    else: ?>
      <p>No hay pistas disponibles por ahora 😕</p>
    <?php endif; ?>
  </div>
</div>


    <script>
  // ===========================
  // 📘 MODAL DE AYUDA
  // ===========================
  const btnAyuda = document.getElementById('btn-ayuda');
  const ayudaModal = document.getElementById('ayuda-modal');
  const closeBtn = ayudaModal.querySelector('.close-btn');

  btnAyuda.addEventListener('click', () => {
    ayudaModal.style.display = 'block';
  });

  closeBtn.addEventListener('click', () => {
    ayudaModal.style.display = 'none';
  });

  // ===========================
  // 💰 MODAL DE TIENDA
  // ===========================
  const btnTienda = document.getElementById('btn-tienda');
  const modalTienda = document.getElementById('tienda-modal');
  const closeTienda = modalTienda.querySelector('.close-btn');

  btnTienda.addEventListener('click', () => modalTienda.classList.add('show'));
  closeTienda.addEventListener('click', () => modalTienda.classList.remove('show'));

  // ===========================
  // 🌎 VARIABLE GLOBAL DE USUARIO
  // ===========================
  const ID_USUARIO_ACTUAL = <?php echo $usuario_logueado ? (int)$id_usuario : 'null'; ?>;

  // ===========================
  // 🧩 COMPRA DE PISTAS
  // ===========================
  document.querySelectorAll('.btn-comprar').forEach(btn => {
    btn.addEventListener('click', async () => {
      const item = btn.closest('.pista-item');
      const pistaId = item.dataset.id;
      const precio = item.dataset.precio;
      const nombre = item.querySelector('h4').innerText;

      if (!window.ID_PALABRA_ACTUAL) {
        alert("⚠️ No se encontró la palabra actual.");
        return;
      }

      if (!confirm(`¿Comprar "${nombre}" por ${precio} monedas?`)) return;

      const body = new URLSearchParams();
      body.append("pista_id", pistaId);
      body.append("id_palabra", window.ID_PALABRA_ACTUAL);

      try {
        const res = await fetch("comprar_pista.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: body.toString()
        });

        const data = await res.json();

        if (!data.ok) {
          alert(`⚠️ ${data.error}`);
          return;
        }

        // Actualizar monedas
        const monedasEl = document.getElementById("monedas");
        if (monedasEl) monedasEl.innerText = data.nuevas_monedas;

        // Crear card visual de pista
        const cont = document.getElementById("contenedor-pistas-compradas");
        const card = document.createElement("div");
        card.classList.add("pista-card");
        card.innerHTML = `
          <h4>${data.pista.icono || "💡"} ${data.pista.nombre}</h4>
          <hr class="linea">
          <p>${data.pista.descripcion}</p>
          <p><strong>Resultado:</strong> ${data.valor_referencia ?? "(sin dato)"}</p>
        `;
        cont.appendChild(card);

        alert(`✅ ${data.msg}`);
      } catch (e) {
        console.error(e);
        alert("❌ Error al conectar con el servidor.");
      }
    });
  });
</script>

</body>

</html>
