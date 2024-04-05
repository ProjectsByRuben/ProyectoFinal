<?php
session_start();

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

// Obtener el ID de usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];

// Consulta para obtener las soluciones del usuario junto con el título y el ID del ejercicio correspondiente
$sql = "SELECT soluciones.*, ejercicios.titulo, ejercicios.id_ejercicio AS id_ejercicio FROM soluciones INNER JOIN ejercicios ON soluciones.id_ejercicio = ejercicios.id_ejercicio WHERE soluciones.id_usuario = $id_usuario";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soluciones del Usuario</title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <img src="./img/logo.png" alt="Bootstrap" width="80" height="80">
        <div class="container-fluid">
            <a class="navbar-brand" href="./dashboard.php">Inicio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
        <button type="button" class="btn btn-primary modal-button" data-bs-toggle="modal"
            data-bs-target="#exampleModal">
            Sesión
        </button>
    </nav>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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

    <div class="container mt-4">
        <h1>Soluciones del Usuario</h1>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $ruta_archivo = $row['solucion'];
                    $titulo = $row['titulo']; // Cambio de $enunciado a $titulo
                    $id_ejercicio = $row['id_ejercicio'];
                    ?>
                    <div class="col" data-id="<?php echo $id_ejercicio; ?>">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">ID del Ejercicio</h5>
                                <p class="card-text"><?php echo $id_ejercicio; ?></p>
                                <h5 class="card-title">Título del Ejercicio</h5> <!-- Cambio de Enunciado a Título -->
                                <p class="card-text"><?php echo $titulo; ?></p> <!-- Cambio de Enunciado a Título -->
                                <h5 class="card-title">Archivo</h5>
                                <p class="card-text"><?php echo basename($ruta_archivo); ?></p>
                                <a href="<?php echo $ruta_archivo; ?>" class="btn btn-primary" download>Descargar</a>
                                <!-- Botón de eliminar -->
                                <button class="btn btn-danger" onclick="eliminarRespuesta(<?php echo $id_ejercicio; ?>)">Eliminar</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No hay soluciones disponibles.</p>";
            }
            ?>
        </div>
    </div>

    <script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        function eliminarRespuesta(id_ejercicio) {
            if (confirm("¿Estás seguro de que deseas eliminar esta respuesta?")) {
                // Realiza una solicitud al servidor para eliminar la respuesta
                fetch('eliminar_respuesta.php?id=' + id_ejercicio, {
                    method: 'GET'
                })
                .then(response => {
                    if (response.ok) {
                        // Elimina la tarjeta de respuesta de la interfaz de usuario
                        const cardToRemove = document.querySelector(`[data-id="${id_ejercicio}"]`);
                        if (cardToRemove) {
                            cardToRemove.parentNode.removeChild(cardToRemove);
                        }
                    } else {
                        throw new Error('Error al eliminar la respuesta');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>

</html>
