<?php
session_start();
include './scripts/conexion.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];

// Verificar si se ha proporcionado un ID de módulo para editar
if (!isset($_GET['id_modulo'])) {
    header("Location: modulos.php");
    exit();
}

$id_modulo = $_GET['id_modulo'];

// Obtener los datos del módulo actual
$sql_modulo = "SELECT * FROM modulos WHERE id_modulo = $id_modulo";
$resultado_modulo = $conn->query($sql_modulo);

if ($resultado_modulo->num_rows == 0) {
    header("Location: modulos.php");
    exit();
}

$modulo = $resultado_modulo->fetch_assoc();
$nombre_modulo = $modulo['nombre'];

// Obtener las asignaturas del módulo
$sql_asignaturas = "SELECT * FROM asignaturas WHERE id_modulo = $id_modulo ORDER BY nombre";
$resultado_asignaturas = $conn->query($sql_asignaturas);

// Agrupar las asignaturas por curso
$asignaturas_por_curso = [];
while ($row = $resultado_asignaturas->fetch_assoc()) {
    $asignaturas_por_curso[$row['id_curso']][] = $row;
}

// Procesar el formulario de edición cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilar los datos del formulario y actualizar las asignaturas existentes
    if (isset($_POST['asignaturas'])) {
        foreach ($_POST['asignaturas'] as $id_asignatura => $nombre) {
            $nombre = $conn->real_escape_string($nombre);
            $update_sql = "UPDATE asignaturas SET nombre = '$nombre' WHERE id_asignatura = $id_asignatura";
            $conn->query($update_sql);
        }
    }

    // Agregar nuevas asignaturas
    if (isset($_POST['nuevas_asignaturas'])) {
        foreach ($_POST['nuevas_asignaturas'] as $id_curso => $nombres) {
            foreach ($nombres as $nombre) {
                if (!empty($nombre)) {
                    $nombre = $conn->real_escape_string($nombre);
                    $insert_sql = "INSERT INTO asignaturas (id_modulo, id_curso, nombre) VALUES ($id_modulo, $id_curso, '$nombre')";
                    $conn->query($insert_sql);
                }
            }
        }
    }

    // Redirigir para evitar reenvío del formulario
    header("Location: modificar_modulo.php?id_modulo=$id_modulo&mensaje=modificado");
    exit();
}

// Mostrar el mensaje de éxito si existe en la URL
$mensaje = '';
if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'modificado') {
    $mensaje = '<div class="alert alert-success" role="alert">Asignaturas modificadas exitosamente.</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./estilos/styles.css?v=22" id="themeStylesheet">
    <style>
    body {
        font-family: 'Bangers', cursive;
        background-color: #f8f9fa;
    }
    .navbar {
        padding-left: 0 !important;
        padding-right: 10px !important;
        margin-top: 0 !important;
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
    .container-wrapper {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap; /* Permite que los contenedores se envuelvan en una nueva línea si no caben en una sola fila */
    }

    .container2 {
        width: calc(50% - 10px); /* Restar el margen entre los contenedores */
        background-color: #CACCCC;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        margin-bottom: 20px; /* Espacio entre los contenedores */
    }

    .container2 h1 {
        text-align: center; /* Centrar el texto */
    }

    .form-label {
        font-weight: bold;
    }
    .btn-primary {
        color: white;
    }
    .form-control {
        margin-bottom: 15px;
    }
    .input-group {
        position: relative;
    }
    #toggleNuevaPassword {
        top: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 5px;
    }
    #toggleConfirmarPassword {
        top: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 5px;
    }
    .warning-message {
        margin-top: 10px;
        font-size: 14px;
        color: #dc3545;
    }
    #passwordContainer {
        display: none; /* Ocultar inicialmente */
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

