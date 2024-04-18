<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

$tipo_usuario = $_SESSION['tipo'];
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios</title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=1" id="themeStylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            padding-left: 0 !important; /* Eliminar el padding a la izquierda */
            padding-right: 10px !important; /* Eliminar el padding a la derecha */
            margin-top: 0 !important; /* Eliminar el margen superior */
            margin-bottom: 10px !important; /* Añade margen inferior de 10px */
        }
        /* Estilos para el contenedor de ejercicios */
        .ejercicios-container {
            display: flex;
            flex-wrap: wrap; /* Para que los elementos se envuelvan en múltiples líneas si no hay suficiente espacio */
            gap: 20px; /* Espacio entre los ejercicios */
            justify-content: space-evenly; /* Para distribuir el espacio disponible entre los ejercicios */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="../img/ejercitacode3.png" alt="Bootstrap" width="80" height="80">
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
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../modulos.php">Modulos</a>
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
            <button type="button" class="btn btn-primary modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal">Sesión</button>
            <button id="themeButton" onclick="toggleTheme()" class="btn btn-primary">Cambiar Tema</button>
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
                <a href="cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<h2>Modulos</h2>

<div class="ejercicios-container">
    <div class='card' style='width: 18rem;'>
        <img src='./img/asir.png' class='card-img-top'>
        <div class='card-body'>
            <h5 class='card-title'>ASIR</h5>
            <a href='./asignaturas/asignaturas_asir_primero.php' class='btn'><button type='button' class='btn btn-card'>1º Curso</button></a> <br>
            <a href='./asignaturas/asignaturas_asir_segundo.php' class='btn'><button type='button' class='btn btn-card'>2º Curso</button></a> <br>
        </div>
    </div>

    <div class='card' style='width: 18rem;'>
        <img src='./img/teleco.png' class='card-img-top'>
        <div class='card-body'>
            <h5 class='card-title'>TELECO</h5>
            <a href='./asignaturas/asignaturas_teleco_primero.php' class='btn'><button type='button' class='btn btn-card'>1º Curso</button></a> <br>
            <a href='./asignaturas/asignaturas_teleco_segundo.php' class='btn'><button type='button' class='btn btn-card'>2º Curso</button></a> <br>
        </div>
    </div>
</div>

<script>
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme); // Guarda el tema seleccionado en el almacenamiento local

        // Actualiza el texto del botón después de cambiar el tema
        const themeButton = document.getElementById('themeButton');
        themeButton.textContent = newTheme === 'dark' ? 'Claro' : 'Oscuro';
    }

    // Aplica el tema almacenado en localStorage al cargar la página
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
    }

    // Actualiza el texto del botón según el tema actual al cargar la página
    const themeButton = document.getElementById('themeButton');
    themeButton.textContent = currentTheme === 'dark' ? 'Claro' : 'Oscuro';
</script>

<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>