<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    // Si el usuario no ha iniciado sesión, redireccionar al formulario de inicio de sesión
    header("Location: index.php");
    exit();
}

// Verificar si se proporciona el ID de la solución a eliminar
if (!isset($_GET['id'])) {
    // Si no se proporciona el ID de la solución, redireccionar a alguna página apropiada
    header("Location: alguna_pagina_apropiada.php");
    exit();
}

// Obtener el ID de la solución a eliminar
$id_solucion = $_GET['id'];

// Consulta para obtener la ruta del archivo de la solución
$sql = "SELECT solucion FROM soluciones WHERE id_solucion = $id_solucion";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ruta_archivo = $row['solucion'];
    
    // Eliminar el archivo físico de la carpeta "soluciones"
    if (unlink($ruta_archivo)) {
        // Si se elimina el archivo correctamente, procede a eliminar la entrada de la base de datos
        $sql_delete = "DELETE FROM soluciones WHERE id_solucion = $id_solucion";
        if ($conn->query($sql_delete) === TRUE) {
            // Redireccionar a la página actual para recargarla
            echo "<script>window.location.reload();</script>";
        } else {
            echo "Error al eliminar la entrada de la base de datos: " . $conn->error;
        }
    } else {
        echo "Error al eliminar el archivo físico.";
    }
} else {
    echo "No se encontró la solución con el ID proporcionado.";
}

$conn->close();
?>