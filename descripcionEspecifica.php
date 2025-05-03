<?php
    $config = parse_ini_file("DB/config.ini", true); //Lee la config

    $db = $config['database']; //Saca los datos y los pasa a un array

    $conexion = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']); //Creacion de la conexion

    if ($conexion->connect_error) { //Verificacion de la conexion
        die("Conexion fallida: " . $conexion->connect_error);
    }

    if (isset($_GET['numero_identificador'])){
        $numero = $_GET['numero_identificador'];

        $sql = "SELECT * FROM pokemon WHERE numero_identificador = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $numero);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $pokemon = $resultado->fetch_assoc();
        $stmt->close();
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pokedex</title>
        <link rel="icon" type="image/png" href="imagenes/logoPokemon.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link href="css/style.css" rel="stylesheet">
    </head>
    <body>
        <?php
            include 'header.php';
        ?>
        <br>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h4 class="text-center mb-4">Detalle del Pokémon</h4>
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-4 text-center p-3">
                                <img src="imagenes/pokemon/<?php echo htmlspecialchars($pokemon['imagen']); ?>" alt="<?php echo htmlspecialchars($pokemon['imagen']); ?>" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                            <div class="col-md-8 text-center p-3">
                                <div class="card-body">
                                    <h3 class="card-title"> <?php echo htmlspecialchars($pokemon['nombre']); ?></h3>
                                    <p class="card-text mb-2"><strong>Número:</strong> <?php echo htmlspecialchars($pokemon['numero_identificador']); ?></p>
                                    <p class="card-text mb-2"><strong>Tipo:</strong>
                                        <img src="imagenes/tipo/<?php echo htmlspecialchars($pokemon['tipo']); ?>" alt="<?php echo htmlspecialchars($pokemon['tipo']); ?>">
                                    </p>
                                    <p class="card-text"><strong>Descripción:</strong> <?php echo htmlspecialchars($pokemon['descripcion']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="index.php" class="btn btn-primary w-100">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    </body>
</html>