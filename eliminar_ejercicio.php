<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

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

// Obtener el ID del ejercicio desde el parámetro GET
$id_ejercicio = $_GET['id'];

// Consulta SQL para eliminar las soluciones asociadas al ejercicio
$sql_delete_soluciones = "DELETE FROM soluciones WHERE id_ejercicio = $id_ejercicio";

if ($conn->query($sql_delete_soluciones) === TRUE) {
    // Consulta SQL para eliminar el ejercicio de la base de datos
    $sql_delete_ejercicio = "DELETE FROM ejercicios WHERE id_ejercicio = $id_ejercicio";

    if ($conn->query($sql_delete_ejercicio) === TRUE) {
        // Directorios donde se encuentran los archivos de ejercicios y enunciados
        $directorio_ejercicios = 'ejercicios/';
        $directorio_enunciados = 'enunciados/';

        // Patrones para buscar archivos asociados al ejercicio y al enunciado
        $patron_archivos_ejercicio = $directorio_ejercicios . $id_ejercicio . ".*";
        $patron_archivos_enunciado = $directorio_enunciados . $id_ejercicio . ".*";

        // Obtener la lista de archivos que coinciden con los patrones
        $archivos_a_eliminar_ejercicio = glob($patron_archivos_ejercicio);
        $archivos_a_eliminar_enunciado = glob($patron_archivos_enunciado);

        // Eliminar cada archivo asociado al ejercicio y al enunciado
        foreach ($archivos_a_eliminar_ejercicio as $archivo) {
            if (file_exists($archivo)) {
                unlink($archivo); // Eliminar el archivo
            }
        }

        foreach ($archivos_a_eliminar_enunciado as $archivo) {
            if (file_exists($archivo)) {
                unlink($archivo); // Eliminar el archivo
            }
        }

        // Redireccionar a la página de ejercicios después de eliminar
        header("Location: ejercicios.php");
        exit();
    } else {
        echo "Error al eliminar el ejercicio: " . $conn->error;
    }
} else {
    echo "Error al eliminar las soluciones asociadas al ejercicio: " . $conn->error;
}

$conn->close();
?>