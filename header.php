<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $config = parse_ini_file("DB/config.ini", true); //Lee la config

    $db = $config['database']; //Saca los datos y los pasa a un array

    $conexion = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']); //Creacion de la conexion

    if ($conexion->connect_error) { //Verificacion de la conexion
        die("Conexion fallida: " . $conexion->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario']) && isset($_POST['contrasenia'])) {
        $usuario = $_POST['usuario'];
        $contrasenia = $_POST['contrasenia'];

        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND contrasenia = ?");
        $stmt->bind_param("ss", $usuario, $contrasenia);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $_SESSION['usuario'] = $usuario;
            header('Location: index.php');
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Title</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-warning">
            <div class="container-fluid">
                <a href="index.php"><img src="imagenes/logoPokemon.png" alt="Pokédex Logo" width="50" height="50"></a>
                <h1 class="navbar-brand mx-auto">Pokédex</h1>

                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <span class="me-3">ADMIN: <?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
                        <form method="post" action="cerrar_sesion.php">
                            <button type="submit" class="btn btn-danger">Cerrar sesión</button>
                        </form>
                    <?php else: ?>
                        <form method="post" action="" class="d-flex">
                            <input type="text" name="usuario" class="form-control me-2" placeholder="Usuario" required>
                            <input type="password" name="contrasenia" class="form-control me-2" placeholder="Contraseña" required>
                            <button type="submit" class="btn btn-danger">Iniciar sesión</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    </body>
</html>
