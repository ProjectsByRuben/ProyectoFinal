<?php
session_start();

$tipo_usuario = $_SESSION['tipo'];

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proyecto_asignaturas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

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

// Consulta para obtener los detalles del ejercicio con el ID proporcionado
$id_ejercicio = $_GET['id'];
$sql = "SELECT * FROM ejercicios WHERE id_ejercicio = $id_ejercicio";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener los detalles del ejercicio
    $row = $result->fetch_assoc();
    $titulo_ejercicio = $row['titulo'];
    $enunciado = $row['enunciado'];
}

// Directorio donde se encuentran los archivos HTML, PHP, PDF, JS, CSS y PY de las soluciones
$directorio_ejercicios = 'ejercicios/';

// Comprobar si el archivo HTML, PDF, PHP, JS, CSS o PY de la muestra del ejercicio existe
$ruta_muestra_html = $directorio_ejercicios . $id_ejercicio . '.html';
$ruta_muestra_pdf = $directorio_ejercicios . $id_ejercicio . '.pdf';
$ruta_muestra_php = $directorio_ejercicios . $id_ejercicio . '.php';
$ruta_muestra_js = $directorio_ejercicios . $id_ejercicio . '.js';
$ruta_muestra_css = $directorio_ejercicios . $id_ejercicio . '.css';
$ruta_muestra_py = $directorio_ejercicios . $id_ejercicio . '.py';
$ruta_muestra_zip = $directorio_ejercicios . $id_ejercicio . '.zip';

// Definir el tipo de archivo por defecto como HTML
$tipo_archivo = 'html';
$contenido = '';

// Verificar si el archivo HTML, PDF, PHP, JS, CSS, PY o ZIP existe
if (file_exists($ruta_muestra_html)) {
    // Si el archivo HTML existe, cargar su contenido
    $contenido = file_get_contents($ruta_muestra_html);
} elseif (file_exists($ruta_muestra_pdf)) {
    // Si el archivo PDF existe, establecer el tipo de archivo como PDF
    $tipo_archivo = 'pdf';
} elseif (file_exists($ruta_muestra_php)) {
    // Si el archivo PHP existe, cargar su contenido y establecer el tipo de archivo como PHP
    $tipo_archivo = 'php';
    $contenido = file_get_contents($ruta_muestra_php);
} elseif (file_exists($ruta_muestra_js)) {
    // Si el archivo JS existe, cargar su contenido y establecer el tipo de archivo como JS
    $tipo_archivo = 'js';
    $contenido = file_get_contents($ruta_muestra_js);
} elseif (file_exists($ruta_muestra_css)) {
    // Si el archivo CSS existe, cargar su contenido y establecer el tipo de archivo como CSS
    $tipo_archivo = 'css';
    $contenido = file_get_contents($ruta_muestra_css);
} elseif (file_exists($ruta_muestra_py)) {
    // Si el archivo PY existe, cargar su contenido y establecer el tipo de archivo como PY
    $tipo_archivo = 'py';
    $contenido = file_get_contents($ruta_muestra_py);
} elseif (file_exists($ruta_muestra_zip)) {
    // Si el archivo ZIP existe, mostrar un mensaje indicando que debe descargarse para verlo
    $tipo_archivo = 'zip';
} else {
    // Si el archivo no existe, redireccionar a la página de ejercicios
    header("Location: ejercicios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solución del Ejercicio</title>
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/styles/default.min.css">
    <link rel="stylesheet" href="./styles.css?v=3" id="themeStylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 10px; /* Reducir el padding a 10px para menos espacio en los bordes */
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
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.7.2/highlight.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-sm bg-body-tertiary"> <!-- Añadido navbar-sm para hacerlo más pequeño -->
    <img src="./img/ejercitacode.png" alt="Bootstrap" width="80" height="80">
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
        Sesion
    </button>
    <button id="themeButton" onclick="toggleTheme()" class="btn btn-primary">Cambiar Tema</button>
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

<div class="container">
    <h1 class="text-center mt-4">Solución del Ejercicio</h1>
    <h2 class="text-center"><?php echo $titulo_ejercicio; ?></h2>
    <div class="alert alert-dark" role="alert">
        <h3 class="text-center"><?php echo $enunciado; ?></h3>
    </div>

    <?php if ($tipo_archivo === 'php' || $tipo_archivo === 'js' || $tipo_archivo === 'css' || $tipo_archivo === 'py'): ?>
        <!-- Si es un archivo HTML, PHP, JS, CSS o PY, mostrar su contenido y su código -->
        <h2 class="text-center">Código del ejercicio</h2>
        <div class="code-container">
            <pre><code class="<?php echo $tipo_archivo; ?>"><?php echo htmlspecialchars($contenido); ?></code></pre>
        </div>

    <?php elseif ($tipo_archivo === 'html'): ?>
    <h2 class="text-center">Muestra del ejercicio</h2>
    <div class="code-container">
        <?php echo $contenido; ?>
    </div>

    <h2 class="text-center">Código del ejercicio</h2>
    <div class="code-container">
        <pre><code class="html"><?php echo htmlspecialchars($contenido); ?></code></pre>
    </div>

    <?php elseif ($tipo_archivo === 'pdf'): ?>
        <!-- Si es un archivo PDF, mostrar el visor PDF -->
        <div class="pdf-navigation">
            <button id="prev-page" class="btn btn-primary">Página anterior</button>
            <span id="page-num"></span>
            <button id="next-page" class="btn btn-primary">Siguiente página</button>
            <a href="<?php echo $ruta_muestra_pdf; ?>" download class="btn btn-success">Descargar PDF</a>
        </div>
        <div id="pdf-viewer"></div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
        <script>
            const pdfPath = '<?php echo $ruta_muestra_pdf; ?>';
            let currentPage = 1;
            let pdfDoc = null;

            const renderPage = (num) => {
                pdfDoc.getPage(num).then((page) => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const viewport = page.getViewport({ scale: 1 });

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    page.render(renderContext).promise.then(() => {
                        document.getElementById('pdf-viewer').innerHTML = '';
                        document.getElementById('pdf-viewer').appendChild(canvas);
                    });
                });

                document.getElementById('page-num').textContent = `Página ${num} de ${pdfDoc.numPages}`;
                currentPage = num;
            };

            const loadPdf = async () => {
                const loadingTask = pdfjsLib.getDocument(pdfPath);
                pdfDoc = await loadingTask.promise;
                renderPage(currentPage);
            };

            document.getElementById('prev-page').addEventListener('click', () => {
                if (currentPage <= 1) return;
                renderPage(currentPage - 1);
            });

            document.getElementById('next-page').addEventListener('click', () => {
                if (currentPage >= pdfDoc.numPages) return;
                renderPage(currentPage + 1);
            });

            loadPdf();

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme); // Guarda el tema seleccionado en el almacenamiento local
    }

    // Aplica el tema almacenado en localStorage al cargar la página
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
    }
        </script>
    <?php elseif ($tipo_archivo === 'zip'): ?>
        <!-- Si es un archivo ZIP, mostrar un mensaje indicando que debe descargarse para verlo -->
        <div class="alert alert-info" role="alert">
            Este ejercicio se encuentra en un archivo ZIP. Por favor, descárguelo para ver el contenido.
            <br>
            <a href="<?php echo $ruta_muestra_zip; ?>" download class="btn btn-primary mt-2">Descargar ZIP</a>
        </div>
    <?php endif; ?>
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

<script>hljs.highlightAll();</script>
<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>