<div style="width: 100%; height: 30px;"><?php echo $mensaje; ?></div>
<div class="container-wrapper">

    <div class="container2">
        <h1>Modificar Asignaturas</h1>
        <!-- Formulario para modificar asignaturas -->
        <form method="post" action="../scripts/modificar_asignaturas.php?id_modulo=<?php echo $id_modulo; ?>">
            <?php foreach ([1 => '1º Curso', 2 => '2º Curso'] as $id_curso => $nombre_curso): ?>
                <div class="container mt-4">
                    <h2><?php echo $nombre_curso; ?></h2>
                    <?php if (isset($asignaturas_por_curso[$id_curso])): ?>
                        <h3 class="mt-3">Asignaturas:</h3>
                        <?php foreach ($asignaturas_por_curso[$id_curso] as $asignatura): ?>
                            <div class="mb-3 d-flex align-items-center" id="asignatura_<?php echo $asignatura['id_asignatura']; ?>">
                                <input type="text" class="form-control me-2" name="asignaturas[<?php echo $asignatura['id_asignatura']; ?>]" value="<?php echo $asignatura['nombre']; ?>" placeholder="Nombre de la asignatura">
                                <input type="hidden" name="id_modulo" value="<?php echo $id_modulo; ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay asignaturas en este curso.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <!-- Botón de modificar -->
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Modificar</button>
            </div>
        </form>
    </div>

    <div class="container2">
        <h1>Agregar Nuevas Asignaturas</h1>
        <!-- Formulario para añadir nueva asignatura -->
        <form method="post" action="../scripts/modificar_asignaturas.php?id_modulo=<?php echo $id_modulo; ?>">
            <?php foreach ([1 => '1º Curso', 2 => '2º Curso'] as $id_curso => $nombre_curso): ?>
                <div class="container mt-4">
                    <h2><?php echo $nombre_curso; ?></h2>
                    <h3 class="mt-3">Añadir nueva asignatura</h3>
                    <div id="nuevas_asignaturas_<?php echo $id_curso; ?>">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="nuevas_asignaturas[<?php echo $id_curso; ?>][]">
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary" onclick="agregarCampo(<?php echo $id_curso; ?>)">Añadir otra asignatura</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <div class="container2">
        <h1>Eliminar Asignaturas</h1>
        <!-- Formulario para eliminar asignaturas -->
        <form method="post" action="./scripts/eliminar_asignatura.php">
            <?php foreach ([1 => '1º Curso', 2 => '2º Curso'] as $id_curso => $nombre_curso): ?>
                <div class="container mt-4">
                    <h2><?php echo $nombre_curso; ?></h2>
                    <?php if (isset($asignaturas_por_curso[$id_curso])): ?>
                        <h3 class="mt-3">Asignaturas:</h3>
                        <?php foreach ($asignaturas_por_curso[$id_curso] as $asignatura): ?>
                            <div class="mb-3 d-flex align-items-center" id="asignatura_<?php echo $asignatura['id_asignatura']; ?>">
                                <input type="text" class="form-control me-2" name="asignaturas[<?php echo $asignatura['id_asignatura']; ?>]" value="<?php echo $asignatura['nombre']; ?>" placeholder="Nombre de la asignatura">
                                <button type="submit" name="eliminar_asignatura" value="<?php echo $asignatura['id_asignatura']; ?>" class="btn btn-danger">Eliminar</button>
                                <input type="hidden" name="id_modulo" value="<?php echo $id_modulo; ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay asignaturas en este curso.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </form>
    </div>

</div>

<script>
function agregarCampo(curso) {
        const div = document.createElement('div');
        div.classList.add('mb-3');
        div.innerHTML = '<input type="text" class="form-control" name="nuevas_asignaturas[' + curso + '][]" placeholder="Nombre de la nueva asignatura">';
        document.getElementById('nuevas_asignaturas_' + curso).appendChild(div);
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);

        const themeIcon = document.getElementById('themeIcon');
        themeIcon.src = `./img/${newTheme === 'dark' ? 'sun' : 'moon'}.png`;
    }

    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
    }

    const themeIcon = document.getElementById('themeIcon');
    themeIcon.src = `./img/${currentTheme === 'dark' ? 'sun' : 'moon'}.png`;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>