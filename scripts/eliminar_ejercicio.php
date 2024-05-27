<?php
session_start();

include './conexion.php'; // Incluye el archivo de conexión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: ../index.php");
    exit();
}

// Verificar si se proporciona el ID del ejercicio
if (!isset($_GET['id'])) {
    // Si no se proporciona el ID del ejercicio, redireccionar a la página de ejercicios
    header("Location: ../modulos.php");
    exit();
}

// Obtener el ID del ejercicio desde el parámetro GET
$id_ejercicio = $_GET['id'];

// Consulta SQL para obtener el nombre del archivo de enunciado asociado al ejercicio
$sql_select_enunciado = "SELECT enunciado_archivo FROM ejercicios WHERE id_ejercicio = $id_ejercicio";
$resultado = $conn->query($sql_select_enunciado);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $nombre_enunciado = $fila["enunciado_archivo"];
    // Eliminar el archivo de enunciado si existe
    if (!empty($nombre_enunciado)) {
        $ruta_enunciado = "../" . $nombre_enunciado;
        if (file_exists($ruta_enunciado)) {
            unlink($ruta_enunciado);
        }
    }
} else {
    echo "Error: No se encontró el ejercicio.";
    exit();
}

// Consulta SQL para eliminar las soluciones asociadas al ejercicio
$sql_delete_soluciones = "DELETE FROM soluciones WHERE id_ejercicio = $id_ejercicio";

if ($conn->query($sql_delete_soluciones) === TRUE) {
    // Consulta SQL para eliminar el ejercicio de la base de datos
    $sql_delete_ejercicio = "DELETE FROM ejercicios WHERE id_ejercicio = $id_ejercicio";

    if ($conn->query($sql_delete_ejercicio) === TRUE) {
        // Directorios donde se encuentran los archivos de ejercicios, enunciados y pistas
        $directorio_ejercicios = '../ejercicios/';
        $directorio_enunciados = '../enunciados/';
        $directorio_pistas = '../pistas/';

        // Patrones para buscar archivos asociados al ejercicio, enunciado y pistas
        $patron_archivos_ejercicio = $directorio_ejercicios . $id_ejercicio . ".*";
        $patron_archivos_enunciado = $directorio_enunciados . $id_ejercicio . ".*";
        $patron_archivos_pistas = $directorio_pistas . $id_ejercicio . "_pista*.*";

        // Obtener la lista de archivos que coinciden con los patrones
        $archivos_a_eliminar_ejercicio = glob($patron_archivos_ejercicio);
        $archivos_a_eliminar_enunciado = glob($patron_archivos_enunciado);
        $archivos_a_eliminar_pistas = glob($patron_archivos_pistas);

        // Eliminar cada archivo asociado al ejercicio, enunciado y pistas
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

        foreach ($archivos_a_eliminar_pistas as $archivo) {
            if (file_exists($archivo)) {
                unlink($archivo); // Eliminar el archivo
            }
        }

        // Redireccionar a la página de la que procediste después de eliminar
        $pagina_anterior = $_SERVER['HTTP_REFERER'];
        header("Location: $pagina_anterior");
        exit();
    } else {
        echo "Error al eliminar el ejercicio: " . $conn->error;
    }
} else {
    echo "Error al eliminar las soluciones asociadas al ejercicio: " . $conn->error;
}

$conn->close();
?>