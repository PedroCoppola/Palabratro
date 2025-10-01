<?php
session_start();
include("conexion.php");

$mensaje = "";
$fotoPerfil = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $pass_actual = $_POST["pass_actual"];
    $pass_nueva = $_POST["pass_nueva"];
    $pfp = $_FILES["pfp"];

    // Buscar usuario
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario) {
        // Verificar contraseña actual
        if (password_verify($pass_actual, $usuario["contraseña"])) {
            $update = "UPDATE usuarios SET email=?, contraseña=?, pfp=? WHERE username=?";
            
            // Si hay nueva contraseña, hasheamos, sino dejamos la misma
            if (!empty($pass_nueva)) {
                $nueva_pass = password_hash($pass_nueva, PASSWORD_DEFAULT);
            } else {
                $nueva_pass = $usuario["contraseña"];
            }

            // Manejo de imagen
            $nombreArchivo = $usuario["pfp"]; // valor por defecto (la que ya tenía)
            if (!empty($pfp["name"])) {
                $carpetaDestino = "img/pfp/";
                $nombreArchivo = time() . "_" . basename($pfp["name"]);
                $rutaDestino = $carpetaDestino . $nombreArchivo;

                if (move_uploaded_file($pfp["tmp_name"], $rutaDestino)) {
                    $fotoPerfil = $rutaDestino;
                } else {
                    $mensaje = "⚠️ Error al subir la imagen.";
                }
            } else {
                if (!empty($usuario["pfp"])) {
                    $fotoPerfil = "img/pfp/" . $usuario["pfp"];
                }
            }

            // Guardar cambios
            $stmt2 = $conn->prepare($update);
            $stmt2->bind_param("ssss", $email, $nueva_pass, $nombreArchivo, $username);

            if ($stmt2->execute()) {
                $mensaje = "✅ Perfil actualizado correctamente";
            } else {
                $mensaje = "❌ Error al actualizar el perfil";
            }

        } else {
            $mensaje = "❌ La contraseña actual no es correcta.";
        }
    } else {
        $mensaje = "❌ Usuario no encontrado.";
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
        input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
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
    </style>
</head>
<body>
    <div class="perfil-container">
        <h2>Editar Perfil</h2>
        <?php if ($mensaje): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <div class="foto-preview">
            <?php if (!empty($fotoPerfil)): ?>
                <img src="<?php echo $fotoPerfil; ?>" alt="Foto de perfil">
            <?php else: ?>
                <img src="img/pfp/default.png" alt="Foto por defecto">
            <?php endif; ?>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <label>Nombre de usuario</label>
            <input type="text" name="username" placeholder="Tu usuario" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="ejemplo@email.com" required>

            <label>Foto de perfil</label>
            <input type="file" name="pfp">

            <label>Contraseña actual (obligatoria)</label>
            <input type="password" name="pass_actual" required>

            <label>Nueva contraseña (opcional)</label>
            <input type="password" name="pass_nueva">

            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</body>
</html>
