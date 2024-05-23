<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit(); // Detener la ejecución del script después de redirigir
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los datos del formulario
    if (isset($_POST['titulo'], $_POST['enunciado'], $_POST['dificultad'], $_POST['asignatura'])) {

        // Obtener datos del formulario y limpiarlos
        $titulo = htmlspecialchars($_POST['titulo']);
        $enunciado = htmlspecialchars($_POST['enunciado']);
        $dificultad = htmlspecialchars($_POST['dificultad']);
        $asignatura = intval($_POST['asignatura']); // Convertir a entero para evitar inyección SQL

        // Insertar nuevo ejercicio en la base de datos utilizando sentencia preparada
        $sql = "INSERT INTO ejercicios (titulo, enunciado, dificultad, id_asignatura) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $titulo, $enunciado, $dificultad, $asignatura);

        if ($stmt->execute()) {
            // Ejercicio creado exitosamente
            header("Location: dashboard.php"); // Redirigir al dashboard
            exit();
        } else {
            // Error al crear el ejercicio
            echo "Error al crear el ejercicio. Por favor, inténtelo de nuevo más tarde.";
            // Log de errores
            error_log("Error al crear el ejercicio: " . $stmt->error);
        }

        $stmt->close();
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