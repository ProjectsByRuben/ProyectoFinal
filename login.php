<?php
session_start();

if (isset($_POST['usuario']) && isset($_POST['pass'])) {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "proyecto_asignaturas";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener los datos del formulario de inicio de sesión
    $username = $_POST['usuario'];
    $password = $_POST['pass'];

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT id_usuario FROM usuarios WHERE usuario = '$username' AND contraseña = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Usuario autenticado correctamente
        $row = $result->fetch_assoc();
        $_SESSION['usuario'] = $username;
        $_SESSION['pass'] = $password;
        $_SESSION['id_usuario'] = $row['id_usuario']; // Guardar el ID de usuario en la sesión
        header("Location: dashboard.php"); // Redirigir al usuario a la página dashboard.php
        exit();
    } else {
        // Usuario no encontrado o contraseña incorrecta
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: index.php"); // Redirigir al usuario a la página index.php
        exit();
    }

    $conn->close();
}
?>