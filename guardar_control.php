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
    $mascota_id = $_POST['mascota_id']; // ID de la mascota ingresado por el usuario
    $dueño_id = $_POST['dueño_id']; // ID del dueño
    $vacunas = $_POST['vacunas'];
    $dosis_vacuna = $_POST['dosis_vacuna'];
    $fecha_vacuna = $_POST['fecha_vacuna'];
    $antiparasitos = $_POST['antiparasitos'];
    $dosis_antiparasitos = $_POST['dosis_antiparasitos'];
    $fecha_antiparasito = $_POST['fecha_antiparasito'];
    $peso = $_POST['peso'];
    $fecha_peso = $_POST['fecha_peso'];
    $observaciones = $_POST['observaciones'];
    $proxima_visita = $_POST['proxima_visita'];

    // Verificar si la mascota existe
    $sql_check = "SELECT * FROM mascotas WHERE mascota_id = ?"; // Asegúrate de usar el nombre correcto de la columna
    $stmt_check = $conex->prepare($sql_check);
    if (!$stmt_check) {
        die("Error al preparar la declaración para verificar mascota: " . $conex->error);
    }

    $stmt_check->bind_param("i", $mascota_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        die("Error: La mascota con ID $mascota_id no existe.");
    }

    // SQL para insertar datos
    $sql = "INSERT INTO control_general (mascota_id, vacunas, dosis_vacuna, fecha_vacuna, 
            antiparasitos, dosis_antiparasitos, fecha_antiparasito, peso, fecha_peso, 
            observaciones, proxima_visita) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la declaración
    $stmt = $conex->prepare($sql);
    if (!$stmt) {
        die("Error al preparar la declaración para insertar control general: " . $conex->error);
    }

    // Vincular parámetros
    $stmt->bind_param("issssssssss", $mascota_id, $vacunas, $dosis_vacuna, $fecha_vacuna, 
                      $antiparasitos, $dosis_antiparasitos, $fecha_antiparasito, 
                      $peso, $fecha_peso, $observaciones, $proxima_visita);

    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo "Datos del control general de mascotas guardados exitosamente.";
        header("Location: ficha_medica.php?mascota_id=" . $mascota_id);
        exit();
    } else {
        echo "Error al ejecutar la declaración: " . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt_check->close();
    $stmt->close();
    $conex->close();
} else {
    die("Método de solicitud no permitido.");
}
?>
