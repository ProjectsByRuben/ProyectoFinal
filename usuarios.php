<?php
include './scripts/conexion.php'; // Incluye el archivo de conexión
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];

// Consulta todos los usuarios disponibles en la base de datos agrupados por tipo
$sql_usuarios = "SELECT * FROM usuarios ORDER BY tipo";
$resultado_usuarios = $conn->query($sql_usuarios);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=13" id="themeStylesheet">
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
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn {
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
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

<div class="container mt-4">
    <h1 class="mb-4">Usuarios</h1>
    <!-- Verifica si hay usuarios en la base de datos -->
    <?php if ($resultado_usuarios->num_rows > 0): ?>
        <!-- Itera sobre cada tipo de usuario y muestra los usuarios agrupados -->
        <?php $tipo_anterior = ''; ?>
        <?php while ($fila = $resultado_usuarios->fetch_assoc()): ?>
            <?php if ($fila['tipo'] !== $tipo_anterior): ?>
                <?php if ($tipo_anterior !== ''): ?>
                    </div><!-- Cierra el contenedor de usuarios -->
                <?php endif; ?>
                <!-- Cambia los nombres de los tipos de usuarios -->
                <h2>
                    <?php if ($fila['tipo'] === 'alumno'): ?>
                        Alumnos
                    <?php elseif ($fila['tipo'] === 'profesor'): ?>
                        Profesores
                    <?php elseif ($fila['tipo'] === 'admin'): ?>
                        Administradores
                    <?php endif; ?>
                </h2>
                <div class="row row-cols-1 row-cols-md-3 g-4"><!-- Abre un nuevo contenedor de usuarios -->
            <?php endif; ?>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $fila['usuario']; ?></h5>
                        <!-- Botón para modificar el usuario -->
                        <a href="modificar_usuario.php?id=<?php echo $fila['id_usuario']; ?>" class="btn btn-primary">Modificar</a>
                        <!-- Botón para eliminar el usuario -->
                        <a href="eliminar_usuario.php?id=<?php echo $fila['id_usuario']; ?>" class="btn btn-danger">Eliminar</a>
                    </div>
                </div>
            </div>
            <?php $tipo_anterior = $fila['tipo']; ?>
        <?php endwhile; ?>
        </div><!-- Cierra el último contenedor de usuarios -->
    <?php else: ?>
        <p class="mt-4">No hay usuarios disponibles.</p>
    <?php endif; ?>
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