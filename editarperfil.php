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
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .perfil-container {
            width: 350px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        .mensaje {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            color: green;
        }
        .foto-preview {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        .foto-preview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input[type="email"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .btn-volver {
            display: inline-block;
            text-align: center;
            background: #555;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            width: 100%;
            margin-top: 10px;
        }
        .btn-volver:hover {
            background: #333;
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
            <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de perfil">
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
