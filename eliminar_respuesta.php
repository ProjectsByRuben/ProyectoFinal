<?php
session_start();

include './scripts/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: alguna_pagina_apropiada.php");
    exit();
}

$id_solucion = $_GET['id'];

$sql = "SELECT solucion FROM soluciones WHERE id_solucion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_solucion);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ruta_archivo = $row['solucion'];
    
    // Verificar si hay un archivo en la ruta especificada o si el campo está vacío
    if (empty($ruta_archivo) || !file_exists($ruta_archivo)) {
        // Si no hay archivo, proceder a eliminar la entrada de la base de datos directamente
        $sql_delete = "DELETE FROM soluciones WHERE id_solucion = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_solucion);
        
        if ($stmt_delete->execute()) {
            // Recargar la página actual después de 2 segundos
            echo "<script>
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                  </script>";
            exit();
        } else {
            echo "Error al eliminar la entrada de la base de datos: " . $conn->error;
        }
    } else {
        // Si hay un archivo, intentar eliminarlo primero
        if (unlink($ruta_archivo)) {
            $sql_delete = "DELETE FROM soluciones WHERE id_solucion = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id_solucion);
            
            if ($stmt_delete->execute()) {
                // Recargar la página actual después de 2 segundos
                echo "<script>
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                      </script>";
                exit();
            } else {
                echo "Error al eliminar la entrada de la base de datos: " . $conn->error;
            }
        } else {
            echo "Error al eliminar el archivo físico.";
        }
    }
} else {
    echo "No se encontró la solución con el ID proporcionado.";
}

$stmt->close();
$stmt_delete->close();
$conn->close();
?>