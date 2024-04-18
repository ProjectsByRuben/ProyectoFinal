<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

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
    <link rel="stylesheet" href="./styles.css?v=1" id="themeStylesheet">
    <title>Crear Ejercicio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            padding-left: 0 !important; /* Eliminar el padding a la izquierda */
            padding-right: 10px !important; /* Eliminar el padding a la derecha */
            margin-top: 0 !important; /* Eliminar el margen superior */
        }

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

        /* Estilos para el textarea en el modo oscuro */
        [data-theme="dark"] textarea {
            background-color: #43494E !important; /* Color de fondo en modo oscuro */
            color: #ffffff; /* Color del texto en modo oscuro */
            border-style: none; /* Borde en modo oscuro */
        }

        /* Establece el color de fondo específico según el tema */
        [data-theme="light"] textarea.form-control:focus {
            background-color: #ffffff !important; /* Fondo blanco en modo claro */
            color: black !important;
        }

        [data-theme="dark"] textarea.form-control:focus {
            background-color: #43494E !important; /* Fondo oscuro en modo oscuro */
            color: white;
            border-style: none;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="../img/ejercitacode3.png" alt="Bootstrap" width="80" height="80">
    <div class="container-fluid">
        <a class="nav-link active" aria-current="page" href="./asignaturas/asignaturas_asir_primero.php">
            <img src="./img/flecha.png" class="img-fluid" style="max-width: 30px;" alt="Flecha">
            <span style='margin: 0 10px;'></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="./dashboard.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./modulos.php">Modulos</a>
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

    // Obtener asignaturas disponibles
    $sql_asignaturas = "SELECT id_asignatura, nombre FROM asignaturas";
    $result_asignaturas = $conn->query($sql_asignaturas);

    // Procesar la subida de archivos
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo"]) && isset($_FILES["enunciado_archivo"])) {
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

        // Guardar la ruta de los archivos en la base de datos
        $sql = "INSERT INTO ejercicios (id_asignatura, titulo, enunciado, enunciado_archivo, dificultad, solucion) VALUES ('$asignatura_id', '$titulo', '$enunciado', '', '$dificultad', '')";
        if ($conn->query($sql) === TRUE) {
            // Obtener el ID del ejercicio recién creado
            $nuevo_id_ejercicio = $conn->insert_id;

            // Procesar el archivo del enunciado después de haber obtenido $nuevo_id_ejercicio
            $extension_enunciado = strtolower(pathinfo($_FILES["enunciado_archivo"]["name"], PATHINFO_EXTENSION));
            if (in_array($extension_enunciado, array("jpg", "jpeg", "png", "gif", "pdf"))) {
                // Obtener el nombre del archivo del enunciado
                $nombre_enunciado = $nuevo_id_ejercicio . "." . $extension_enunciado;

                // Mover el archivo del enunciado a la carpeta de enunciados
                $ruta_enunciado = "enunciados/" . $nombre_enunciado;
                move_uploaded_file($_FILES["enunciado_archivo"]["tmp_name"], $ruta_enunciado);

                // Actualizar la ruta del archivo de enunciado en la base de datos
                $sql_update_enunciado = "UPDATE ejercicios SET enunciado_archivo='$ruta_enunciado' WHERE id_ejercicio=$nuevo_id_ejercicio";
                if ($conn->query($sql_update_enunciado) !== TRUE) {
                    echo '<div class="alert alert-danger" role="alert">Error al actualizar la ruta del archivo de enunciado: ' . $conn->error . '</div>';
                }
            }

            // Construir el nombre del archivo de la solución
            $extension = strtolower(pathinfo($_FILES["archivo"]["name"], PATHINFO_EXTENSION));
            $nombre_archivo = $nuevo_id_ejercicio . "." . $extension;

            // Mover el archivo a la carpeta de ejercicios
            $ruta_archivo = $directorio . $nombre_archivo;
            move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta_archivo);

            // Actualizar la ruta de la solución en la base de datos
            $sql_update_ruta = "UPDATE ejercicios SET solucion='$ruta_archivo' WHERE id_ejercicio=$nuevo_id_ejercicio";
            if ($conn->query($sql_update_ruta) !== TRUE) {
                echo '<div class="alert alert-danger" role="alert">Error al actualizar la ruta del archivo de solución: ' . $conn->error . '</div>';
            } else {
                echo '<div class="alert alert-success" role="alert">Ejercicio creado exitosamente.</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Error al crear el ejercicio: ' . $conn->error . '</div>';
        }
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título:</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
        <div class="mb-3">
            <label for="enunciado" class="form-label">Enunciado:</label><br>
            <textarea class="form-control" id="enunciado" name="enunciado" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="enunciado_archivo" class="form-label">Archivo del Enunciado (Opcional):</label>
            <input type="file" class="form-control" id="enunciado_archivo" name="enunciado_archivo" accept=".jpg, .jpeg, .png, .gif, .pdf">
            <small><p>Se permiten archivos de imagen (JPG, JPEG, PNG, GIF) o PDF.</p></small>
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
            <label for="archivo" class="form-label">Archivo de la Solución:</label>
            <input type="file" class="form-control" id="archivo" name="archivo" accept=".html, .php, .pdf, .zip, .js, .css, .py" required>
            <small><p>Solo se permiten archivos HTML, PHP, PDF, JS, CSS, PY o ZIP. Si la solución consiste en varios archivos, por favor, comprímalos en un archivo ZIP.</p></small>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn btn-primary">Crear Ejercicio</button>
        </div>
    </form>
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