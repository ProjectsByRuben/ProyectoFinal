<?php
session_start();
include './scripts/conexion.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];

// Verificar si se ha proporcionado un ID de usuario para editar
if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id_usuario = $_GET['id'];

// Obtener los datos del usuario actual
$sql_usuario = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
$resultado_usuario = $conn->query($sql_usuario);

if ($resultado_usuario->num_rows == 0) {
    header("Location: usuarios.php");
    exit();
}

$usuario = $resultado_usuario->fetch_assoc();

// Procesar el formulario de edición cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilar los datos del formulario
    $nuevo_usuario = $_POST['usuario'];
    $nueva_contrasena = $_POST['contrasena'];
    $nuevo_tipo = $_POST['tipo'];
    
    // Verificar si el tipo es admin
    if ($nuevo_tipo === 'admin') {
        $nuevo_modulo = null; // Si el tipo es admin, el módulo se establece en null
    } else {
        // Si el tipo no es admin, verificar si se seleccionó un módulo
        if (isset($_POST['modulo']) && $_POST['modulo'] !== '') {
            $nuevo_modulo = $_POST['modulo'];
        } else {
            $nuevo_modulo = null;
        }
    }

    // Validar y actualizar los datos en la base de datos
    $update_sql = "UPDATE usuarios SET usuario = '$nuevo_usuario', contraseña = '$nueva_contrasena', tipo = '$nuevo_tipo', id_modulo = " . ($nuevo_modulo !== null ? "'$nuevo_modulo'" : "NULL") . " WHERE id_usuario = $id_usuario";
    if ($conn->query($update_sql) === TRUE) {
        // Redireccionar al usuario de vuelta a la página de usuarios después de la edición
        header("Location: usuarios.php");
        exit();
    } else {
        // En caso de error, puedes redirigir a una página de error o mostrar un mensaje al usuario
        echo "Error al actualizar el usuario: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=2" id="themeStylesheet">
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
        .container {
            margin-top: 50px;
            max-width: 400px;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            color: white;
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
                <?php endif; ?>
                <?php if ($tipo_usuario === 'profesor'): ?>
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
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_ejercicio.php">Crear Ejercicio</a>
                    </li>
                <?php endif; ?>
                <?php if ($tipo_usuario === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./usuarios.php">Usuarios</a>
                    </li>
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
    </button></nav>

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

<div class="container">
    <h1 class="text-center">Modificar Usuario</h1>
    <form method="post">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo $usuario['usuario']; ?>">
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña</label>
            <div class="input-group">
                <input type="password" class="form-control" id="contrasena" name="contrasena" value="<?php echo $usuario['contraseña']; ?>">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <img id="eyeIcon" src="./img/abierto.png" alt="Mostrar" style="width: 25px; height: 25px;">
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de Usuario</label>
            <select class="form-select" id="tipo" name="tipo">
                <option value="alumno" <?php if($usuario['tipo'] === 'alumno') echo 'selected'; ?>>Alumno</option>
                <option value="profesor" <?php if($usuario['tipo'] === 'profesor') echo 'selected'; ?>>Profesor</option>
                <option value="admin" <?php if($usuario['tipo'] === 'admin') echo 'selected'; ?>>Admin</option>
            </select>
            <small><p>(Si es admin, el módulo debe estar en "Ninguno")</p></small>
        </div>
        <div class="mb-3">
            <label for="modulo" class="form-label">Módulo</label>
            <select class="form-select" id="modulo" name="modulo">
                <option value="" <?php if($usuario['id_modulo'] === null) echo 'selected'; ?>>Ninguno</option>
                <option value="1" <?php if($usuario['id_modulo'] == 1) echo 'selected'; ?>>Asir</option>
                <option value="2" <?php if($usuario['id_modulo'] == 2) echo 'selected'; ?>>Teleco</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        var passwordInput = document.getElementById('contrasena');
        var eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = './img/cerrado.png';
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = './img/abierto.png';
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