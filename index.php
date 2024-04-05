<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="login-box">
    <h2>Login</h2>
    <!-- Formulario de inicio de sesión -->
    <form method="post" action="./login.php">
        <div class="user-box">
            <input type="text" name="usuario" size="7" required="">
            <label for="usuario">usuario</label>
        </div>
        <div class="user-box">
            <input type="password" name="pass" size="30" required="">
            <label for="pass">Contraseña</label>
        </div>
        <?php
    session_start();
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger" role="alert" style="color: red;">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']); // Limpiar el mensaje de error
    }
    ?>
        <a href="./login.php">
          <button type="submit" value="Añadir" class="boton"><span></span>Enviar</button>
          </a>
    </form>
</div>

<script src="./node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
