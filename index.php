<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - EjercitaCode</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('img/fondo.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            font-family: 'Bangers', cursive;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-form {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: visible;
        }

        .login-form::before {
            content: '';
            position: absolute;
            width: 0;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.3);
            transition: width 0.3s ease;
            z-index: -1;
        }

        .login-form:hover::before {
            width: 100%;
        }

        .login-form h2 {
            margin-bottom: 30px;
            text-align: center;
            color: #007bff;
            font-size: 28px;
            letter-spacing: 2px;
        }

        .form-control {
            height: 45px;
            font-size: 18px;
            border-radius: 5px;
            border: 2px solid #007bff;
        }

        .submit-btn {
            position: relative;
            overflow: hidden;
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            z-index: 1;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background-color: rgba(255, 255, 255, 0.3);
            transition: width 2s ease, height 0.3s ease;
            z-index: -1;
            transform: translate(-50%, -50%);
        }

        .submit-btn:hover::before {
            width: 0;
            height: 0;
        }

        .forgot-password {
            text-align: center;
        }

        .forgot-password a {
            color: #007bff;
            font-size: 16px;
        }

        .forgot-password a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-form">
        <h2>Inicio de Sesión</h2>
        <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
        ?>
        <form action="./scripts/login.php" method="post">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario" required>
            </div>
            <div class="form-group">
                <label for="pass">Contraseña</label>
                <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block submit-btn">Enviar</button>
            </div>
        </form>
        <!-- <div class="forgot-password">
            <a href="#">¿Olvidaste tu contraseña?</a>
        </div> -->
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>