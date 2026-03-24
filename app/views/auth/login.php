<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Ventas por Catálogo</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background: #4e73df;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background: #2e59d9;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #4e73df;
        }
    </style>
</head>
<body>

<div class="login-container">

    <div class="logo">Ventas Catálogo</div>

    <h2>Iniciar Sesión</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>login">

        <input type="text" name="usuario" placeholder="Usuario" required>

        <input type="password" name="password" placeholder="Contraseña" required>

        <button type="submit">Ingresar</button>

    </form>

</div>

</body>
</html>