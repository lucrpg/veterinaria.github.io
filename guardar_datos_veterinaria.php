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
    $clinica = $_POST['clinica'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $email = $_POST['email'];
    $dueño = $_POST['dueño'];

    // SQL para insertar datos
    $sql = "INSERT INTO `datos de la veterinaria` (clinica, telefono, direccion, email, dueño) VALUES (?, ?, ?, ?, ?)";

    // Preparar la declaración
    $stmt = $conex->prepare($sql);
    if ($stmt === false) {
        die("Error al preparar la declaración: " . $conex->error);
    }

    // Vincular parámetros
    $stmt->bind_param("sssss", $clinica, $telefono, $direccion, $email, $dueño);

    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo "Datos de la veterinaria guardados exitosamente.";
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
