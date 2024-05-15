<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

$tipo_usuario = $_SESSION['tipo'];
$id_modulo = $_SESSION['id_modulo'];

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

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

// Verificar si se proporciona el ID del ejercicio
if (!isset($_GET['id'])) {
    // Si no se proporciona el ID del ejercicio, redireccionar a la página de ejercicios
    header("Location: modulos.php");
    exit();
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

// Consulta para obtener las pistas del ejercicio desde las columnas pista1, pista2 y pista3
$sql_pistas = "SELECT pista1, pista2, pista3 FROM ejercicios WHERE id_ejercicio = $id_ejercicio";
$result_pistas = $conn->query($sql_pistas);

$pistas = [];
if ($result_pistas->num_rows > 0) {
    $row_pistas = $result_pistas->fetch_assoc();
    // Agregar las rutas de las pistas al array
    $pistas[] = $row_pistas['pista1'];
    $pistas[] = $row_pistas['pista2'];
    $pistas[] = $row_pistas['pista3'];
}

// Guardar la solución enviada por el usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha enviado un archivo
    if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] == 0) {
        // Obtener la ruta donde se guardará el archivo
        $ruta = 'soluciones/' . $_FILES['archivo']['name'];

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=2" id="themeStylesheet">
    <style>
        body {
            font-family: 'Bangers', cursive;
            background-color: #f8f9fa;
        }
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
        .navbar {
            padding-left: 0 !important; /* Eliminar el padding a la izquierda */
            padding-right: 10px !important; /* Eliminar el padding a la derecha */
            margin-top: 0 !important; /* Eliminar el margen superior */
        }
        .code-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        #pdf-viewer {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            text-align: center; /* Para centrar el visor PDF */
        }
        .pdf-navigation {
            margin-top: 20px;
            text-align: center;
        }
        h1, h2, h3 {
            color: #343a40;
        }
        .alert h3{
            font-size: 15px;
        }
        #themeIcon {
            width: 28px; /* Ajustar el ancho */
            height: 25px; /* Ajustar la altura */
            margin-left: 11px;
            margin-right: 10px;
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
        .text-center{
            margin-top: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="./img/logo.png" alt="Bootstrap" width="140" height="90">
    <div class="container-fluid">
        <a class="nav-link active" aria-current="page" href="javascript:history.back()">
            <img src="../img/flecha.png" class="img-fluid" style="max-width: 30px;" alt="Flecha">
            <span style='margin: 0 10px;'></span>
        </a>
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
                        <?php if ($id_modulo == 1): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_primero.php">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_segundo.php">2º Curso</a></li>
                        <?php elseif ($id_modulo == 2): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_primero.php">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_segundo.php">2º Curso</a></li>
                        <?php endif; ?>
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
                        <?php if ($id_modulo == 1): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_primero.php">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_asir_segundo.php">2º Curso</a></li>
                        <?php elseif ($id_modulo == 2): ?>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_primero.php">1º Curso</a></li>
                        <li><a class="dropdown-item" href="./asignaturas/asignaturas_teleco_segundo.php">2º Curso</a></li>
                        <?php endif; ?>
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
                        <a class="nav-link active" aria-current="page" href="./crear_usuario.php">Crear Usuario</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal">Sesion</button>
    <button id="themeButton" onclick="toggleTheme()" class="btn">
        <img id="themeIcon" src="./img/<?php echo $currentTheme === 'dark' ? 'sun' : 'moon'; ?>.png" alt="<?php echo $currentTheme === 'dark' ? 'moon' : 'sun'; ?>">
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

<div class="container">
    <h2 class="text-center"><?php echo $titulo; ?></h2>
    <div class="alert alert-dark" role="alert">
        <h3 class="text-center"><?php echo $enunciado; ?></h3>
    </div>
    <?php if (!empty($row['enunciado_archivo'])): ?>
        <div class="alert alert-info" role="alert">
            <p>Este ejercicio tiene un archivo de enunciado adicional. Por favor, descárguelo para obtener más información sobre el ejercicio en cuestión.</p>
            <a href="<?php echo $row['enunciado_archivo']; ?>" class="btn btn-primary download-button" download>Descargar Enunciado</a>
        </div>
    <?php endif; ?>
</div>



<!-- Modales para las pistas -->
<?php foreach ($pistas as $index => $pista): ?>
    <?php if (!empty($pista)): ?>
        <div class="modal fade" id="pistaModal<?php echo $index; ?>" tabindex="-1" aria-labelledby="pistaModalLabel<?php echo $index; ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pistaModalLabel<?php echo $index; ?>">Pista <?php echo $index + 1; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php
                            // Leer el contenido del archivo de la pista
                            $pista_contenido = file_get_contents($pista);
                            if ($pista_contenido !== false) {
                                // Mostrar el contenido de la pista
                                echo "<pre>" . htmlspecialchars($pista_contenido) . "</pre>";
                            } else {
                                // Manejar el caso de error al leer el archivo
                                echo "Error al leer el contenido de la pista $index";
                            }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Botón para abrir la ventana modal de esta pista -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pistaModal<?php echo $index; ?>">Pista <?php echo $index + 1; ?></button>
    <?php endif; ?>
<?php endforeach; ?>

<a href="./ver_solucion.php?id=<?php echo $id_ejercicio; ?>" class="btn btn-primary">Ver Solución</a>

<div id="drop-area" onclick="document.getElementById('fileElem').click();" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);">
    <p>Haz clic aquí o arrastra tus archivos para subirlos</p>
    <input type="file" id="fileElem" name="archivo" multiple accept="*" style="display:none;">
