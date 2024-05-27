<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];
$message = '';

// Procesar el envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nombre_modulo = $_POST["nombreModulo"];
    $asignaturas_1 = $_POST["asignaturas_1"];
    $asignaturas_2 = $_POST["asignaturas_2"];

    // Insertar el módulo en la tabla de módulos
    $sql_modulo = "INSERT INTO modulos (nombre) VALUES ('$nombre_modulo')";
    if ($conn->query($sql_modulo) === TRUE) {
        $id_modulo = $conn->insert_id;

        // Insertar las asignaturas del 1º Curso en la tabla de asignaturas
        foreach ($asignaturas_1 as $asignatura) {
            $sql_asignatura_1 = "INSERT INTO asignaturas (id_modulo, id_curso, nombre) VALUES ($id_modulo, 1, '$asignatura')";
            $conn->query($sql_asignatura_1);
        }

        // Insertar las asignaturas del 2º Curso en la tabla de asignaturas
        foreach ($asignaturas_2 as $asignatura) {
            $sql_asignatura_2 = "INSERT INTO asignaturas (id_modulo, id_curso, nombre) VALUES ($id_modulo, 2, '$asignatura')";
            $conn->query($sql_asignatura_2);
        }

        $message = '<div class="alert alert-success" role="alert">Módulo y asignaturas creados exitosamente.</div>';
    } else {
        $message = '<div class="alert alert-danger" role="alert">Error al crear el módulo: ' . $conn->error . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./estilos/styles.css?v=6" id="themeStylesheet">
    <title>Crear Nuevo Usuario</title>
    <style>
        body {
            font-family: 'Bangers', cursive;
            background-color: #f8f9fa;
        }
        .navbar {
            padding-left: 0 !important; /* Eliminar el padding a la izquierda */
            padding-right: 10px !important; /* Eliminar el padding a la derecha */
            margin-top: 0 !important; /* Eliminar el margen superior */
        }

        .form-container2 {
            background-color: #CACCCC;
            margin-top: 50px;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            border: none;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            color: white;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
        }

        #themeIcon {
            width: 28px; /* Ajustar el ancho */
            height: 25px; /* Ajustar la altura */
            margin-left: 11px;
            margin-right: 10px;
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
        #togglePassword {
            height: 20px;
            top: 0;
            right: 0;
            bottom: 0;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
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
                <a href="../scripts/cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<div class="container2 form-container2">
    <h1 class="text-center">Crear Nuevo Módulo</h1>
    <!-- Mostrar mensaje de éxito o error -->
    <?php if ($message != ''): ?>
        <div class="text-center"><?php echo $message; ?></div>
    <?php endif; ?>
    <!-- Formulario para crear un nuevo módulo -->
    <form method="post">
        <div class="mb-3">
            <label for="nombreModulo" class="form-label">Nombre del Módulo:</label>
            <input type="text" class="form-control" id="nombreModulo" name="nombreModulo" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Asignaturas para el 1º Curso:</label>
            <div id="asignaturasContainer_1">
                <input type="text" class="form-control mb-2" name="asignaturas_1[]" required>
            </div>
            <button type="button" class="btn btn-primary mb-2" onclick="agregarAsignatura('asignaturasContainer_1')">Agregar Asignatura</button>
        </div>
        <div class="mb-3">
            <label class="form-label">Asignaturas para el 2º Curso:</label>
            <div id="asignaturasContainer_2">
                <input type="text" class="form-control mb-2" name="asignaturas_2[]" required>
            </div>
            <button type="button" class="btn btn-primary mb-2" onclick="agregarAsignatura('asignaturasContainer_2')">Agregar Asignatura</button>
        </div>

        <div class="btn-container">
            <button type="submit" class="btn btn-primary">Crear Módulo</button>
        </div>
    </form>
</div>

<!-- Scripts al final del body -->
<script>
    function agregarAsignatura(containerId) {
        var container = document.getElementById(containerId);
        var newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.className = 'form-control mb-2';
        newInput.name = containerId.replace('asignaturasContainer_', '') === '1' ? 'asignaturas_1[]' : 'asignaturas_2[]'; // Asignar el nombre correcto según el contenedor
        newInput.required = true;
        var button = container.querySelector('button'); // Obtener el botón "Agregar Asignatura"
        container.insertBefore(newInput, button); // Insertar el nuevo campo antes del botón "Agregar Asignatura"
    }
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