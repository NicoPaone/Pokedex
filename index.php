<?php
    include 'tabla.php';
    //include 'agregarPokemon.php';

    $config = parse_ini_file("DB/config.ini", true); //Lee la config

    $db = $config['database']; //Saca los datos y los pasa a un array

    $conexion = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']); //Creacion de la conexion

    if ($conexion->connect_error) { //Verificacion de la conexion
        die("Conexion fallida: " . $conexion->connect_error);
    }

    if (isset($_GET['busqueda'])) {
        $busqueda = $_GET['busqueda'];
    } else {
        $busqueda = "";
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
        <?php
            include 'header.php';
        ?>
        <br>
        <main class="container-fluid container-lg">
            <form method="get" action="index.php" class="d-flex">
                <div class="row w-100">
                    <div class="col-12 col-md-8 col-lg-9">
                        <input type="text" name="busqueda" class="form-control me-2" placeholder="Ingrese nombre, tipo o numero de Pokemon" aria-label="Search">
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 m-auto mt-1 mt-md-0">
                        <button type="submit" class="btn btn-outline-primary w-100">¿Que Pokemon es este?</button>
                    </div>
                </div>
            </form>
            <div class="container my-5">
                <?php
                    mostrarTablaPokemones($conexion, $busqueda);

                    if (isset($_SESSION['usuario'])) {
                        echo '<a href="agregarPokemon.php" class="link-light link-opacity-100"><button type="button" class="btn btn-primary w-100">Agregar Pokemon</button></a>';
                    }
                ?>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    </body>
</html>
