<?php
session_start();
include './conexion.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Verificar si se ha proporcionado un ID de usuario para eliminar
if (!isset($_GET['id'])) {
    header("Location: ../usuarios.php");
    exit();
}

$id_usuario = $_GET['id'];

// Eliminar el usuario de la base de datos
$sql_eliminar = "DELETE FROM usuarios WHERE id_usuario = $id_usuario";

if ($conn->query($sql_eliminar) === TRUE) {
    // Redireccionar al usuario de vuelta a la página de usuarios después de eliminar
    header("Location: ../usuarios.php");
    exit();
} else {
    // En caso de error, puedes redirigir a una página de error o mostrar un mensaje al usuario
    echo "Error al eliminar el usuario: " . $conn->error;
}
?>