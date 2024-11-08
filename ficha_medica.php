<?php
session_start(); // Iniciar sesión

// Verificar si se ha proporcionado el ID de la mascota
if (isset($_GET['mascota_id'])) {
    $mascota_id = intval($_GET['mascota_id']); // Asegúrate de que el ID es un número entero

    // Conectar a la base de datos
    $conex = new mysqli("localhost", "root", "", "veterinaria");

    // Verificar conexión
    if ($conex->connect_error) {
        die("Conexión fallida: " . $conex->connect_error);
    }

    // Consulta para obtener la información completa de la mascota y su control general
    $sql = "SELECT m.nombre, m.especie, m.raza, 
            cg.vacunas, cg.dosis_vacuna, cg.fecha_vacuna, 
            cg.antiparasitos, cg.dosis_antiparasitos, cg.fecha_antiparasito, 
            cg.peso, cg.fecha_peso, cg.observaciones, cg.proxima_visita
            FROM mascotas m
            LEFT JOIN control_general cg ON m.id = cg.mascota_id
            WHERE m.id = ?";

    $stmt = $conex->prepare($sql);
    if ($stmt === false) {
        die("Error al preparar la declaración: " . $conex->error);
    }

    $stmt->bind_param("i", $mascota_id); // Vincular el id de la mascota
    $stmt->execute();
    $result = $stmt->get_result(); // Obtener resultados

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        $mascota = $result->fetch_assoc();
    } else {
        die("No se encontró la información de la mascota.");
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conex->close();
} else {
    die("ID de mascota no proporcionado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Médica de <?php echo htmlspecialchars($mascota['nombre']); ?></title>
</head>
<body>
    <h2>Ficha Médica de <?php echo htmlspecialchars($mascota['nombre']); ?></h2>
    <p>Especie: <?php echo htmlspecialchars($mascota['especie']); ?></p>
    <p>Raza: <?php echo htmlspecialchars($mascota['raza']); ?></p>

    <h3>Control General</h3>
    <p>Vacunas: <?php echo htmlspecialchars($mascota['vacunas']); ?></p>
    <p>Dosis de Vacuna: <?php echo htmlspecialchars($mascota['dosis_vacuna']); ?></p>
    <p>Fecha de Vacuna: <?php echo htmlspecialchars($mascota['fecha_vacuna']); ?></p>
    <p>Antiparasitos: <?php echo htmlspecialchars($mascota['antiparasitos']); ?></p>
    <p>Dosis de Antiparasitos: <?php echo htmlspecialchars($mascota['dosis_antiparasitos']); ?></p>
    <p>Fecha de Antiparasito: <?php echo htmlspecialchars($mascota['fecha_antiparasito']); ?></p>
    <p>Peso: <?php echo htmlspecialchars($mascota['peso']); ?> kg</p>
    <p>Fecha de Peso: <?php echo htmlspecialchars($mascota['fecha_peso']); ?></p>
    <p>Observaciones: <?php echo htmlspecialchars($mascota['observaciones']); ?></p>
    <p>Próxima Visita: <?php echo htmlspecialchars($mascota['proxima_visita']); ?></p>

    <button onclick="window.location.href='index.html'">Volver al inicio</button>
</body>
</html>
