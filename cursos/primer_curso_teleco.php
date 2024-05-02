<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: ../index.php");
    exit();
}

include '../scripts/conexion.php'; // Incluye el archivo de conexión

$tipo_usuario = $_SESSION['tipo'];

// Verifica si se proporcionó un ID de asignatura en la URL
if (isset($_GET['asignatura_id']) && !empty($_GET['asignatura_id'])) {
    $asignatura_id = $_GET['asignatura_id'];

    // Consulta para obtener los datos de la asignatura seleccionada
    $sql_asignatura = "SELECT nombre FROM asignaturas WHERE id_asignatura = $asignatura_id";
    $result_asignatura = $conn->query($sql_asignatura);

    if ($result_asignatura->num_rows > 0) {
        $row_asignatura = $result_asignatura->fetch_assoc();
        $nombre_asignatura = $row_asignatura['nombre'];
    } else {
        // Si no se encuentra la asignatura, redirige
        header("Location: asignaturas_asir_primero.php");
        exit;
    }

    // Consulta para obtener los ejercicios de la asignatura seleccionada
    $sql = "SELECT * FROM ejercicios WHERE id_asignatura = $asignatura_id";
    $result = $conn->query($sql);
} else {
    // Redirige si no se proporcionó un ID de asignatura válido
    header("Location: asignaturas_asir_primero.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios de <?php echo $nombre_asignatura; ?> - 1º Curso ASIR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css?v=2" id="themeStylesheet">
    <style>
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
    <img src="../img/logo.png" alt="Bootstrap" width="140" height="90">
    <div class="container-fluid">
        <a class="nav-link active" aria-current="page" href="javascript:history.back()">
            <img src="../img/flecha.png" class="img-fluid" style="max-width: 30px;" alt="Flecha">
            <span style='margin: 0 10px;'></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="../dashboard.php">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="../modulos.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Modulos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../modulos.php">Modulos</a></li>
                        <li><a class="dropdown-item" href="../asignaturas/asignaturas_asir_primero.php">1º Asir</a></li>
                        <li><a class="dropdown-item" href="../asignaturas/asignaturas_asir_segundo.php">2º Asir</a></li>
                        <li><a class="dropdown-item" href="../asignaturas/asignaturas_teleco_primero.php">1º Teleco</a></li>
                        <li><a class="dropdown-item" href="../asignaturas/asignaturas_teleco_segundo.php">2º Teleco</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../soluciones.php">Soluciones</a>
                </li>
                <?php if ($tipo_usuario === 'profesor'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../crear_ejercicio.php">Crear Ejercicio</a>
                    </li>
                <?php endif; ?>
            </ul>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal">Sesion</button>
            <button id="themeButton" onclick="toggleTheme()" class="btn">
        <img id="themeIcon" src="../img/<?php echo $currentTheme === 'dark' ? 'sun' : 'moon'; ?>.png" alt="<?php echo $currentTheme === 'dark' ? 'moon' : 'sun'; ?>">
    </button>
        </div>
    </div>
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
                <a href="../cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>


<!-- Contenido principal -->
<div class="container mt-4">
    <h2>Ejercicios de <?php echo $nombre_asignatura; ?> - 1º Curso TELECO</h2>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determinar el color según la dificultad del ejercicio
            $dificultad_color = '';
            switch ($row['dificultad']) {
                case 'facil':
                    $dificultad_color = 'text-success'; // Verde
                    break;
                case 'medio':
                    $dificultad_color = 'text-warning'; // Amarillo
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
            echo "<a href='../eliminar_ejercicio.php?id={$row['id_ejercicio']}' class='btn'><button type='button' class='btn btn-danger'>Eliminar Ejercicio</button></a>";
            endif;
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p class='text-muted'>No hay ejercicios disponibles para esta asignatura.</p>";
    }
    ?>
</div>

<script>
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme); // Guarda el tema seleccionado en el almacenamiento local

        // Actualiza la imagen del botón después de cambiar el tema
        const themeIcon = document.getElementById('themeIcon');
        themeIcon.src = `../img/${newTheme === 'dark' ? 'sun' : 'moon'}.png`;
    }

    // Aplica el tema almacenado en localStorage al cargar la página
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
    }

    // Actualiza la imagen del botón según el tema actual al cargar la página
    const themeIcon = document.getElementById('themeIcon');
    themeIcon.src = `../img/${currentTheme === 'dark' ? 'sun' : 'moon'}.png`;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>