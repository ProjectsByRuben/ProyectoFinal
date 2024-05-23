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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=6" id="themeStylesheet">
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
    
<div class="container2 form-container2">
    <h1 class="text-center">Crear Nuevo Usuario</h1>
    <!-- Procesar la subida del formulario de creación de usuarios -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["usuario"]) && isset($_POST["contrasena"]) && isset($_POST["tipo"])) {
        $usuario = $_POST["usuario"];
        $contrasena = $_POST["contrasena"];
        $tipo = $_POST["tipo"];
        $hashed_password = hash('sha256', $contrasena);

        // Verificar si el tipo de usuario es administrador y establecer el id_modulo en NULL si es así
        $id_modulo = ($tipo === 'admin') ? 'NULL' : $_POST["id_modulo"];

        // Insertar los datos del nuevo usuario en la base de datos
        $sql = "INSERT INTO usuarios (usuario, id_modulo, contraseña, tipo) VALUES ('$usuario', $id_modulo, '$hashed_password', '$tipo')";
        if ($conn->query($sql) === TRUE) {
            echo '<div class="alert alert-success" role="alert">Usuario creado exitosamente.</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error al crear el usuario: ' . $conn->error . '</div>';
        }
    }
    ?>
    <?php
// Consultar los módulos disponibles
$sql_modulos = "SELECT id_modulo, nombre FROM modulos";
$result_modulos = $conn->query($sql_modulos);
?>
    <form method="post">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario:</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="mb-3">
            <label for="contrasena" class="form-label">Contraseña:</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <img id="eyeIcon" src="./img/cerrado.png" alt="Mostrar" style="width: 28px; height: 42px; padding-top: 0px; padding-bottom: 15px;">
                    </button>
                </div>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo:</label>
            <select class="form-select" id="tipo" name="tipo" required>
                <option value="" disabled selected hidden>Seleccionar...</option>
                <option value="alumno">Alumno</option>
                <option value="profesor">Profesor</option>
                <option value="admin">Administrador</option>
            </select>
            <small><p>(Si es admin, el módulo debe estar en "Ninguno")</p></small>
        </div>
        <div class="mb-3">
            <label for="id_modulo" class="form-label">Módulo:</label>
            <select class="form-select" id="id_modulo" name="id_modulo" required>
            <option value="" disabled selected hidden>Seleccionar...</option>
                <?php while ($row_modulo = $result_modulos->fetch_assoc()): ?>
                    <option value="<?php echo $row_modulo['id_modulo']; ?>"><?php echo $row_modulo['nombre']; ?></option>
                <?php endwhile; ?>
                <option value="NULL">Ninguno</option> <!-- Opción para NULL -->
            </select>
        </div>

        <div class="btn-container">
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        var passwordInput = document.getElementById('contrasena');
        var eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = './img/abierto.png';
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = './img/cerrado.png';
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