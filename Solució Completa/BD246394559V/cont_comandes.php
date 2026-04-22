<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comandes</title>
    <link rel="stylesheet" href="controlador.css">
</head>
<body>

    <h1>Comandes</h1>

    <?php

    // Inicia la sesión
    session_start();

    // Recupera los datos de la sesión
    $correu = $_SESSION['correu'];

    // Guardar en la sesión
    $_SESSION['correu'] = $correu;

    include "../connexio.php";

    // Query para obtener datos (ajusta el query según tu necesidad)
    $consulta = "SELECT comanda.idcoman, comanda.idcomp, comanda.NIF, incidencia.nom, comanda.data 
                FROM comanda 
                LEFT JOIN entrega ON comanda.idcoman = entrega.idcomanda 
                LEFT JOIN incidencia ON entrega.idinici = incidencia.idinci 
                WHERE comanda.idcon = '$correu';";
    $resultado = mysqli_query($con, $consulta);


    // Verificar si hay resultados
    if ($resultado->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Comanda</th><th>Comprador</th><th>Empresa</th><th>Productes</th><th>Data Comanda</th></tr>";
        
        // Imprimir datos en filas de la tabla
        while ($fila = mysqli_fetch_array($resultado)) {

            //echo $fila['idcoman'];

            $_SESSION['idcoman'] = $fila['idcoman'];
            echo "<tr>";
            echo "<td>" . $fila['idcoman'] . "</td>";
            echo "<td>" . $fila['idcomp'] . "</td>";

            if (empty($fila['NIF'])){
                echo "<td><form action='sel_empresa.php' method='post'>";
                echo "<input type='hidden' name='idcoman' value='" . $fila['idcoman'] . "'>";
        
                // Agregar un campo oculto para el valor asociado al botón "Elegir"        
                echo "<button type='submit' class='btnAvis'>Elegir</button>";
                echo "</form></td>";
            }else{
                echo "<td>" . $fila['NIF'] . "</td>";
            }

            echo "<td><form action='productes_comanda.php' method='post'>";
            echo "<input type='hidden' name='idcoman' value='" . $fila['idcoman'] . "'>";
            echo "<button type='submit' name='accion' class='btnAvis'>Veure</button>";
            echo "</form></td>";
            echo "<td>" . $fila['data'] . "</td>";
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

