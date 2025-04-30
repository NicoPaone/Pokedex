<?php
    session_start();

    include 'tabla.php';

    $config = parse_ini_file("DB/config.ini", true); //Lee la config

    $db = $config['database']; //Saca los datos y los pasa a un array

    $conexion = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']); //Creacion de la conexion

    if ($conexion->connect_error) { //Verificacion de la conexion
    die("Conexion fallida: " . $conexion->connect_error);
    }

    if (isset($_SESSION['usuario'])) {
        if (isset($_GET['numero_identificador'])) {
            $numero = $_GET['numero_identificador'];

            $sql = "SELECT * FROM pokemon WHERE numero_identificador = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $numero);
            $stmt->execute();

            $resultado = $stmt->get_result();
            $pokemon = $resultado->fetch_assoc();
            $stmt->close();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : $pokemon['nombre'];
                $tipoSeleccionado = !empty($_POST['tipo']) ? $_POST['tipo'] : pathinfo($pokemon['tipo'], PATHINFO_FILENAME);
                $tipo = strtolower($tipoSeleccionado) . '.png';
                $numero_identificador = !empty($_POST['numero_identificador']) ? $_POST['numero_identificador'] : $pokemon['numero_identificador'];
                $descripcion = !empty($_POST['descripcion']) ? $_POST['descripcion'] : $pokemon['descripcion'];

                if (!empty($_FILES['imagen']['name'])) {
                    $imagen = $_FILES['imagen'];
                    $nombreImagen = $imagen['name'];
                    $rutaImagen = $nombreImagen;
                    $destino = 'imagenes/pokemon/' . $nombreImagen;

                    if (move_uploaded_file($imagen['tmp_name'], $destino)) {
                        $sqlImagen = "UPDATE pokemon SET imagen = ? WHERE numero_identificador = ?";
                        $stmtImagen = $conexion->prepare($sqlImagen);
                        $stmtImagen->bind_param("si", $rutaImagen, $numero);
                        $stmtImagen->execute();
                        $stmtImagen->close();
                    }
                }
                $sql = "UPDATE pokemon SET nombre = ?, tipo = ?, numero_identificador = ?, descripcion = ? WHERE numero_identificador = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssisi", $nombre, $tipo, $numero_identificador, $descripcion, $numero);
                $stmt->execute();
                $stmt->close();

                header('Location: index.php');
                exit();
            }
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
    </head>
    <body>
        <?php
            include 'header.php';
        ?>
        <br>
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="text-center mb-3">Versión actual</h4>
                    <div class="card">
                        <div class="card-body">
                            <img class="d-block mx-auto mb-3" src="imagenes/pokemon/<?php echo htmlspecialchars($pokemon['imagen']); ?>" alt="<?php echo htmlspecialchars($pokemon['nombre']); ?>" width="150" height="150"><br>
                            <p class="card-text"><strong>Número:</strong> <?php echo htmlspecialchars($pokemon['numero_identificador']); ?></p>
                            <p class="card-text"><strong>Nombre:</strong> <?php echo htmlspecialchars($pokemon['nombre']); ?></p>
                            <p class="card-text"><strong>Tipo:</strong> <img src="imagenes/tipo/<?php echo htmlspecialchars($pokemon['tipo']); ?>" alt="<?php echo htmlspecialchars($pokemon['tipo']); ?>"></p>
                            <p class="card-text"><strong>Descripción:</strong> <?php echo htmlspecialchars($pokemon['descripcion']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="text-center mb-3">Editar Pokémon</h4>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Pokémon</label>
                            <input type="text" class="form-control me-2" id="nombre" name="nombre">
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-select" id="tipo" name="tipo">
                                <option value="">Selecciona un tipo</option>
                                <option value="Acero">Acero</option>
                                <option value="Agua">Agua</option>
                                <option value="Bicho">Bicho</option>
                                <option value="Dragon">Dragon</option>
                                <option value="Electrico">Electrico</option>
                                <option value="Fantasma">Fantasma</option>
                                <option value="Fuego">Fuego</option>
                                <option value="Hada">Hada</option>
                                <option value="Hielo">Hielo</option>
                                <option value="Lucha">Lucha</option>
                                <option value="Normal">Normal</option>
                                <option value="Planta">Planta</option>
                                <option value="Psiquico">Psiquico</option>
                                <option value="Roca">Roca</option>
                                <option value="Siniestro">Siniestro</option>
                                <option value="Tierra">Tierra</option>
                                <option value="Veneno">Veneno</option>
                                <option value="Volador">Volador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="numero_identificador" class="form-label">Número identificador</label>
                            <input type="number" class="form-control me-2" id="numero_identificador" name="numero_identificador">
                        </div>
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control me-2" id="imagen" name="imagen">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control me-2" id="descripcion" name="descripcion">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-50">Actualizar Pokémon</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    </body>
</html>