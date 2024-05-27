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
    // Consulta el nombre del módulo de manera segura
    $stmt = $conn->prepare("SELECT nombre FROM modulos WHERE id_modulo = ?");
    $stmt->bind_param("i", $id_modulo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificar si se encontró el módulo y obtener su nombre
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $nombre_modulo = $fila["nombre"];
    } else {
        // Si no se encuentra el módulo, mostrar un mensaje de error
        $nombre_modulo = "Módulo Desconocido";
    }
    $stmt->close();
}

// Verificar si se proporciona el ID del ejercicio
if (!isset($_GET['id'])) {
    // Si no se proporciona el ID del ejercicio, redireccionar a la página de ejercicios
    header("Location: modulos.php");
    exit();
}

// Obtener el ID del ejercicio de manera segura
$id_ejercicio = intval($_GET['id']);

// Consulta para obtener la información del ejercicio
$stmt = $conn->prepare("SELECT * FROM ejercicios WHERE id_ejercicio = ?");
$stmt->bind_param("i", $id_ejercicio);
$stmt->execute();
$result = $stmt->get_result();

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
$stmt->close();

// Consulta para obtener las pistas del ejercicio
$stmt_pistas = $conn->prepare("SELECT pista1, pista2, pista3 FROM ejercicios WHERE id_ejercicio = ?");
$stmt_pistas->bind_param("i", $id_ejercicio);
$stmt_pistas->execute();
$result_pistas = $stmt_pistas->get_result();

$pistas = [];
if ($result_pistas->num_rows > 0) {
    $row_pistas = $result_pistas->fetch_assoc();
    // Agregar las rutas de las pistas al array
    $pistas[] = $row_pistas['pista1'];
    $pistas[] = $row_pistas['pista2'];
    $pistas[] = $row_pistas['pista3'];
}
$stmt_pistas->close();

// Guardar la solución enviada por el usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se ha enviado un archivo
    if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] == 0) {
        // Verificar si el ID de usuario está definido en la sesión
        if (isset($_SESSION['id_usuario'])) {
            $id_usuario = $_SESSION['id_usuario'];
            
            // Obtener la extensión del archivo
            $archivo_nombre = pathinfo($_FILES['archivo']['name'], PATHINFO_FILENAME);
            $archivo_extension = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
            
            // Construir el nuevo nombre del archivo
            $nuevo_nombre_archivo = $archivo_nombre . '.' . $archivo_extension;
            $ruta = 'soluciones/' . $nuevo_nombre_archivo;

            // Comprobar si el archivo ya existe y añadir un número incremental si es necesario
            $contador = 1;
            while (file_exists($ruta)) {
                $nuevo_nombre_archivo = $archivo_nombre . '_' . $contador . '.' . $archivo_extension;
                $ruta = 'soluciones/' . $nuevo_nombre_archivo;
                $contador++;
            }

            // Mover el archivo al directorio de descargas
            if (move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta)) {
                // Insertar la solución en la tabla de soluciones
                $stmt_sol = $conn->prepare("INSERT INTO soluciones (id_ejercicio, id_usuario, solucion) VALUES (?, ?, CONCAT('../', ?))");
                $stmt_sol->bind_param("iis", $id_ejercicio, $id_usuario, $ruta);
                if ($stmt_sol->execute()) {
                    echo '<script>alert("Respuesta enviada correctamente."); window.location.href = window.location.href.split("?")[0];</script>';
                } else {
                    echo '<script>alert("Error al enviar la respuesta.");</script>';
                }
                $stmt_sol->close();
            } else {
                echo '<script>alert("Error al subir el archivo.");</script>';
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
    <link rel="stylesheet" href="./estilos/styles.css?v=2" id="themeStylesheet">
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
            margin-left: 10px;
        }
        .file-item {
            margin-bottom: 10px;
            padding-left: 10px; /* Agregar padding-left a los archivos seleccionados */
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
            margin-top: 15px;
        }
        .btn-pista-1 {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="./img/logo.png" alt="Bootstrap" width="140" height="90">
    <div class="container-fluid">
        <a class="nav-link active" aria-current="page" href="javascript:history.back()">
            <img src="./img/flecha.png" class="img-fluid" style="max-width: 30px;" alt="Flecha">
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
                        <a class="nav-link active" aria-current="page" href="./crear_usuario.php">Crear Usuario</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Button trigger modal -->
    <button type="button" class="btn modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal" style="border: none;"><img src="./img/usuario.png" style="width: 25px; height: 25px;"></button>
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
                <a href="../scripts/cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
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
        <button type="button" class="btn btn-primary <?php echo $index === 0 ? 'btn-pista-1' : ''; ?>" data-bs-toggle="modal" data-bs-target="#pistaModal<?php echo $index; ?>">Pista <?php echo $index + 1; ?></button>
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