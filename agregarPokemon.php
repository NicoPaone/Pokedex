<?php
    session_start();

    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $config = parse_ini_file("DB/config.ini", true); //Lee la config

    $db = $config['database']; //Saca los datos y los pasa a un array

    $conexion = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']); //Creacion de la conexion

    if ($conexion->connect_error) { //Verificacion de la conexion
        die("Conexion fallida: " . $conexion->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $numero_identificador = intval($_POST['numero_identificador']);
        $nombre = trim($_POST['nombre']);
        $tipo = strtolower(trim($_POST['tipo']) . '.png');
        $descripcion = trim($_POST['descripcion']);

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $nombreImagen = basename($_FILES['imagen']['name']);
            $rutaDestino = 'imagenes/pokemon/' . $nombreImagen;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $sql = "INSERT INTO pokemon (numero_identificador, nombre, tipo, descripcion, imagen) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("issss", $numero_identificador, $nombre, $tipo, $descripcion, $nombreImagen);

                if ($stmt->execute()) {
                    header('Location: index.php');
                    exit();
                } else {
                    echo "Error al guardar en la base de datos: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error al subir la imagen";
            }
        } else {
            echo "No se seleccionó ninguna imagen o hubo un error";
        }
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
        <main class="container my-5">
            <h2 class="mb-4 text-center">Agregar Nuevo Pokémon</h2>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Pokémon</label>
                    <input type="text" class="form-control me-2" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select class="form-select" id="tipo" name="tipo" required>
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
                    <label for="numero_identificador" class="form-label">Número Identificador</label>
                    <input type="number" class="form-control me-2" id="numero_identificador" name="numero_identificador" required>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control me-2" id="imagen" name="imagen" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripcion</label>
                    <input type="text" class="form-control me-2" id="descripcion" name="descripcion" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-50">Agregar Pokémon</button>
                </div>
            </form>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    </body>
</html>