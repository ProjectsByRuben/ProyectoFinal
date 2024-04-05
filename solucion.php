<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: index.html");
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
        // Obtener el contenido del archivo
        $contenido_pdf = file_get_contents($_FILES["archivo"]["tmp_name"]);

        // Insertar la solución en la base de datos
        $stmt = $conn->prepare("INSERT INTO soluciones (id_ejercicio, id_usuario, solucion) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_ejercicio, $_SESSION['id_usuario'], $contenido_pdf);

        if ($stmt->execute()) {
            echo "La solución se ha guardado correctamente.";
        } else {
            echo "Error al guardar la solución: " . $stmt->error;
        }
    } else {
        echo "Error al subir el archivo.";
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
</head>
<body>
    <h1>Realizar Ejercicio</h1>
    <h2>Enunciado:</h2>
    <p><?php echo $enunciado; ?></p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id_ejercicio; ?>" method="post" enctype="multipart/form-data">
        <h2>Realiza el Ejercicio:</h2>
        <input type="file" name="archivo" accept=".pdf">
        <br><br>
        <input type="submit" value="Enviar Solución">
    </form>
    <br>
    <a href="ejercicios.php">Volver a la lista de ejercicios</a>
    <br>
    <a href="cerrar_sesion.php">Cerrar Sesión</a>
</body>
</html>
