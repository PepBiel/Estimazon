
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productes</title>
    <link rel="stylesheet" href="controlador.css">
</head>
<body>

    <h1>Productes</h1>

    <?php

    include "../connexio.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        session_start();
        
        $idcoman = intval($_POST['idcoman']);
        
        // Query para obtener datos (ajusta el query según tu necesidad)
        $consulta = "SELECT producte.nom, venda.stock, quantitat.nombre, venda.data, venda.idven 
        FROM comanda 
        JOIN quantitat ON comanda.idcoman = quantitat.idcomanda 
        JOIN venda ON quantitat.idvenda = venda.idvenda 
        JOIN producte ON venda.idprod = producte.idprod 
        WHERE comanda.idcoman = $idcoman;";
        $resultado = mysqli_query($con, $consulta);

        // Verificar si hay resultados
        if ($resultado->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Nom</th><th>Stock</th><th>Quantitat Client</th><th>Data Arribada Venda</th><th>Avis</th></tr>";

            // Imprimir datos en filas de la tabla
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $fila['nom'] . "</td>";
                echo "<td>" . $fila['stock'] . "</td>";
                echo "<td>" . $fila['nombre'] . "</td>";
                echo "<td>" . $fila['data'] . "</td>";
                if (empty($fila['data'])){
                    echo "<td><form action='avis.php' method='post'>";
                    echo "<input type='hidden' name='idcoman' value='" . $idcoman . "'>";
                    echo "<input type='hidden' name='idven' value='" . $fila['idven'] . "'>";
                    echo "<button type='submit' class='btnAvis'>Posar</button>";
                    echo "</form></td>";
                }else{
                    echo "<td>" . " " . "</td>";
                }
                
                echo "</tr>";
            }


            echo "</table>";
        } else {
            echo "No hay resultados.";
        }
    }else{
        echo "Formulari no rebut";
    }

    // Cerrar la conexión
    $con->close();
    ?>

    <script src="script.js"></script>
</body>
</html>