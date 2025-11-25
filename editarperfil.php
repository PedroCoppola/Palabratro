<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}

$id_usuario = $_SESSION['id'];
$mensaje = "";

// === Obtener datos del usuario logueado ===
$sql = "SELECT username, email, contrasena, pfp FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

$fotoPerfil = !empty($usuario['pfp']) ? "img/pfp/" . $usuario['pfp'] : "img/default.png";

// === Procesar formulario ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevo_email = trim($_POST["email"]);
    $pass_actual = $_POST["pass_actual"];
    $pass_nueva = $_POST["pass_nueva"];
    $pfp = $_FILES["pfp"];

    // Verificar contraseña actual
    if (!password_verify($pass_actual, $usuario["contrasena"])) {
        $mensaje = "❌ La contraseña actual no es correcta.";
    } else {
        $nueva_pass = $usuario["contrasena"];
        if (!empty($pass_nueva)) {
            $nueva_pass = password_hash($pass_nueva, PASSWORD_DEFAULT);
        }

        // Manejo de imagen
        $nombreArchivo = $usuario["pfp"]; // mantiene la actual si no se sube nueva
        if (!empty($pfp["name"])) {
            $carpetaDestino = "img/pfp/";
            $nombreArchivo = time() . "_" . basename($pfp["name"]);
            $rutaDestino = $carpetaDestino . $nombreArchivo;

            if (!move_uploaded_file($pfp["tmp_name"], $rutaDestino)) {
                $mensaje = "⚠️ Error al subir la imagen.";
            }
        }

        // Actualizar datos
        $update = "UPDATE usuarios SET email=?, contrasena=?, pfp=? WHERE id=?";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("sssi", $nuevo_email, $nueva_pass, $nombreArchivo, $id_usuario);

        if ($stmt2->execute()) {
            $mensaje = "✅ Perfil actualizado correctamente.";
            // Refrescar los datos
            $fotoPerfil = !empty($nombreArchivo) ? "img/pfp/" . $nombreArchivo : "img/default.png";
        } else {
            $mensaje = "❌ Error al actualizar el perfil.";
        }
        $stmt2->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&display=swap');

:root {
    --azul: #0044aa;
    --rojo: #cc0000;
    --texto: #1a1a1a;
}

body {
    font-family: 'Gloria Hallelujah', cursive;
    background: url("img/fondo.jpg") no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    min-height: 100vh;

    display: flex;
    justify-content: center;
    align-items: center;
}

/* === CONTENEDOR PRINCIPAL === */
.perfil-container {
    background: url("img/hoja.jpg") no-repeat center center;
    background-size: cover;
    
    width: 700px;
    padding: 26px 34px;
    
    border: 3px dashed var(--azul);
    border-radius: 16px;

    box-shadow: 6px 6px 0 #000;
    transform: rotate(-1deg);

    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;

    text-shadow: 1px 1px 0 #fff, -1px -1px 0 #000;
}

/* === TÍTULO === */
.perfil-container h2 {
    color: var(--azul);
    margin: 4px 0 10px;
    font-size: 28px;
    text-decoration: underline;
}

/* === MENSAJES === */
.mensaje {
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: bold;
    border: 2px dashed #000;
    box-shadow: 2px 2px 0 #000;
    background: rgba(255,255,255,0.85);
}

/* === PREVIEW FOTO === */
.foto-preview img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px dashed var(--azul);
    box-shadow: 3px 3px 0 #000;
    object-fit: cover;
}

/* === FORMULARIO === */
form {
    width: 80%;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

label {
    color: var(--azul);
    font-size: 18px;
}

input[type="text"],
input[type="email"],
input[type="password"],
input[type="file"] {
    padding: 8px 10px;
    font-size: 16px;
    border: 2px dashed var(--azul);
    border-radius: 8px;
    background: rgba(255,255,255,0.85);
    box-shadow: 2px 2px 0 #000;
    font-family: 'Gloria Hallelujah', cursive;
}

/* === BOTÓN GUARDAR === */
button[type="submit"] {
    background: var(--azul);
    color: #fff;
    font-size: 18px;
    padding: 10px 16px;

    border: none;
    border-radius: 12px;
    cursor: pointer;

    box-shadow: 3px 3px 0 #000;
    transform: rotate(-1deg);
    transition: 0.2s;
    font-family: 'Gloria Hallelujah', cursive;
}

button[type="submit"]:hover {
    transform: rotate(1deg) scale(1.05);
    box-shadow: 6px 6px 0 #000;
}

/* === BOTÓN VOLVER === */
.btn-volver {
    display: inline-block;
    margin-top: 12px;
    padding: 12px 20px;

    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
    text-align: center;

    color: var(--azul);
    background-color: #cce5ff;

    border: 3px dashed var(--azul);
    border-radius: 12px;

    box-shadow: 3px 3px 0 #000;
    transform: rotate(-1deg);
    transition: 0.2s;
}

.btn-volver:hover {
    transform: rotate(1deg) scale(1.05);
    box-shadow: 6px 6px 0 #000;
    background-color: #e8f4ff;
}

/* === RESPONSIVE === */
@media (max-width: 480px) {
    .perfil-container {
        width: 90%;
        padding: 20px;
    }

    .foto-preview img {
        width: 90px;
        height: 90px;
    }

    form {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <div class="perfil-container">
        <h2>Editar Perfil</h2>

        <?php if ($mensaje): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <div class="foto-preview">
            <img src="<?php echo htmlspecialchars(!empty($fotoPerfil) ? $fotoPerfil : 'img/default.jpg'); ?>" alt="Foto de perfil">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <label>Usuario</label>
            <input type="text" value="<?php echo htmlspecialchars($usuario['username']); ?>" disabled>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

            <label>Foto de perfil</label>
            <input type="file" name="pfp">

            <label>Contraseña actual</label>
            <input type="password" name="pass_actual" required>

            <label>Nueva contraseña (opcional)</label>
            <input type="password" name="pass_nueva">

            <button type="submit">Guardar cambios</button>
        </form>

        <a href="index.php" class="btn-volver">⬅ Volver</a>
    </div>
</body>
</html>
