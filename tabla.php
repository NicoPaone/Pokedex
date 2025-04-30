<?php

$config = parse_ini_file("DB/config.ini", true); //Lee la config

$db = $config['database']; //Saca los datos y los pasa a un array

$conexion = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']); //Creacion de la conexion

if ($conexion->connect_error) { //Verificacion de la conexion
    die("Conexion fallida: " . $conexion->connect_error);
}

function obtenerPokemones($conexion, $busqueda = '')
{
    $busqueda = strtolower(trim($busqueda));

    if ($busqueda) {
        $sql = "SELECT * FROM pokemon WHERE LOWER(nombre) LIKE ? OR LOWER(tipo) LIKE ? OR numero_identificador LIKE ?";
        $stmt = $conexion->prepare($sql);
        $searchTerm = "%" . $busqueda . "%";
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    } else {
        $sql = "SELECT * FROM pokemon";
        $stmt = $conexion->prepare($sql);
    }

    $stmt->execute();

    return $stmt->get_result();
}

function mostrarTablaPokemones($conexion, $busqueda = '')
{
    $result = obtenerPokemones($conexion, $busqueda);

    echo '<div class="table-responsive">';
    echo '<table class="table table-hover table-bordered text-center align-middle">';
    echo '<thead class="table-warning">';
    echo '<tr>';
    echo '<th>Imagen</th>';
    echo '<th>Tipo</th>';
    echo '<th>Número</th>';
    echo '<th>Nombre</th>';

    if (isset($_SESSION['usuario'])) {
        echo '<th>Acciones</th>';
    }

    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';

            $imagenPokemon = 'imagenes/pokemon/' . htmlspecialchars($row['imagen']);
            echo '<td><img src="' . $imagenPokemon . '" alt="' . htmlspecialchars($row['nombre']) . '" width="100" height="100"></td>';

            $imagenTipo = 'imagenes/tipo/' . htmlspecialchars($row['tipo']);
            echo '<td><img src="' . $imagenTipo . '" alt="' . htmlspecialchars($row['tipo']) . '"></td>';

            echo '<td>' . htmlspecialchars($row['numero_identificador']) . '</td>';

            echo '<td>' . '<a class="link-underline-dark link-dark" href="descripcionEspecifica.php?numero_identificador=' . $row['numero_identificador'] . '">' . htmlspecialchars($row['nombre']) . '</a>' . '</td>';

            if (isset($_SESSION['usuario'])) { //Solo lo muestra si esta la sesion iniciada
                echo '<td>';
                echo '<a href="editar.php?numero_identificador=' . $row['numero_identificador'] . '" class="btn btn-warning btn-sm me-1">Editar</a>';
                echo '<a href="eliminar.php?numero_identificador=' . $row['numero_identificador'] . '" class="btn btn-danger btn-sm">Eliminar</a>';
                echo '</td>';
            }

            echo '</tr>';
        }
    } else {
        $colspan = isset($_SESSION['usuario']) ? 5 : 4;
        echo '<tr><td colspan="' . $colspan . '" class="text-center">No se encontraron Pokémon</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
