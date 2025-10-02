<?php
require_once "conexion.php";

$mensaje = "";

// Solo procesamos si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? "";
    $contraseña = $_POST['contraseña'] ?? "";

    if (empty($username) || empty($contraseña)) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {

        // Lista de palabras prohibidas
        $palabrasProhibidas = [
            "puto","boludo","gil","idiota","hijo de puta","imbecil",
            "estupido","pendejo","cabrón","forro","tarado","mamerto",
            "pelotudo","mierda","culiao","facho","tonto","payaso",
            "cagon","zorra","perra","pajero","huevon","facho","basura",
            "malparido","cornudo","choto","maricón","sorete","pichón",
            "bobo","chupapija","gilipollas","mongol","peluca","trol",
            "cabron","idiota","chupamedias","baboso","maldito","bestia",
            "loco","culo","imbecil","mentiroso","malparida","bruto","zopenco"
        ];

        // Función para normalizar texto
        function normalizarTexto($texto) {
            $texto = strtolower($texto);
            $texto = str_replace(
                ['0','@','1','!','3','4','5','7'],
                ['o','o','i','i','e','a','s','t'],
                $texto
            );
            $texto = preg_replace("/[^a-z\s]/", "", $texto);
            return $texto;
        }

        // Validar nombre de usuario prohibido
        $usernameNormalizado = normalizarTexto($username);
        foreach ($palabrasProhibidas as $palabra) {
            if (strpos($usernameNormalizado, normalizarTexto($palabra)) !== false) {
                $mensaje = "Nombre de usuario inapropiado. No se permite login 😎";
                echo $mensaje;
                exit; // bloquea el login
            }
        }

        // Si pasó la validación, buscar usuario en BD
        $stmt = $conn->prepare("SELECT id, contraseña FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $mensaje = "Usuario no encontrado.";
        } else {
            $stmt->bind_result($id, $hash);
            $stmt->fetch();

            if (password_verify($contraseña, $hash)) {
                $mensaje = "¡Bienvenido, $username!";
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        }

        $stmt->close();
        $conn->close();
        echo $mensaje;
    }
}
?>
