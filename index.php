<?php
session_start(); // Iniciar sesión

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['nombre_usuario']) && isset($_SESSION['id_usuario'])) {
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $id_usuario = $_SESSION['id_usuario']; // Obtener el id del usuario
} else {
    // Si no ha iniciado sesión, redirigir al formulario de login o registro
    header("Location: login.html");
    exit();
}

// Conectar a la base de datos
$conex = new mysqli("localhost", "root", "", "veterinaria");

// Verificar conexión
if ($conex->connect_error) {
    die("Conexión fallida: " . $conex->connect_error);
}

// Consulta para obtener las mascotas registradas asociadas al propietario
$sql = "SELECT nombre, especie, raza FROM mascotas WHERE propietario_id = ?";
$stmt = $conex->prepare($sql);
$stmt->bind_param("i", $id_usuario); // Vincular el id del propietario
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Pet</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAE8EoUU_4rMgjgf3XJd3DwYX5hrMYBzlU&libraries=places&callback=initMap" async defer></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .top-bar {
            background-color: #8B4513; /* Color café */
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            color: white;
        }
        .menu-icon img {
            width: 30px;
            cursor: pointer;
        }
        .search-bar {
            display: flex;
            justify-content: center;  /* Centrar horizontalmente */
            align-items: center;      /* Centrar verticalmente */
            width: 100%;              /* Asegura que ocupe todo el ancho disponible */
        }
        .search-bar input {
            padding: 5px;
            border: none;
            border-radius: 4px;
            margin-right: 5px;  /* Espacio entre el input y el botón */
        }
        .search-bar button {
            background-color: #FFF;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .sidebar {
            background-color: #a96a43; /* Color café claro */
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding: 20px;
            display: none; /* Inicialmente oculto */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
        }
        .close-icon {
            font-size: 24px;
            cursor: pointer;
            color: black; /* Flecha negra */
            margin-bottom: 20px;
        }
        .main-content {
            margin-left: 260px; /* Espacio para la sidebar */
            padding: 20px;
        }
        .add-pet {
            background-color: #8B4513;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }
        .add-pet:hover {
            background-color: #A0522D;
        }
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="menu-icon" onclick="toggleSidebar()">
            <img src="menu-icon.png" alt="Menú">
        </div>
        <div class="search-bar">
            <input id="search-box" type="text" placeholder="Buscar ubicación...">
            <button onclick="searchLocation()">🔍</button>
        </div>
    </div>

    <aside id="sidebar" class="sidebar">
        <div class="close-icon" onclick="toggleSidebar()">
            ←
        </div>

        <div class="user-profile">
            <p><?php echo htmlspecialchars($nombre_usuario); ?></p> <!-- Mostrar el nombre del usuario -->
            <a href="logout.php">Cerrar sesión</a> <!-- Botón para cerrar sesión -->
        </div>
        
        <ul class="pet-list">
            <!-- Mostrar las mascotas registradas -->
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($row['nombre']); ?></strong><br>
                        Especie: <?php echo htmlspecialchars($row['especie']); ?><br>
                        Raza: <?php echo htmlspecialchars($row['raza']); ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No hay mascotas registradas.</li>
            <?php endif; ?>
            <li><button class="add-pet" onclick="window.location.href='formulario_mascota.html'">+</button></li>
        </ul>
    </aside>

    <main class="main-content">
        <div id="map"></div>
    </main>

    <script>
        let map, marker;

        // Inicializar el mapa
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 37.421999, lng: -122.084249 }, // Googleplex por defecto
                zoom: 15
            });
            marker = new google.maps.Marker({
                map: map,
            });
        }

        // Buscar la ubicación y centrar el mapa
        function searchLocation() {
            const query = document.getElementById('search-box').value.trim();

            // Validación para verificar que el campo no está vacío
            if (query === '') {
                alert('Por favor ingrese una búsqueda.');
                return;
            }

            const service = new google.maps.places.PlacesService(map);
            const request = {
                query: query,
                fields: ['name', 'geometry'],
            };

            service.findPlaceFromQuery(request, (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    const location = results[0].geometry.location;
                    map.setCenter(location);
                    marker.setPosition(location);
                    map.setZoom(15);
                } else {
                    alert('No se encontraron resultados para la búsqueda');
                }
            });
        }

        // Función para mostrar y ocultar el sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            // Cambiar entre mostrar u ocultar el sidebar
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block';
            } else {
                sidebar.style.display = 'none';
            }
        }

        // Esperar que la API de Google Maps se cargue y ejecutar la función initMap
        window.onload = function() {
            initMap();
        };
    </script>
</body>
</html>

<?php
// Cerrar la declaración y la conexión
$stmt->close();
$conex->close();
?>
