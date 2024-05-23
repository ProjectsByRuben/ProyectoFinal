<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

include './scripts/conexion.php'; // Incluye el archivo de conexión

$tipo_usuario = $_SESSION['tipo'];
$id_modulo = $_SESSION['id_modulo'];
$id_usuario = $_SESSION['id_usuario'];

// Función para obtener el nombre del módulo
function obtenerNombreModulo($conn, $id_modulo) {
    if ($id_modulo === NULL) {
        return "Módulo Desconocido";
    } else {
        $stmt = $conn->prepare("SELECT nombre FROM modulos WHERE id_modulo = ?");
        $stmt->bind_param("i", $id_modulo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return $fila["nombre"];
        } else {
            return "Módulo Desconocido";
        }
    }
}

// Función para obtener el número de ejercicios
function obtenerNumeroEjercicios($conn, $id_modulo, $id_curso = null) {
    $query = "SELECT COUNT(e.id_ejercicio) AS num_ejercicios
              FROM asignaturas a
              LEFT JOIN ejercicios e ON a.id_asignatura = e.id_asignatura
              WHERE a.id_modulo = ?";
    if ($id_curso !== null) {
        $query .= " AND a.id_curso = ?";
    }

    $stmt = $conn->prepare($query);
    if ($id_curso !== null) {
        $stmt->bind_param("ii", $id_modulo, $id_curso);
    } else {
        $stmt->bind_param("i", $id_modulo);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['num_ejercicios'];
    } else {
        return 0;
    }
}

$nombre_modulo = obtenerNombreModulo($conn, $id_modulo);
$num_ejercicios = obtenerNumeroEjercicios($conn, $id_modulo);
$num_ejercicios_curso1 = obtenerNumeroEjercicios($conn, $id_modulo, 1);
$num_ejercicios_curso2 = obtenerNumeroEjercicios($conn, $id_modulo, 2);

$imagen_modulo = ($id_modulo == 1) ? "asir.png" : "teleco.png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=6" id="themeStylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Bangers', cursive;
            background-color: #f8f9fa;
        }
        .navbar {
            padding-left: 0 !important; /* Eliminar el padding a la izquierda */
            padding-right: 10px !important; /* Eliminar el padding a la derecha */
            margin-top: 0 !important; /* Eliminar el margen superior */
            margin-bottom: 10px !important; /* Añade margen inferior de 10px */
        }
        #themeIcon {
            width: 28px; /* Ajustar el ancho */
            height: 25px; /* Ajustar la altura */
            margin-left: 11px;
            margin-right: 10px;
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
        .color {
            color: orange;
        }
        .small-text {
            font-size: 0.8rem;
        }
        .card-text {
            font-size: 18px;
        }
        .ejercicios-card {
        background-color: #ffffff;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        transition: transform 1s, box-shadow 1s;
        }
        .ejercicios-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
        }
        .ejercicios-card .card-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .ejercicios-card .card-text {
            font-size: 1.2rem;
        }
        .btn-outline-primary {
            border-color: #007bff;
            color: black;
            transition: 0.5s;
        }
        .btn-outline-primary:hover {
            background-color: transparent;
            font-size: 20px;
            color: black;
        }
        .bi {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="./img/logo.png" alt="Bootstrap" width="140" height="90">
    <div class="container-fluid">
        <a class="nav-link active" aria-current="page" href="javascript:history.back()">
            <img src="../img/flecha.png" class="img-fluid" style="max-width: 30px;" alt="Flecha">
            <span style='margin: 0 10px;'></span>
        </a>
        <a class="navbar-brand" href="./dashboard.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <?php if ($tipo_usuario === 'alumno' || $tipo_usuario === 'profesor'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="./modulos.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Módulo
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="./modulos.php"><?php echo $nombre_modulo; ?></a></li>
                        <?php if ($id_modulo == 1): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_primero.php">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_segundo.php">2º Curso</a></li>
                        <?php elseif ($id_modulo == 2): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_primero.php">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_segundo.php">2º Curso</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./soluciones.php">Soluciones</a>
                </li>
                <?php if ($tipo_usuario === 'profesor'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_ejercicio.php">Crear Ejercicio</a>
                    </li>
                <?php endif; ?>
                <?php if ($tipo_usuario === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_usuario.php">Crear Usuario</a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Button trigger modal -->
    <button type="button" class="btn modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal" style="border: none;"><img src="./img/usuario.png" style="width: 25px; height: 25px;"></button>
    <button id="themeButton" onclick="toggleTheme()" class="btn">
        <img id="themeIcon" src="./img/<?php echo $currentTheme === 'dark' ? 'sun' : 'moon'; ?>.png" alt="<?php echo $currentTheme === 'dark' ? 'moon' : 'sun'; ?>">
    </button>
</nav>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Información de la Sesión</h1>
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

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card ejercicios-card p-4 mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title">Módulo: <?php echo $nombre_modulo; ?></h5>
                    <p class="card-text-solution">Este módulo tiene disponibles <span class="text-primary"><?php echo $num_ejercicios; ?></span> ejercicio/s en total.</p>
                    <hr>
                    <?php if ($id_modulo == 1): ?>
                        <div class="d-grid gap-2">
                            <a href="./asignaturas/asignaturas_asir_primero.php" class="uno btn btn-outline-primary mb-2">
                                <i class="bi bi-book"></i> Explorar 1º Curso (<span class="text-primary"><?php echo $num_ejercicios_curso1; ?></span> ejercicio/s)
                            </a>
                            <a href="./asignaturas/asignaturas_asir_segundo.php" class="uno btn btn-outline-primary mb-2">
                                <i class="bi bi-book"></i> Explorar 2º Curso (<span class="text-primary"><?php echo $num_ejercicios_curso2; ?></span> ejercicio/s)
                            </a>
                        </div>
                    <?php elseif ($id_modulo == 2): ?>
                        <div class="d-grid gap-2">
                            <a href="./asignaturas/asignaturas_teleco_primero.php" class="uno btn btn-outline-primary mb-2">
                                <i class="bi bi-book"></i> Explorar 1º Curso (<span class="text-primary"><?php echo $num_ejercicios_curso1; ?></span> ejercicio/s)
                            </a>
                            <a href="./asignaturas/asignaturas_teleco_segundo.php" class="uno btn btn-outline-primary mb-2">
                                <i class="bi bi-book"></i> Explorar 2º Curso (<span class="text-primary"><?php echo $num_ejercicios_curso2; ?></span> ejercicio/s)
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

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