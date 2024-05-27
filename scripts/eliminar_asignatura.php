<?php
include './conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['id_modulo']) && isset($_POST['eliminar_asignatura'])) {
        $id_modulo = $_POST['id_modulo'];
        $id_asignatura = $_POST['eliminar_asignatura'];

        // Procesar eliminación de la asignatura
        $query = "DELETE FROM asignaturas WHERE id_asignatura = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id_asignatura);
        $stmt->execute();

        // Redirigir de vuelta a la página de modificación del módulo con el id_modulo
        header("Location: ../modificar_modulo.php?id_modulo=$id_modulo");
        exit();
    }
}
?>