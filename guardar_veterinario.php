<?php
// Verificar si se han enviado los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos de conexión a la base de datos
    $conex = new mysqli("localhost", "root", "", "veterinaria");

    // Verificar conexión
    if ($conex->connect_error) {
        die("Conexión fallida: " . $conex->connect_error);
    }

    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT); // Hashear la contraseña
    $telefono = $_POST['telefono'];
    $dni = $_POST['dni'];
    $especializacion = $_POST['especializacion'];
    $horario_de_trabajo = $_POST['horario_de_trabajo'];

    // Validar que todos los campos no estén vacíos
    if (empty($nombre) || empty($apellido) || empty($email) || empty($contraseña) || empty($telefono) || empty($dni) || empty($especializacion) || empty($horario_de_trabajo)) {
        die("Todos los campos son obligatorios.");
    }

    // Validar que teléfono y DNI sean numéricos
    if (!is_numeric($telefono) || !is_numeric($dni)) {
        die("El teléfono y el DNI deben ser numéricos.");
    }

    // Hash de la contraseña
    $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);

    // SQL para insertar datos
    $sql = "INSERT INTO `datos del veterinario` (nombre, apellido, email, contraseña, telefono, dni, especializacion, `horario de trabajo`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la declaración
    $stmt = $conex->prepare($sql);
    if ($stmt === false) {
        die("Error al preparar la declaración: " . $conex->error);
    }

    // Vincular parámetros
    $stmt->bind_param("ssssssss", $nombre, $apellido, $email, $hashed_password, $telefono, $dni, $especializacion, $horario_de_trabajo);

    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo "Datos del veterinario registrados exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conex->close();
} else {
    die("Método de solicitud no permitido.");
}
?>
