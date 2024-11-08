<?php
session_start(); // Iniciar sesión

// Conectar a la base de datos
$conex = new mysqli("localhost", "root", "", "veterinaria");
$conex->set_charset("utf8");

// Verificar conexión
if ($conex->connect_error) {
    die("Conexión fallida: " . $conex->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar los datos del formulario
    $nombre = filter_var($_POST['nombre'], FILTER_SANITIZE_STRING);
    $apellido = filter_var($_POST['apellido'], FILTER_SANITIZE_STRING);
    $dni = filter_var($_POST['dni'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $contraseña = $_POST['contraseña'];
    $telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_STRING);

    // Verificar si el correo ya está registrado
    $check_email_sql = "SELECT email FROM dueños WHERE email = ?";
    $stmt_check = $conex->prepare($check_email_sql);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "El correo ya está registrado. <a href='login.html'>Inicia sesión aquí.</a>";
    } else {
        // Hash de la contraseña
        $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);

        // Insertar los datos del usuario
        $sql = "INSERT INTO dueños (nombre, apellido, dni, email, contraseña, telefono) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conex->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la declaración: " . $conex->error);
        }
        $stmt->bind_param("ssssss", $nombre, $apellido, $dni, $email, $hashed_password, $telefono);

        if ($stmt->execute()) {
            $_SESSION['nombre_usuario'] = $nombre;
            header("Location: index.php");
            exit();
        } else {
            echo "Error al insertar los datos: " . $conex->error;
        }

        $stmt->close();
    }

    $stmt_check->close();
}
$conex->close();
?>
