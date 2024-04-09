<?php
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit(); // Detener la ejecución del script después de redirigir
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los datos del formulario
    if (isset($_POST['titulo']) && isset($_POST['enunciado']) && isset($_POST['dificultad']) && isset($_POST['asignatura'])) {
        // Conexión a la base de datos
        $servername = "localhost";
        $username = "root";
        $password = "root";
        $dbname = "proyecto_asignaturas";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Obtener datos del formulario
        $titulo = $_POST['titulo'];
        $enunciado = $_POST['enunciado'];
        $dificultad = $_POST['dificultad'];
        $asignatura = $_POST['asignatura'];

        // Insertar nuevo ejercicio en la base de datos
        $sql = "INSERT INTO ejercicios (titulo, enunciado, dificultad, id_asignatura) VALUES ('$titulo', '$enunciado', '$dificultad', '$asignatura')";

        if ($conn->query($sql) === TRUE) {
            // Ejercicio creado exitosamente
            header("Location: dashboard.php"); // Redirigir al dashboard
            exit();
        } else {
            // Error al crear el ejercicio
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    } else {
        // Datos del formulario incompletos
        echo "Por favor complete todos los campos del formulario.";
    }
} else {
    // Acceso directo a este script no permitido
    header("Location: index.php");
    exit();
}
?>