<?php
    session_start();

    $config = parse_ini_file("DB/config.ini", true); //Lee la config

    $db = $config['database']; //Saca los datos y los pasa a un array

    $conexion = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']); //Creacion de la conexion

    if ($conexion->connect_error) { //Verificacion de la conexion
        die("Conexion fallida: " . $conexion->connect_error);
    }

    if (isset($_SESSION['usuario'])) {
        if (isset($_GET['numero_identificador'])) {
            $numero_identificador = $_GET['numero_identificador'];

            $sqlImagen = "SELECT imagen FROM pokemon WHERE numero_identificador = ?";
            $stmtImagen = $conexion->prepare($sqlImagen);
            $stmtImagen->bind_param("i", $numero_identificador);
            $stmtImagen->execute();
            $resultado = $stmtImagen->get_result();

            if ($resultado->num_rows > 0) {
                $pokemon = $resultado->fetch_assoc();
                $nombreImagen = $pokemon['imagen'];

                $rutaImagen = "imagenes/pokemon/" . $nombreImagen;
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
            $stmtImagen->close();

            $sql = "DELETE FROM pokemon WHERE numero_identificador = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $numero_identificador);

            if ($stmt->execute()) {
                header('Location: index.php');
                exit();
            }
            $stmt->close();
        }
    }