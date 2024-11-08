<?php
session_start(); // Iniciar sesión

// Conectar a la base de datos
$conex = mysqli_connect("localhost", "root", "", "veterinaria");
$acentros = $conex->query("SET NAMES 'utf8'");

// Verificar conexión
if ($conex->connect_error) {
    die("Conexión fallida: " . $conex->connect_error);
}

// Verificar si se envió el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT); // Hashear la contraseña
    $telefono = $_POST['telefono'];

    // Verificar si el correo ya está registrado en la base de datos
    $check_email_sql = "SELECT email FROM dueños WHERE email = ?";
    $stmt_check = $conex->prepare($check_email_sql);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "El correo ya está registrado.";
    } else {
        // SQL para insertar los datos del usuario en la tabla 'dueños'
        $sql = "INSERT INTO dueños (nombre, apellido, dni, email, contraseña, telefono) VALUES (?, ?, ?, ?, ?, ?)";

        // Preparar la consulta
        $stmt = $conex->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la declaración: " . $conex->error);
        }

        // Vincular parámetros
        $stmt->bind_param("ssssss", $nombre, $apellido, $dni, $email, $contraseña, $telefono);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Guardar el nombre del usuario en la sesión para mostrarlo en el perfil
            $_SESSION['nombre_usuario'] = $nombre;

            // Redirigir a la página principal después del registro exitoso
            header("Location: index.php");
            exit();
        } else {
            echo "Error al insertar los datos: " . $conex->error;
        }

        // Cerrar la declaración preparada
        $stmt->close();
    }

    // Cerrar la declaración de verificación de correo
    $stmt_check->close();
}

// Cerrar la conexión a la base de datos
$conex->close();
?>
