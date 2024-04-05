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
    $sql = "SELECT * FROM usuarios WHERE usuario = '$username' AND contraseña = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Usuario autenticado correctamente
        $_SESSION['usuario'] = $username;
        $_SESSION['pass'] = $password;
        header("Location: dashboard.php"); // Redirigir al usuario a la página ejercicios.php
        exit();
    } else {
        // Usuario no encontrado o contraseña incorrecta
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: index.php"); // Redirigir al usuario a la página ejercicios.php
        exit();
    }

    $conn->close();
}
?>