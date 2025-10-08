<?php
session_start();
session_unset(); // elimina todas las variables de sesión
session_destroy(); // destruye la sesión

header("Location: login.html"); // redirige al login
exit;
?>
