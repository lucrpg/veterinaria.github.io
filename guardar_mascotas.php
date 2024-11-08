
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
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $fechadenacimiento = isset($_POST['fechadenacimiento']) ? trim($_POST['fechadenacimiento']) : '';
    $sexo = isset($_POST['sexo']) ? trim($_POST['sexo']) : '';
    $especie = isset($_POST['especie']) ? trim($_POST['especie']) : '';
    $raza = isset($_POST['raza']) ? trim($_POST['raza']) : '';
    $propietario_email = isset($_POST['propietario']) ? trim($_POST['propietario']) : '';

    // Validar que todos los campos no estén vacíos
    if (empty($nombre) || empty($fechadenacimiento) || empty($sexo) || empty($especie) || empty($raza) || empty($propietario_email)) {
        die("Todos los campos son obligatorios.");
    }

    // Comprobar si el propietario existe y obtener su ID
    $check_owner_sql = "SELECT id FROM dueños WHERE email = ?";
    $stmt_check_owner = $conex->prepare($check_owner_sql);
    $stmt_check_owner->bind_param("s", $propietario_email);
    $stmt_check_owner->execute();
    $stmt_check_owner->store_result();

    // Verificar si el propietario existe y obtener el ID
    if ($stmt_check_owner->num_rows === 0) {
        die("El propietario no existe en la base de datos.");
    } else {
        $stmt_check_owner->bind_result($propietario_id);
        $stmt_check_owner->fetch();
    }

    // SQL para insertar datos
    $sql = "INSERT INTO mascotas (nombre, fechadenacimiento, sexo, especie, raza, propietario_id) VALUES (?, ?, ?, ?, ?, ?)";

    // Preparar la declaración
    $stmt = $conex->prepare($sql);
    if ($stmt === false) {
        die("Error al preparar la declaración: " . $conex->error);
    }

    // Vincular parámetros
    $stmt->bind_param("sssssi", $nombre, $fechadenacimiento, $sexo, $especie, $raza, $propietario_id);

    // Ejecutar la declaración
    if ($stmt->execute()) {
        // Redirigir a index.php después de registrar la mascota exitosamente
        header("Location: index.php");
        exit(); // Asegúrate de usar exit después de header
    } else {
        echo "Error al registrar la mascota: " . $conex->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conex->close();
} else {
    die("Método de solicitud no permitido.");
}
?>
