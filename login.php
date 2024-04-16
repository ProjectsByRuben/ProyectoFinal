<?php
session_start();

include './scripts/conexion.php'; // Incluye el archivo de conexión

if (isset($_POST['usuario']) && isset($_POST['pass'])) {

    // Obtener los datos del formulario de inicio de sesión
    $username = $_POST['usuario'];
    $password = $_POST['pass'];

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT id_usuario, tipo FROM usuarios WHERE usuario = '$username' AND contraseña = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Usuario autenticado correctamente
        $row = $result->fetch_assoc();
        $_SESSION['usuario'] = $username;
        $_SESSION['pass'] = $password;
        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['tipo'] = $row['tipo']; // Guardar el tipo de usuario en la sesión
        header("Location: dashboard.php");
        exit();
    } else {
        // Usuario no encontrado o contraseña incorrecta
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: index.php");
        exit();
    }

    $conn->close();
}
?>