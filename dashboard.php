<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
  // Redirigir al usuario a la página de index.php
  header("Location: index.php");
  exit(); // Detener la ejecución del script después de redirigir
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        /* Estilos personalizados */
        .carousel-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px; /* Espacio arriba */
        }

        .carousel-title {
            font-size: 2rem; /* Tamaño del título */
            margin-bottom: 20px; /* Espacio debajo del título */
        }

        .carousel-description {
            font-size: 1.2rem; /* Tamaño de la descripción */
            text-align: center; /* Alineación del texto */
            max-width: 80%; /* Ancho máximo del contenedor de la descripción */
            margin-bottom: 40px; /* Espacio debajo de la descripción */
        }

        /* Estilos personalizados para el botón de la ventana modal */
        .modal-button {
            margin-left: auto; /* Mover el botón hacia la derecha */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="./img/logo.png" alt="Bootstrap" width="80" height="80">
    <div class="container-fluid">
        <a class="navbar-brand" href="./dashboard.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./ejercicios.php">Ejercicios</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="./soluciones.php">Soluciones</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Usuario
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

<div class="carousel-container">
    <h1 class="carousel-title">Ejercicios ASIR</h1>
    <p class="carousel-description">Aquí podrás mejorar tu destreza realizando una serie de ejercicios de diferentes asignaturas de ASIR. No te preocupes si no sabes realizar algún ejercicio, todos ellos tienen la solución pero te recomendamos no mirarla hasta el final.</p>
    <div id="carouselExampleIndicators" class="carousel slide" style="width: 500px; height: 500px;">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" aria-label="Slide 5"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./img/aso.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./img/iaw.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./img/asgbd.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./img/sad.png" class="d-block w-100" alt="...">
            </div>
            <div class="carousel-item">
                <img src="./img/sri.png" class="d-block w-100" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</html>