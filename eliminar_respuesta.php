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

// Consulta para eliminar la respuesta de la base de datos
$sql = "DELETE FROM soluciones WHERE id_solucion = $id_solucion";

if ($conn->query($sql) === TRUE) {
    // Redireccionar a la página actual para recargarla
    echo "<script>window.location.reload();</script>";
} else {
    echo "Error al eliminar la respuesta: " . $conn->error;
}

$conn->close();
?>