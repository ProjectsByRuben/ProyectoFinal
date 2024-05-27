<?php
session_start();
include './scripts/conexion.php'; // Incluye el archivo de conexión

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];
$id_modulo = $_SESSION['id_modulo'];

// Verifica si id_modulo es NULL
$nombre_modulo = "Módulo Desconocido";
if ($id_modulo !== NULL) {
    $sql = "SELECT nombre FROM modulos WHERE id_modulo = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_modulo);
        $stmt->execute();
        $stmt->bind_result($nombre_modulo);
        $stmt->fetch();
        $stmt->close();
    }
}

// Consulta el número total de ejercicios disponibles (solo si el tipo de usuario no es admin)
$total_ejercicios = 0;
if ($tipo_usuario !== 'admin') {
    $sql_total_ejercicios = "SELECT COUNT(e.id_ejercicio) AS total_ejercicios
                             FROM asignaturas a
                             LEFT JOIN ejercicios e ON a.id_asignatura = e.id_asignatura
                             WHERE a.id_modulo = ?";
    if ($stmt = $conn->prepare($sql_total_ejercicios)) {
        $stmt->bind_param("i", $id_modulo);
        $stmt->execute();
        $stmt->bind_result($total_ejercicios);
        $stmt->fetch();
        $stmt->close();
    }
}

// Consulta el número total de usuarios
$total_usuarios = 0;
$sql_total_usuarios = "SELECT COUNT(*) AS total_usuarios FROM usuarios";
if ($resultado_total_usuarios = $conn->query($sql_total_usuarios)) {
    $total_usuarios = $resultado_total_usuarios->fetch_assoc()['total_usuarios'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./estilos/styles.css?v=2" id="themeStylesheet">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Bangers', cursive;
            background-color: #F8F9FA;
        }
        .navbar {
            padding-left: 0 !important;
            padding-right: 10px !important;
            margin-top: 0 !important;
        }
        .jumbotron {
            padding-top: 5px;
            padding-left: 5px;
            padding-right: 5px;
        }
        .modal-button {
            margin-left: auto;
        }
        #themeIcon {
            width: 28px;
            height: 25px;
            margin-left: 10px;
            margin-right: 20px;
        }
        #themeButton {
            background-color: transparent;
            border: none;
            padding: 0;
        }
        #themeButton img {
            width: 28px;
            height: 25px;
        }
        #notification {
            display: none;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            position: fixed;
            top: 10px;
            right: 10px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        #progressBar {
            width: 100%;
            background-color: #f1f1f1;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }
        #progressBar div {
            height: 10px;
            width: 0;
            background-color: #007bff;
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
                            Módulo
                        </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./modulos.php"><?php echo $nombre_modulo; ?></a></li>
                        <li><a class="dropdown-item" href="./asignaturas.php?id_curso=1">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas.php?id_curso=2">2º Curso</a></li>
                    </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./soluciones.php">Soluciones</a>
                    </li>
                <?php endif; ?>
                <?php if ($tipo_usuario === 'profesor'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link active dropdown-toggle" href="./modulos.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Módulo
                        </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./modulos.php"><?php echo $nombre_modulo; ?></a></li>
                        <li><a class="dropdown-item" href="./asignaturas.php?id_curso=1">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas.php?id_curso=2">2º Curso</a></li>
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
                        <a class="nav-link active" aria-current="page" href="./usuarios.php">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./ver_modulos.php">Modulos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_usuario.php">Crear Usuario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_modulos.php">Crear Modulos</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Contenedor de la notificación -->
    <?php if (isset($_SESSION['showNotification']) && $_SESSION['showNotification']): ?>
        <div id="notification">
            Inicio de sesión correcto
            <div id="progressBar"><div></div></div>
        </div>
        <?php unset($_SESSION['showNotification']); ?>
    <?php endif; ?>

    <!-- Button trigger modal -->
    <button type="button" class="btn modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal" style="border: none;">
        <img src="./img/usuario.png" style="width: 25px; height: 25px;">
    </button>
    <button id="themeButton" onclick="toggleTheme()">
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
                <a href="../scripts/../scripts/cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<div class="jumbotron text-center">
    <div class="card2 mb-3">
        <h1 class="display-3">¡Bienvenido a EjercitaCode!</h1>
        <p class="lead">¡Bienvenido a nuestro portal educativo! Aquí los Profesores comparten ejercicios de asignaturas para que los alumnos practiquen y aprendan. Los ejercicios disponen de pistas y soluciones. Perfiles de alumno, profesor y administrador con funciones específicas para una experiencia personalizada.</p>
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
                <a class="btn btn-primary btn-lg" href="./modificar_usuario.php" role="button">Usuarios</a>
            <?php endif; ?>
            <?php if ($tipo_usuario === 'admin'): ?>
                <a class="btn btn-primary btn-lg" href="./ver_modulos.php" role="button">Modulos</a>
            <?php endif; ?>
            <?php if ($tipo_usuario === 'admin'): ?>
                <a class="btn btn-primary btn-lg" href="./crear_usuario.php" role="button">Crear Usuario</a>
            <?php endif; ?>
            <?php if ($tipo_usuario === 'admin'): ?>
                <a class="btn btn-primary btn-lg" href="./crear_modulos.php" role="button">Crear Módulo</a>
            <?php endif; ?>
        </p>
    </div>
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

<script>
    // Mostrar la notificación durante unos segundos con una barra de progreso
    window.onload = function() {
        var notification = document.getElementById('notification');
        var progressBar = document.getElementById('progressBar').children[0];
        if (notification) {
            notification.style.display = 'inline-block';
            let width = 0;
            const interval = setInterval(function() {
                if (width >= 100) {
                    clearInterval(interval);
                    notification.style.display = 'none';
                } else {
                    width++;
                    progressBar.style.width = width + '%';
                }
            }, 30); // La duración total es de aproximadamente 3 segundos (100 * 30ms = 3000ms)
        }
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>