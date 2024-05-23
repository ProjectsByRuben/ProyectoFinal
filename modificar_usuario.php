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

$error_message = ''; // Variable para almacenar el mensaje de error
$mensaje = '';

// Procesar el formulario de edición cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilar los datos del formulario
    $nuevo_usuario = $_POST['usuario'];
    $cambiar_contrasena = isset($_POST['cambiar_contrasena']) && $_POST['cambiar_contrasena'] === 'si';
    
    // Solo actualizar la contraseña si se ha indicado
    if ($cambiar_contrasena) {
        $nueva_contrasena = $_POST['contrasena'];
        $confirmar_contrasena = $_POST['confirmar_contrasena'];
        if ($nueva_contrasena !== $confirmar_contrasena) {
            $error_message = '<div class="alert alert-danger" role="alert">Las contraseñas no coinciden.</div>';
        } else {
            $hashed_password = hash('sha256', $nueva_contrasena);
        }
    } else {
        $hashed_password = $usuario['contraseña'];
    }
    
    if (empty($error_message)) {
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
        $update_sql = "UPDATE usuarios SET usuario = '$nuevo_usuario', contraseña = '$hashed_password', tipo = '$nuevo_tipo', id_modulo = " . ($nuevo_modulo !== null ? "'$nuevo_modulo'" : "NULL") . " WHERE id_usuario = $id_usuario";
        if ($conn->query($update_sql) === TRUE) {
            $mensaje = '<div class="alert alert-success" role="alert">Usuario modificado exitosamente.</div>';
            // Redirigir a la misma página para mostrar el mensaje
            header("Location: modificar_usuario.php?id=$id_usuario&mensaje=modificado");
            exit();
        } else {
            $error_message = "Error al actualizar el usuario: " . $conn->error;
        }
    }
}

// Mostrar el mensaje de éxito si existe en la URL
if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'modificado') {
    $mensaje = '<div class="alert alert-success" role="alert">Usuario modificado exitosamente.</div>';
}

// Obtener los módulos disponibles
$sql_modulos = "SELECT * FROM modulos";
$resultado_modulos = $conn->query($sql_modulos);

// Variable para almacenar las opciones de módulo
$options_modulos = '';

if ($resultado_modulos->num_rows > 0) {
    // Recorrer los resultados y generar las opciones
    while ($row = $resultado_modulos->fetch_assoc()) {
        // Verificar si este módulo está asociado al usuario actual
        $selected = ($row['id_modulo'] == $usuario['id_modulo']) ? 'selected' : '';
        $options_modulos .= '<option value="' . $row['id_modulo'] . '" ' . $selected . '>' . $row['nombre'] . '</option>';
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
    <link rel="stylesheet" href="./styles.css?v=22" id="themeStylesheet">
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
        .container2 {
            margin-top: 50px;
            max-width: 400px;
            background-color: #CACCCC;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
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
                <a href="cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<div class="container2">
    <form method="post">
        <h1 class="text-center">Modificar Usuario</h1>
        <?php echo $error_message ?>
        <?php echo $mensaje ?>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo $usuario['usuario']; ?>">
        </div>
        <input type="hidden" name="cambiar_contrasena" id="cambiar_contrasena" value="no">
        <div class="mb-3">
            <button type="button" class="btn btn-secondary" id="changePasswordButton">¿Cambiar Contraseña?</button>
        </div>
        <div class="mb-3" id="passwordContainer">
            <label for="contrasena" class="form-label">Nueva Contraseña</label>
            <div class="input-group">
                <input type="password" class="form-control" id="contrasena" name="contrasena">
                <button class="btn btn-outline-secondary" type="button" id="toggleNuevaPassword">
                    <img id="eyeNuevaIcon" src="./img/cerrado.png" alt="Mostrar" style="width: 28px; height: 42px; padding-top: 0px; padding-bottom: 15px;">
                </button>
            </div>
            <div class="mb-3">
                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirmarContrasena" name="confirmar_contrasena">
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmarPassword">
                        <img id="eyeConfirmarIcon" src="./img/cerrado.png" alt="Mostrar" style="width: 28px; height: 42px; padding-top: 0px; padding-bottom: 15px;">
                    </button>
                </div>
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
                <?php echo $options_modulos; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<script>
    document.getElementById('toggleNuevaPassword').addEventListener('click', function() {
    var passwordInput = document.getElementById('contrasena');
    var eyeIcon = document.getElementById('eyeNuevaIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.src = './img/abierto.png';
    } else {
        passwordInput.type = 'password';
        eyeIcon.src = './img/cerrado.png';
    }
});

document.getElementById('toggleConfirmarPassword').addEventListener('click', function() {
    var passwordInput = document.getElementById('confirmarContrasena');
    var eyeIcon = document.getElementById('eyeConfirmarIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.src = './img/abierto.png';
    } else {
        passwordInput.type = 'password';
        eyeIcon.src = './img/cerrado.png';
    }
});

document.getElementById('changePasswordButton').addEventListener('click', function() {
    var passwordContainer = document.getElementById('passwordContainer');
    passwordContainer.style.display = 'block';
    this.style.display = 'none';
    document.getElementById('cambiar_contrasena').value = 'si'; // Actualiza el valor a 'si'
});

// Agregar validación al enviar el formulario
document.querySelector('form').addEventListener('submit', function(event) {
    var cambiarContrasena = document.getElementById('cambiar_contrasena').value;
    if (cambiarContrasena === 'si') {
        var nuevaContrasena = document.getElementById('contrasena').value;
        var confirmarContrasena = document.getElementById('confirmarContrasena').value;
        if (nuevaContrasena === '' || confirmarContrasena === '') {
            event.preventDefault(); // Evitar que se envíe el formulario

            // Resaltar los campos de contraseña
            document.getElementById('contrasena').style.backgroundColor = '#a92f2f';
            document.getElementById('confirmarContrasena').style.backgroundColor = '#a92f2f';

            // Mostrar mensaje al lado de los campos
            var mensaje = document.createElement('p');
            mensaje.textContent = 'Por favor, complete ambos campos de contraseña.';
            mensaje.style.color = '#dc3545';
            mensaje.style.fontSize = '14px';
            mensaje.classList.add('warning-message');
            var passwordContainer = document.getElementById('passwordContainer');
            passwordContainer.appendChild(mensaje);
        }
    }
});

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