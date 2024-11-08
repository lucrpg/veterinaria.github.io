<?php
session_start();
session_destroy(); // Destruir todas las sesiones
header("Location: login.html"); // Redirigir al formulario de login
exit();
?>
