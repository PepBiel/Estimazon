<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendes</title>
    <link rel="stylesheet" href="controlador.css">
</head>
<body>

    <h1>Vendes</h1>

    <?php

    include "../connexio.php";

    // Query para obtener datos (ajusta el query según tu necesidad)
    $consulta = "SELECT * FROM comanda";
    $resultado = mysqli_query($con, $consulta);

    // Verificar si hay resultados
    if ($resultado->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>IDCom</th><th>Temps</th><th>Acción</th></tr>";

        // Imprimir datos en filas de la tabla
        while ($fila = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $fila['idcoman'] . "</td>";
            echo "<td>" . $fila['idcoman'] . "</td>";
            echo "<td><button class='btnAvis' data-idcoman='" . $fila['idcoman'] . "'>Avis</button></td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No hay resultados.";
    }

    // Cerrar la conexión
    $con->close();
    ?>

    <script src="script.js"></script>
</body>
</html>