<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "conexion.php";

// Verificar que el formulario se haya enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : "";

    if (!empty($username) && !empty($contraseña)) {
        // Consulta preparada
        $stmt = $conn->prepare("SELECT id, username, contraseña FROM usuarios WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            // Verificar contraseña
            if (password_verify($contraseña, $usuario['contraseña'])) {
                $_SESSION['usuario'] = $usuario['username'];
                $_SESSION['id'] = $usuario['id'];

                header("Location: index.php");
                exit;
            } else {
                echo "⚠️ Contraseña incorrecta.";
            }
        } else {
            echo "⚠️ Usuario no encontrado.";
        }

        $stmt->close();
    } else {
        echo "⚠️ Completa todos los campos.";
    }
}

$conn->close();
?>
