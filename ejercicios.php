<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios</title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        /* Estilos para el contenedor de ejercicios */
        .ejercicios-container {
            display: flex;
            flex-wrap: wrap; /* Para que los elementos se envuelvan en múltiples líneas si no hay suficiente espacio */
            gap: 20px; /* Espacio entre los ejercicios */
            justify-content: space-evenly; /* Para distribuir el espacio disponible entre los ejercicios */
        }
        
        /* Estilos para el botón "Ver Solución" */
        .card-body .btn-primary {
            display: block;
            margin: auto; /* Centrar horizontalmente */
        }

        /* Estilos para el botón de cerrar sesión */
        .btn-cerrar-sesion {
            display: inline-block;
            margin-top: 10px; /* Espacio superior */
            font-size: 14px; /* Tamaño de fuente más pequeño */
        }
    </style>
</head>
<body>

<?php
session_start();

$tipo_usuario = $_SESSION['tipo'];

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proyecto_asignaturas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los ejercicios
$sql = "SELECT * FROM ejercicios";
$result = $conn->query($sql);
?>

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
                <?php if ($tipo_usuario === 'profesor'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./crear_ejercicio.php">Crear Ejercicio</a>
                    </li>
                <?php endif; ?>
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

<h2>Ejercicios Disponibles</h2>

<div class="ejercicios-container">
<?php
// Array asociativo para mapear id_asignatura con la ruta de la imagen correspondiente
$imagenes_por_asignatura = array(
    1 => "./img/3.png",   // iaw.png para id_asignatura 1
    2 => "./img/4.png",   // sad.png para id_asignatura 2
    3 => "./img/5.png",   // sri.png para id_asignatura 3
    4 => "./img/1.png", // asgbd.png para id_asignatura 4
    5 => "./img/2.png"    // aso.png para id_asignatura 5
);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_asignatura = $row['id_asignatura'];
        // Verificar si el id_asignatura existe en el array de imágenes por asignatura
        if (array_key_exists($id_asignatura, $imagenes_por_asignatura)) {
            $imagen_path = $imagenes_por_asignatura[$id_asignatura];
        } else {
            // Si el id_asignatura no tiene una imagen asociada, se usa una imagen por defecto
            $imagen_path = "./img/default.png";  // Ruta de la imagen por defecto
        }
        
        echo "<div class='card' style='width: 18rem;'>";
        echo "<img src='$imagen_path' class='card-img-top' alt='Asignatura Imagen'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>{$row['titulo']}</h5>"; // Aquí se imprime el enunciado del ejercicio
        echo "<p class='card-text'></p>";
        echo "<a href='ver_solucion.php?id={$row['id_ejercicio']}' class='btn'><button type='button' class='btn btn-success'>Ver Solución</button></a> <br>";
        echo "<a href='solucion.php?id={$row['id_ejercicio']}' class='btn'><button type='button' class='btn btn-warning'>Intentar</button></a> <br>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No hay ejercicios disponibles.</p>";
}
?>

</div>

<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>