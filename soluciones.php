<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

$tipo_usuario = $_SESSION['tipo'];
$id_modulo = $_SESSION['id_modulo'];

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

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Obtener el ID de usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];

// Consulta para obtener las soluciones del usuario junto con el título y el ID del ejercicio correspondiente
// Consulta para obtener las soluciones basadas en el tipo de usuario
if ($tipo_usuario == 'alumno') {
    // Si es un alumno, obtener soluciones del usuario actual
    $sql = "SELECT soluciones.*, ejercicios.titulo, ejercicios.id_ejercicio AS id_ejercicio, usuarios.usuario AS nombre_usuario, asignaturas.nombre AS nombre_asignatura
            FROM soluciones 
            INNER JOIN ejercicios ON soluciones.id_ejercicio = ejercicios.id_ejercicio 
            INNER JOIN usuarios ON soluciones.id_usuario = usuarios.id_usuario
            INNER JOIN asignaturas ON ejercicios.id_asignatura = asignaturas.id_asignatura
            WHERE soluciones.id_usuario = $id_usuario";
} elseif ($tipo_usuario == 'profesor') {
    // Si es un profesor, obtener soluciones de todos los usuarios
    $sql = "SELECT soluciones.*, ejercicios.titulo, ejercicios.id_ejercicio AS id_ejercicio, usuarios.usuario AS nombre_usuario, asignaturas.nombre AS nombre_asignatura
        FROM soluciones 
        INNER JOIN ejercicios ON soluciones.id_ejercicio = ejercicios.id_ejercicio 
        INNER JOIN usuarios ON soluciones.id_usuario = usuarios.id_usuario
        INNER JOIN asignaturas ON ejercicios.id_asignatura = asignaturas.id_asignatura
        WHERE asignaturas.id_modulo = $id_modulo";
} else {
    // Manejar cualquier otro tipo de usuario (opcional)
    // Puedes mostrar un mensaje de error o redireccionar a otra página
    echo "Tipo de usuario no reconocido";
    exit();
}
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soluciones del Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css?v=2" id="themeStylesheet">
    <style>
        body {
            font-family: 'Bangers', cursive;
            background-color: #f8f9fa;
        }
        .navbar {
            padding-left: 0 !important; /* Eliminar el padding a la izquierda */
            padding-right: 10px !important; /* Eliminar el padding a la derecha */
            margin-top: 0 !important; /* Eliminar el margen superior */
        }
        .btn-descarga {
            background-color: #228182;
            color: white;
        }
        /* Estilo para el botón de descarga al pasar el ratón por encima */
        .btn-descarga:hover {
            background-color: #00d5d6; /* Mantener el color de fondo */
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
    <button type="button" class="btn modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal" style="border: none;"><img src="./img/usuario.png" style="width: 25px; height: 25px;"></button>
    <button id="themeButton" onclick="toggleTheme()" class="btn">
        <img id="themeIcon" src="./img/<?php echo $currentTheme === 'dark' ? 'sun' : 'moon'; ?>.png" alt="<?php echo $currentTheme === 'dark' ? 'moon' : 'sun'; ?>">
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
                $id_solucion = $row['id_solucion']; // Obtener id_solucion
                ?>
                <div class="col" data-id="<?php echo $id_solucion; ?>"> <!-- Usar id_solucion como data-id -->
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title-solution">Nombre del Usuario</h5>
                        <p class="card-text-solution"><?php echo $row['nombre_usuario']; ?></p>
                        <h5 class="card-title-solution">Nombre de la Asignatura</h5>
                        <p class="card-text-solution"><?php echo $row['nombre_asignatura']; ?></p>
                        <h5 class="card-title-solution">Título del Ejercicio</h5> <!-- Cambio de Enunciado a Título -->
                        <p class="card-text-solution"><?php echo $titulo; ?></p> <!-- Cambio de Enunciado a Título -->
                        <h5 class="card-title-solution">Archivo</h5>
                        <p class="card-text-solution"><?php echo basename($ruta_archivo); ?></p>
                        <a href="<?php echo $ruta_archivo; ?>" class="btn btn-descarga" download>Descargar</a>
                        <!-- Botón de eliminar -->
                        <button class="btn btn-danger btn-delete" onclick="eliminarRespuesta(<?php echo $id_solucion; ?>, '<?php echo $ruta_archivo; ?>')">Eliminar</button>
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
    
    <script>
        function eliminarRespuesta(id_solucion, ruta_archivo) {
    if (confirm("¿Estás seguro de que deseas eliminar esta respuesta?")) {
        fetch('eliminar_respuesta.php?id=' + id_solucion, {
            method: 'GET'
        })
        .then(response => {
            if (response.ok) {
                const cardToRemove = document.querySelector(`[data-id="${id_solucion}"]`);
                if (cardToRemove) {
                    cardToRemove.parentNode.removeChild(cardToRemove);
                }
                // Eliminar el archivo de la carpeta "soluciones"
                eliminarArchivoDescarga(ruta_archivo);
            } else {
                throw new Error('Error al eliminar la respuesta');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
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