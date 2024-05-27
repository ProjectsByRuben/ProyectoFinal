<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: ./index.php");
    exit();
}

include './scripts/conexion.php'; // Incluye el archivo de conexión, verifica que la ruta es correcta

$tipo_usuario = $_SESSION['tipo'];
$id_modulo = $_SESSION['id_modulo'];
$id_curso = isset($_GET['id_curso']) ? $_GET['id_curso'] : null; // Captura el id_curso de la URL, maneja si no está definido
$id_asignatura = isset($_GET['asignatura_id']) ? $_GET['asignatura_id'] : null; // Captura el id_asignatura de la URL, maneja si no está definido
$nombre = isset($_GET['nombre_asignatura']) ? $_GET['nombre_asignatura'] : null; // Captura el id_asignatura de la URL, maneja si no está definido

// Verifica si id_modulo es NULL
if ($id_modulo === NULL) {
    $nombre_modulo = "Módulo Desconocido";
} else {
    // Consulta el nombre del módulo si id_modulo no es NULL
    $sql = "SELECT nombre FROM modulos WHERE id_modulo = $id_modulo";
    $resultado = $conn->query($sql);

    // Verificar si se encontró el módulo y obtener su nombre
    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $nombre_modulo = $fila["nombre"];
    } else {
        // Si no se encuentra el módulo, mostrar un mensaje de error
        $nombre_modulo = "Módulo Desconocido";
    }
}

// Consulta para obtener los ejercicios de la asignatura especificada
if ($id_asignatura !== null) {
    $sql = "SELECT id_ejercicio, titulo, enunciado, dificultad FROM ejercicios WHERE id_asignatura = $id_asignatura";
    $result = $conn->query($sql);
} else {
    $result = false; // Maneja el caso en que no se proporcionó id_asignatura
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios de <?php echo $nombre_modulo; ?> - <?php echo $id_curso; ?>º Curso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./estilos/styles.css?v=2">
    <style>
        body {
            font-family: 'Bangers', cursive;
            background-color: #f8f9fa;
        }
        .small-text {
            font-size: 16px;
            color: #228182;
        }
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
        .num-ejercicios.rojo {
            color: red;
        }
        .num-ejercicios.verde {
            color: green;
        }
        .text-success {
            color: green !important; /* Cambiar el color a naranja */
            background-color: #959999;
            display: inline-block; /* Ajusta el ancho al tamaño del texto */
            padding: 5px; /* Espacio alrededor del texto */
        }

        .text-danger {
            color: red !important; /* Cambiar el color a naranja */
            background-color: #959999;
            display: inline-block; /* Ajusta el ancho al tamaño del texto */
            padding: 5px; /* Espacio alrededor del texto */
        }

        .text-medium {
            color: orange !important; /* Cambiar el color a naranja */
            background-color: #959999;
            display: inline-block; /* Ajusta el ancho al tamaño del texto */
            padding: 5px; /* Espacio alrededor del texto */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="./img/logo.png" alt="Bootstrap" width="140" height="90">
    <div class="container-fluid">
    <a class="nav-link active" aria-current="page" href="javascript:history.back()">
        <img src="./img/flecha.png" class="img-fluid" style="max-width: 30px;" alt="Flecha">
        <span style='margin: 0 10px;'></span>
    </a>
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
                    <a class="nav-link active" aria-current="page" href="./crear_usuario.php">Crear Usuario</a>
                </li>
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
                <a href="../scripts/cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="container mt-4">
    <h2>Ejercicios de <?php echo $nombre; ?></h2>
    <div class="list-group">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Determinar el color según la dificultad del ejercicio
            $dificultad_color = '';
            switch ($row['dificultad']) {
                case 'facil':
                    $dificultad_color = 'text-success'; // Verde
                    break;
                case 'medio':
                    $dificultad_color = 'text-medium'; // Amarillo
                    break;
                case 'dificil':
                    $dificultad_color = 'text-danger'; // Rojo
                    break;
                default:
                    $dificultad_color = '';
                    break;
            }

                echo "<div class='card mb-3'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title $dificultad_color'>{$row['titulo']}</h5>";
                echo "<p class='card-text'>{$row['enunciado']}</p>";
                echo "<a href='../solucion.php?id={$row['id_ejercicio']}' class='btn btn-primary'>Intentar</a>";
                if ($tipo_usuario === 'profesor'):
                    echo "<a href='./scripts/eliminar_ejercicio.php?id={$row['id_ejercicio']}' class='btn'><button type='button' class='btn btn-danger'>Eliminar Ejercicio</button></a>";
                endif;
                echo "</div>";
                echo "</div>";            
            }
        } else {
            echo "<p class='text-muted'>No hay ejercicios disponibles.</p>";
        }
        ?>
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