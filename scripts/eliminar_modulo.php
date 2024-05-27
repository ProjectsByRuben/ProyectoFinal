<?php
session_start();
include 'conexion.php';

// Verificar si se ha proporcionado un ID de módulo para eliminar
if (!isset($_GET['id_modulo'])) {
    header("Location: ../ver_modulos.php");
    exit();
}

$id_modulo = $_GET['id_modulo'];

// Eliminar el módulo de la base de datos
$sql_delete_modulo = "DELETE FROM modulos WHERE id_modulo = $id_modulo";

if ($conn->query($sql_delete_modulo) === TRUE) {
    // Éxito al eliminar el módulo
    $_SESSION['mensaje'] = "Módulo eliminado correctamente.";
} else {
    // Error al eliminar el módulo
    $_SESSION['error'] = "Error al eliminar el módulo: " . $conn->error;
}

// Redirigir de vuelta a la página de módulos
header("Location: ../ver_modulos.php");
exit();
?>