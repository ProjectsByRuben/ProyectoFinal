<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Verificar si se proporciona el ID del ejercicio
if (!isset($_GET['id'])) {
    // Si no se proporciona el ID del ejercicio, redireccionar a la página de ejercicios
    header("Location: ejercicios.php");
    exit();
}

// Conexión a la base de datos (omitido para simplificar)
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proyecto_asignaturas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del ejercicio desde el parámetro GET (omitido para simplificar)
$id_ejercicio = $_GET['id'];

// Consulta para obtener la solución del ejercicio (omitido para simplificar)
$sql = "SELECT solucion FROM ejercicios WHERE id_ejercicio = $id_ejercicio";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Mostrar la solución del ejercicio (omitido para simplificar)
    $row = $result->fetch_assoc();
    $solucion = htmlspecialchars($row['solucion']); // Convertir el texto HTML a entidades HTML
    $solucion2 = $row['solucion']; // Solución sin modificar
} else {
    // Si no se encuentra el ejercicio, mostrar un mensaje de error (omitido para simplificar)
    $solucion = "La solución de este ejercicio no está disponible.";
    $solucion2 = "La solución de este ejercicio no está disponible.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solución del Ejercicio</title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/styles/default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .code-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
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
        Sesión
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
                <a href="cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

    <h1>Solución del Ejercicio</h1>
    <h2>Muestra ejercicio</h2>
    <div class="code-container">
        <code class="html"><?php echo $solucion2; ?></code>
    </div>
    <h2>Codigo ejercicio</h2>
    <div class="code-container">
        <pre><code class="html"><?php echo $solucion; ?></code></pre>
    </div>
    
    <!-- Incluir script de Highlight.js para resaltar la sintaxis -->
    <script>hljs.highlightAll();</script>
    <script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
