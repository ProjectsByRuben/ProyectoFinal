<?php
session_start();
include './conexion.php';

// Verificar si el usuario está autenticado y es admin
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$tipo_usuario = $_SESSION['tipo'];

// Verificar si se ha proporcionado un ID de módulo para editar
if (!isset($_GET['id_modulo'])) {
    header("Location: ../modificar_modulo.php");
    exit();
}

$id_modulo = $_GET['id_modulo'];

// Procesar el formulario de edición cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilar los datos del formulario y actualizar las asignaturas existentes
    if (!empty($_POST['asignaturas'])) {
        foreach ($_POST['asignaturas'] as $id_asignatura => $nombre) {
            $nombre = $conn->real_escape_string($nombre);
            $update_sql = "UPDATE asignaturas SET nombre = '$nombre' WHERE id_asignatura = $id_asignatura";
            $conn->query($update_sql);
        }
    }

    // Agregar nuevas asignaturas
    if (!empty($_POST['nuevas_asignaturas'])) {
        foreach ($_POST['nuevas_asignaturas'] as $id_curso => $nombres) {
            foreach ($nombres as $nombre) {
                if (!empty($nombre)) {
                    $nombre = $conn->real_escape_string($nombre);
                    $insert_sql = "INSERT INTO asignaturas (id_modulo, id_curso, nombre) VALUES ($id_modulo, $id_curso, '$nombre')";
                    $conn->query($insert_sql);
                }
            }
        }
    }

    // Redirigir para evitar reenvío del formulario
    header("Location: ../modificar_modulo.php?id_modulo=$id_modulo&mensaje=modificado");
    exit();
}
?>