</div>

<div id="file-list">
    <p>Archivos seleccionados:</p>
</div>

<script>
    function createFileItem(file, filesContainer) {
    var fileItemContainer = document.createElement('div');
    fileItemContainer.className = 'container'; // Agregar la clase container al contenedor
    fileItemContainer.style.marginTop = '20px'; // Agregar margen superior para separación

    var fileAlert = document.createElement('div');
    fileAlert.className = 'alert alert-dark'; // Agregar clases de Bootstrap para alerta
    fileAlert.setAttribute('role', 'alert'); // Añadir atributo role

    var fileName = document.createElement('h3');
    fileName.className = 'text-center'; // Agregar la clase text-center al nombre del archivo
    fileName.textContent = file.name;
    fileName.style.fontWeight = 'bold'; // Aplicar negrita al nombre del archivo
    fileName.style.color = '#343a40'; // Cambiar el color del texto

    fileAlert.appendChild(fileName); // Añadir el nombre del archivo a la alerta
    fileItemContainer.appendChild(fileAlert); // Añadir la alerta al contenedor general

    var sendButtonContainer = document.createElement('div'); // Nuevo contenedor para el botón
    sendButtonContainer.className = 'text-center'; // Clase text-center para centrar el botón

    var sendButton = document.createElement('button');
    sendButton.textContent = 'Enviar';
    sendButton.className = 'btn btn-primary'; // Agregar clases de Bootstrap al botón
    sendButton.onclick = function() {
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

    sendButtonContainer.appendChild(sendButton); // Agregar el botón al contenedor del botón
    fileItemContainer.appendChild(sendButtonContainer); // Agregar el contenedor del botón al contenedor general

    // Añadir el contenedor del archivo al contenedor general de archivos
    filesContainer.appendChild(fileItemContainer);
}

document.getElementById('fileElem').onchange = function(event) {
    var fileList = event.target.files;
    var filesContainer = document.getElementById('file-list');
    filesContainer.innerHTML = '';

    for (var i = 0; i < fileList.length; i++) {
        createFileItem(fileList[i], filesContainer);
    }
};

function dropHandler(event) {
    event.preventDefault();
    document.getElementById('drop-area').classList.remove('highlight');

    var fileList = event.dataTransfer.files;
    var filesContainer = document.getElementById('file-list');
    filesContainer.innerHTML = '';

    for (var i = 0; i < fileList.length; i++) {
        createFileItem(fileList[i], filesContainer);
    }

    document.getElementById('file-form').style.display = 'block';
}

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