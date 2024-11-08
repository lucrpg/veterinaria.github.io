<?php
session_start(); // Iniciar sesión

// Conectar a la base de datos
$conex = new mysqli("localhost", "root", "", "veterinaria");
$conex->set_charset("utf8");

if ($conex->connect_error) {
    die("Conexión fallida: " . $conex->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar entrada
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contraseña = $_POST['contraseña'];

    // Buscar al usuario en la base de datos por email
    $sql = "SELECT dueño_id, nombre, contraseña FROM dueños WHERE email = ?";
    $stmt = $conex->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conex->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si existe el email
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nombre, $contraseña_db);
        $stmt->fetch();

        // Comparar la contraseña directamente (sin hash)
        if ($contraseña === $contraseña_db) {
            // Contraseña correcta: iniciar sesión
            $_SESSION['nombre_usuario'] = $nombre;
            $_SESSION['id_usuario'] = $id;
            header("Location: index.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Credenciales incorrectas.";
    }

    $stmt->close();
}

$conex->close();
?>
