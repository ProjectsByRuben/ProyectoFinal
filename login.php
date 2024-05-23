<?php
session_start();
include './scripts/conexion.php'; // Incluye el archivo de conexión utilizando una ruta relativa

if (isset($_POST['usuario']) && isset($_POST['pass'])) {

    // Obtener los datos del formulario de inicio de sesión y sanitizarlos
    $username = htmlspecialchars($_POST['usuario']);
    $password = $_POST['pass'];

    // Consulta para obtener el hash de la contraseña almacenada en la base de datos
    $sql = "SELECT id_usuario, tipo, id_modulo, contraseña FROM usuarios WHERE usuario = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Usuario encontrado
            $stmt->bind_result($id_usuario, $tipo, $id_modulo, $stored_hashed_password);
            $stmt->fetch();

            // Verificar si la contraseña ingresada coincide con el hash almacenado
            if (hash('sha256', $password) === $stored_hashed_password) {
                // Usuario autenticado correctamente
                $_SESSION['usuario'] = $username;
                $_SESSION['pass'] = $password;
                $_SESSION['id_usuario'] = $id_usuario;
                $_SESSION['tipo'] = $tipo; // Guardar el tipo de usuario en la sesión
                $_SESSION['id_modulo'] = $id_modulo; // Guardar el id del módulo en la sesión
                $_SESSION['showNotification'] = true;
                header("Location: dashboard.php");
                exit();
            } else {
                // Contraseña incorrecta
                $_SESSION['error'] = "Credenciales incorrectas.";
                header("Location: index.php");
                exit();
            }
        } else {
            // Usuario no encontrado
            $_SESSION['error'] = "Credenciales incorrectas.";
            header("Location: index.php");
            exit();
        }

        $stmt->close();
    } else {
        // Error en la preparación de la consulta
        $_SESSION['error'] = "Error en la conexión a la base de datos.";
        header("Location: index.php");
        exit();
    }

    $conn->close();
} else {
    // Si el formulario no ha sido enviado correctamente
    header("Location: index.php");
    exit();
}
?>