<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        /* Estilos personalizados */
        /* Puedes agregar estilos adicionales aquí según sea necesario */

        .form-container {
            margin: 50px auto;
            width: 80%;
            max-width: 800px;
        }

        /* Estilos adicionales para los botones */
        .btn-container {
            display: flex;
            justify-content: space-between;
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

<div class="container form-container">
    <h1>Crear Nuevo Ejercicio</h1>
    <?php
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "proyecto_asignaturas";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener asignaturas disponibles
    $sql_asignaturas = "SELECT id_asignatura, nombre FROM asignaturas";
    $result_asignaturas = $conn->query($sql_asignaturas);

    // Procesar la subida de archivos
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo"])) {
        $asignatura_id = $_POST["asignatura"];
        $titulo = $_POST["titulo"];
        $enunciado = $_POST["enunciado"];
        $dificultad = $_POST["dificultad"];

        // Directorio donde se guardarán los archivos
        $directorio = "ejercicios/";

        // Verificar si el directorio existe, si no, crearlo
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Obtener el último número de ejercicio guardado en la carpeta
        $archivos = glob($directorio . "*.*");
        $ultimo_numero = count($archivos) + 1;

        // Obtener la extensión del archivo subido
        $extension = pathinfo($_FILES["archivo"]["name"], PATHINFO_EXTENSION);

        // Construir el nombre del archivo
        $nombre_archivo = $ultimo_numero . "." . $extension;

        // Mover el archivo a la carpeta de ejercicios
        $ruta_archivo = $directorio . $nombre_archivo;
        move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta_archivo);

        // Guardar la ruta del archivo en la base de datos
        $sql = "INSERT INTO ejercicios (id_asignatura, titulo, enunciado, dificultad, solucion) VALUES ('$asignatura_id', '$titulo', '$enunciado', '$dificultad', '$ruta_archivo')";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Ejercicio creado exitosamente.");</script>';
        } else {
            echo '<script>alert("Error al crear el ejercicio: ' . $conn->error . '");</script>';
        }
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
        <div class="mb-3">
            <label for="enunciado" class="form-label">Enunciado:</label>
            <textarea class="form-control" id="enunciado" name="enunciado" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="dificultad" class="form-label">Dificultad:</label>
            <select class="form-select" id="dificultad" name="dificultad" required>
                <option value="facil">Fácil</option>
                <option value="medio">Medio</option>
                <option value="dificil">Difícil</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="asignatura" class="form-label">Asignatura:</label>
            <select class="form-select" id="asignatura" name="asignatura" required>
                <?php while ($row_asignatura = $result_asignaturas->fetch_assoc()): ?>
                    <option value="<?php echo $row_asignatura['id_asignatura']; ?>"><?php echo $row_asignatura['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="archivo" class="form-label">Seleccionar archivo:</label>
            <input type="file" class="form-control" id="archivo" name="archivo" required>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn btn-primary">Crear Ejercicio</button>
        </div>
    </form>
</div>

<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>