<?php
session_start();

$tipo_usuario = $_SESSION['tipo'];

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

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proyecto_asignaturas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del ejercicio desde el parámetro GET
$id_ejercicio = $_GET['id'];

// Consulta para obtener la información del ejercicio
$sql = "SELECT * FROM ejercicios WHERE id_ejercicio = $id_ejercicio";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener los datos del ejercicio
    $row = $result->fetch_assoc();
    $titulo = $row['titulo'];
    $enunciado = $row['enunciado'];
    $solucion = $row['solucion'];
} else {
    // Si no se encuentra el ejercicio, mostrar un mensaje de error y redireccionar
    echo "El ejercicio no existe.";
    exit();
}

// Guardar la solución enviada por el usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha enviado un archivo
    if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] == 0) {
        // Obtener la ruta donde se guardará el archivo
        $ruta = 'downloads/' . $_FILES['archivo']['name'];

        // Mover el archivo al directorio de descargas
        move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta);

        // Verificar si el ID de usuario está definido en la sesión
        if (isset($_SESSION['id_usuario'])) {
            $id_usuario = $_SESSION['id_usuario'];
            
            // Insertar la solución en la tabla de soluciones
            $query = "INSERT INTO soluciones (id_ejercicio, id_usuario, solucion) VALUES ('$id_ejercicio', '$id_usuario', '$ruta')";
            if ($conn->query($query) === TRUE) {
                echo '<script>alert("Respuesta enviada correctamente."); window.location.href = window.location.href.split("?")[0];</script>';
            } else {
                echo '<script>alert("Error al enviar la respuesta.");</script>';
            }
        } else {
            echo '<script>alert("El ID de usuario no está definido en la sesión.");</script>';
        }
    } else {
        echo '<script>alert("Error al subir el archivo.");</script>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Ejercicio</title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        #drop-area {
            border: 2px dashed #ccc;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            margin: 20px auto;
            width: 60%;
            cursor: pointer;
        }
        #file-list {
            margin-top: 20px;
        }
        .file-item {
            margin-bottom: 10px;
        }
        .file-item button {
            margin-left: 10px;
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

<h1><?php echo $titulo; ?></h1>
<h2>Enunciado:</h2>
<p><?php echo $enunciado; ?></p>

<div id="drop-area" onclick="document.getElementById('fileElem').click();" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
    <p>Haz clic aquí o arrastra tus archivos para subirlos</p>
    <input type="file" id="fileElem" name="archivo" multiple accept="*" style="display:none;">
</div>

<div id="file-list">
    <p>Archivos seleccionados:</p>
</div>

<br>
<a href="ejercicios.php">Volver a la lista de ejercicios</a>
<br>
<a href="cerrar_sesion.php">Cerrar Sesión</a>

<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
    document.getElementById('fileElem').onchange = function(event) {
        var fileList = event.target.files;
        var filesContainer = document.getElementById('file-list');
        filesContainer.innerHTML = '';

        for (var i = 0; i < fileList.length; i++) {
            var file = fileList[i];
            var fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.textContent = file.name;

            var sendButton = document.createElement('button');
            sendButton.textContent = 'Enviar';
            sendButton.onclick = function(file) {
                return function() {
                    var formData = new FormData();
                    formData.append('archivo', file);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_ejercicio; ?>', true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            alert('Respuesta enviada correctamente.');
                            console.log('Archivo enviado correctamente');
                            location.reload();
                        } else {
                            alert('Error al enviar la respuesta.');
                            console.error('Error al enviar el archivo');
                        }
                    };
                    xhr.send(formData);
                };
            }(file);

            fileItem.appendChild(sendButton);
            filesContainer.appendChild(fileItem);
        }
    };

    function dragOverHandler(event) {
        event.preventDefault();
        document.getElementById('drop-area').classList.add('highlight');
    }

    function dropHandler(event) {
        event.preventDefault();
        document.getElementById('drop-area').classList.remove('highlight');

        var fileList = event.dataTransfer.files;
        var filesContainer = document.getElementById('file-list');
        filesContainer.innerHTML = '';

        for (var i = 0; i < fileList.length; i++) {
            var file = fileList[i];
            var fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.textContent = file.name;

            var sendButton = document.createElement('button');
            sendButton.textContent = 'Enviar';
            sendButton.onclick = function(file) {
                return function() {
                    var formData = new FormData();
                    formData.append('archivo', file);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_ejercicio; ?>', true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            alert('Respuesta enviada correctamente.');
                            console.log('Archivo enviado correctamente');
                            location.reload();
                        } else {
                            alert('Error al enviar la respuesta.');
                            console.error('Error al enviar el archivo');
                        }
                    };
                    xhr.send(formData);
                };
            }(file);

            fileItem.appendChild(sendButton);
            filesContainer.appendChild(fileItem);
        }

        document.getElementById('file-form').style.display = 'block';
    }
</script>
</body>
</html>
