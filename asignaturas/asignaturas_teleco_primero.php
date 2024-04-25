<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: ../index.php");
    exit();
}

include '../scripts/conexion.php'; // Incluye el archivo de conexión

$tipo_usuario = $_SESSION['tipo'];

// Consulta para obtener las asignaturas de ASIR del primer curso
$sql = "SELECT a.id_asignatura, a.nombre, COUNT(e.id_ejercicio) AS num_ejercicios
        FROM asignaturas a
        LEFT JOIN ejercicios e ON a.id_asignatura = e.id_asignatura
        WHERE a.id_modulo = 2 AND a.id_curso = 1
        GROUP BY a.id_asignatura";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignaturas de TELECO - 1º Curso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css?v=2" id="themeStylesheet">
    <style>
        .small-text {
            font-size: 16px;
            color: #228182;
        }
        #themeIcon {
            width: 28px; /* Ajustar el ancho */
            height: 25px; /* Ajustar la altura */
            margin-left: 10px;
            margin-right: 20px;
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
        /* Estilo para el número de ejercicios */
        .num-ejercicios {
            font-weight: bold;
        }
        .num-ejercicios.rojo {
            color: red;
        }
        .num-ejercicios.verde {
            color: green;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <img src="../img/ejercitacode3.png" alt="Bootstrap" width="80" height="80">
    <div class="container-fluid">
        <a class="nav-link active" aria-current="page" href="javascript:history.back()">
            <img src="../img/flecha.png" class="img-fluid" style="max-width: 30px;" alt="Flecha">
            <span style='margin: 0 10px;'></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="../dashboard.php">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="../modulos.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Modulos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../modulos.php">Modulos</a></li>
                        <li><a class="dropdown-item" href="./asignaturas_asir_primero.php">1º Asir</a></li>
                        <li><a class="dropdown-item" href="./asignaturas_asir_segundo.php">2º Asir</a></li>
                        <li><a class="dropdown-item" href="./asignaturas_teleco_primero.php">1º Teleco</a></li>
                        <li><a class="dropdown-item" href="./asignaturas_teleco_segundo.php">2º Teleco</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../soluciones.php">Soluciones</a>
                </li>
                <?php if ($tipo_usuario === 'profesor'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../crear_ejercicio.php">Crear Ejercicio</a>
                    </li>
                <?php endif; ?>
            </ul>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary modal-button" data-bs-toggle="modal" data-bs-target="#exampleModal">Sesion</button>
            <button id="themeButton" onclick="toggleTheme()" class="btn">
        <img id="themeIcon" src="../img/<?php echo $currentTheme === 'dark' ? 'sun' : 'moon'; ?>.png" alt="<?php echo $currentTheme === 'dark' ? 'moon' : 'sun'; ?>">
    </button>
        </div>
    </div>
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
                <a href="../cerrar_sesion.php" class="btn btn-primary">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="container mt-4">
    <h2>Asignaturas de TELECO - 2º Curso</h2>
    <div class="list-group">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card mb-3'>";
                echo "<div class='card-body'>";
                // Aquí aplicamos una clase condicional según el valor del número de ejercicios
                $clase_ejercicios = $row['num_ejercicios'] == 0 ? 'rojo' : 'verde';
                echo "<h5 class='card-title'>{$row['nombre']} <small class='small-text'>(<span class='num-ejercicios $clase_ejercicios'>{$row['num_ejercicios']}</span> ejercicio/s)</small></h5>";
                echo "<a href='../cursos/segundo_curso_teleco.php?asignatura_id={$row['id_asignatura']}' class='btn btn-primary'>Ver ejercicios</a>";
                echo "</div>";
                echo "</div>";            
            }
        } else {
            echo "<p class='text-muted'>No hay asignaturas disponibles.</p>";
        }
        ?>
    </div>
</div>

<script>
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme); // Guarda el tema seleccionado en el almacenamiento local

        // Actualiza la imagen del botón después de cambiar el tema
        const themeIcon = document.getElementById('themeIcon');
        themeIcon.src = `../img/${newTheme === 'dark' ? 'sun' : 'moon'}.png`;
    }

    // Aplica el tema almacenado en localStorage al cargar la página
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
    }

    // Actualiza la imagen del botón según el tema actual al cargar la página
    const themeIcon = document.getElementById('themeIcon');
    themeIcon.src = `../img/${currentTheme === 'dark' ? 'sun' : 'moon'}.png`;
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>