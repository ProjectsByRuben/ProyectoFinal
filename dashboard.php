<?php
include './scripts/conexion.php'; // Incluye el archivo de conexión
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];
$id_modulo = $_SESSION['id_modulo'];

// Verifica si id_modulo es NULL
if ($id_modulo === NULL) {
    $nombre_modulo = "Módulo Desconocido";
} else {
    // Consulta el nombre del módulo si id_modulo no es NULL
    $sql = "SELECT nombre FROM modulos WHERE id_modulo = $id_modulo";
    $resultado = $conn->query($sql);

    // Verificar si se encontró el módulo y obtener su nombre
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $nombre_modulo = $fila["nombre"];
    } else {
        // Si no se encuentra el módulo, mostrar un mensaje de error
        $nombre_modulo = "Módulo Desconocido";
    }
}

// Consulta el número total de ejercicios disponibles
$sql_total_ejercicios = "SELECT COUNT(*) AS total_ejercicios FROM ejercicios";
$resultado_total_ejercicios = $conn->query($sql_total_ejercicios);
$total_ejercicios = $resultado_total_ejercicios->fetch_assoc()['total_ejercicios'];

// Consulta el número total de usuarios
$sql_total_usuarios = "SELECT COUNT(*) AS total_usuarios FROM usuarios";
$resultado_total_usuarios = $conn->query($sql_total_usuarios);
$total_usuarios = $resultado_total_usuarios->fetch_assoc()['total_usuarios'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=2" id="themeStylesheet">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F8F9FA;
        }
        .navbar {
            padding-left: 0 !important; /* Eliminar el padding a la izquierda */
            padding-right: 10px !important; /* Eliminar el padding a la derecha */
            margin-top: 0 !important; /* Eliminar el margen superior */
        }
        /* Estilos personalizados */
        .carousel-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px; /* Espacio arriba */
        }

        .carousel-title {
            font-size: 2rem; /* Tamaño del título */
            margin-bottom: 20px; /* Espacio debajo del título */
        }

        .carousel-description {
            font-size: 1.2rem; /* Tamaño de la descripción */
            text-align: center; /* Alineación del texto */
            max-width: 80%; /* Ancho máximo del contenedor de la descripción */
            margin-bottom: 40px; /* Espacio debajo de la descripción */
        }

        /* Estilos personalizados para el botón de la ventana modal */
        .modal-button {
            margin-left: auto; /* Mover el botón hacia la derecha */
        }
        /* Estilo para la imagen del sol y la luna */
        #themeIcon {
            width: 28px; /* Ajustar el ancho */
            height: 25px; /* Ajustar la altura */
            margin-left: 10px;
            margin-right: 20px;
        }
        /* Estilo para ocultar el botón y mostrar solo la imagen */
        #themeButton {
            background-color: transparent;
            border: none;
            padding: 0;
        }

        #themeButton img {
            width: 28px;
            height: 25px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="./img/logo.png" alt="Bootstrap" width="140" height="90">
    <div class="container-fluid">
        <a class="navbar-brand" href="./dashboard.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <?php if ($tipo_usuario === 'alumno'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="./modulos.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Asignatura
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./modulos.php"><?php echo $nombre_modulo; ?></a></li>
                        <?php if ($id_modulo == 1): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_primero.php">1º Asir</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_segundo.php">2º Asir</a></li>
                        <?php elseif ($id_modulo == 2): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_primero.php">1º Teleco</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_segundo.php">2º Teleco</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./soluciones.php">Soluciones</a>
                </li>
                <?php endif; ?>
                <?php if ($tipo_usuario === 'profesor'): ?>
                    <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="./modulos.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Asignatura
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./modulos.php"><?php echo $nombre_modulo; ?></a></li>
                        <?php if ($id_modulo == 1): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_primero.php">1º Asir</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_segundo.php">2º Asir</a></li>
                        <?php elseif ($id_modulo == 2): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_primero.php">1º Teleco</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_segundo.php">2º Teleco</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./soluciones.php">Soluciones</a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_ejercicio.php">Crear Ejercicio</a>
                    </li>
                <?php endif; ?>
                <?php if ($tipo_usuario === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_usuario.php">Crear Usuario</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal">Sesion</button>
    <button id="themeButton" onclick="toggleTheme()" class="btn">
        <img id="themeIcon" src="./img/<?php echo $currentTheme === 'dark' ? 'sun' : 'moon'; ?>.png" alt="<?php echo $currentTheme === 'dark' ? 'moon' : 'sun'; ?>">
    </button>
</nav>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Información de Usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Nombre de usuario: <?php echo $_SESSION['usuario']; ?></p>
                <p>Contraseña: <?php echo $_SESSION['pass']; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron text-center">
    <h1 class="display-3">¡Bienvenido a EjercitaCode!</h1>
    <p class="lead">¡Bienvenido a nuestro portal educativo! Aquí los Profesores comparten ejercicios de asignaturas para que los alumnos practiquen y aprendan. Los ejercicios disponen de pistas y soluciones. Perfiles de alumno, profesor y administrador con funciones específicas para una experiencia personalizada.</p>
    <hr class="my-4">
    <p>¿Listo para empezar?</p>
    <p class="lead">
        <?php if ($tipo_usuario == 'profesor' || $tipo_usuario == 'alumno'): ?>
            <a class="btn btn-primary btn-lg" href="./modulos.php" role="button">Ver Asignaturas</a>
        <?php endif; ?>
        <?php if ($tipo_usuario == 'profesor'): ?>
            <a class="btn btn-primary btn-lg" href="./crear_ejercicio.php" role="button">Crear Ejercicio</a>
        <?php endif; ?>
        <?php if ($tipo_usuario == 'profesor' || $tipo_usuario == 'alumno'): ?>
            <a class="btn btn-secondary btn-lg" href="./soluciones.php" role="button">Ver Soluciones</a>
        <?php endif; ?>
        <?php if ($tipo_usuario === 'admin'): ?>
            <a class="btn btn-primary btn-lg" href="./crear_usuario.php" role="button">Crear Usuario</a>
        <?php endif; ?>
    </p>
</div>

<!-- Contenedor del gráfico -->
<div class="container">
    <h2 class="text-center mb-4">Estadísticas</h2>
    <div class="row justify-content-center"> <!-- Centra horizontalmente -->
        <div class="col-md-8">
            <div class="text-center"> <!-- Centra verticalmente -->
                <canvas id="barChart" width="1000" height="500"></canvas> <!-- Ajustado para que sea más grande -->
            </div>
        </div>
    </div>
</div>

<!-- Script para incluir la biblioteca Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Script para configurar y mostrar el gráfico -->
<script>
    // Datos del gráfico
    var totalEjercicios = <?php echo $total_ejercicios; ?>;
    var totalUsuarios = <?php echo $total_usuarios; ?>;

    // Configuración del gráfico
    var ctx = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Ejercicios Disponibles', 'Usuarios Totales'],
            datasets: [{
                label: 'Ejercicios', // Etiqueta para los ejercicios
                data: [totalEjercicios, 0], // Se establece 0 para el total de usuarios para que no se muestre en la leyenda
                backgroundColor: '#007bff',
                borderWidth: 1
            }, {
                label: 'Usuarios', // Etiqueta para los usuarios
                data: [0, totalUsuarios], // Se establece 0 para el total de ejercicios para que no se muestre en la leyenda
                backgroundColor: '#28a745',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        // Filtrar las etiquetas para que solo muestre las de ejercicios y usuarios
                        filter: function(item, chart) {
                            return item.text === 'Ejercicios' || item.text === 'Usuarios';
                        }
                    }
                }
            }
        }
    });
</script>

<script>
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme); // Guarda el tema seleccionado en el almacenamiento local

        // Actualiza la imagen del botón después de cambiar el tema
        const themeIcon = document.getElementById('themeIcon');
        themeIcon.src = `./img/${newTheme === 'dark' ? 'sun' : 'moon'}.png`;
    }

    // Aplica el tema almacenado en localStorage al cargar la página
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
    }

    // Actualiza la imagen del botón según el tema actual al cargar la página
    const themeIcon = document.getElementById('themeIcon');
    themeIcon.src = `./img/${currentTheme === 'dark' ? 'sun' : 'moon'}.png`;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>