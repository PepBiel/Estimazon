<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../connexio.php";
session_start();    
// Obtener el correo del vendedor de la sesión
if (isset($_SESSION['correu'])) {
    $correo_vendedor = $_SESSION['correu'];

    // Consulta SQL para obtener los productos relacionados con el vendedor
    $consulta = "SELECT PRODUCTE.nom, PRODUCTE.descripcio, VENDA.stock, VENDA.preu ,VENDA.idvenda
                    FROM PRODUCTE 
                    INNER JOIN VENDA ON PRODUCTE.idprod = VENDA.idprod
                    WHERE VENDA.idven = '$correo_vendedor'";
                    

    $result = mysqli_query($con, $consulta);

    // Nueva consulta para obtener información del vendedor
    $consulta_vendedor = "SELECT VENDEDOR.nom, VENDEDOR.llinatges, VENDEDOR.estat,
                            COUNT(AVIS.idavis) AS total_avisos
                      FROM VENDEDOR 
                      LEFT JOIN AVIS ON VENDEDOR.correu = AVIS.idven
                      WHERE VENDEDOR.correu = '$correo_vendedor'
                      GROUP BY VENDEDOR.correu";

    $result_vendedor = mysqli_query($con, $consulta_vendedor);

    if ($result_vendedor) {
        $vendedor = mysqli_fetch_assoc($result_vendedor);
        $total_avisos = $vendedor['total_avisos'];
    } else {
        echo "Error al obtener la información del vendedor.";
    }   
        
} else {
    echo "No se ha iniciado sesión o no se ha proporcionado un correo de vendedor.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina principal venedor</title>
    <link rel="stylesheet" href="estil_venedor.css"> 
</head>
<body>

    <a href="afegir_producte.php" class="add-product">Afegir Producte</a>

    <a href="../tancar_sesio.php" class="logout">Tancar Sessió</a>

    <div class="container">

        <?php
        if (isset($vendedor)) {
            echo "<h1>Ventes publicades de " . $vendedor['nom'] . " " . $vendedor['llinatges'] . "</h1>";

            // Mostrar estado del vendedor
            $estado_mostrado = ($vendedor['estat'] === null) ? "Neutral" : $vendedor['estat'];
            echo "<label style='color: white; font-weight: bold; font-size: 18px; display: block ;margin-bottom: 10px;'>Estat del venedor: " . $estado_mostrado . "</label>";

            // Mostrar total de avisos
            echo "<label style='color: white; font-size: 18px;'>Total avisos: " . $total_avisos . "</label>";
        } else {
            echo "<h1>Llistat de productes publicats</h1>";
        }
        ?>

    <table>
        <thead>
            <tr>
                <th>NOM</th>
                <th>DESCIPCIÓ</th>
                <th>STOCK</th>
                <th>PREU</th>
                <th>MODIFICAR VENTA</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['nom'] . "</td>";
                echo "<td>" . $row['descripcio'] . "</td>";
                echo "<td>" . $row['stock'] . "</td>";
                echo "<td>" . $row['preu'] . "</td>";
                echo "<td>";
                echo '<form action="modificar_stock.php" method="GET">';
                echo '<input type="hidden" name="id_venta" value="' . $row['idvenda'] . '">';
                echo '<button type="submit" name="accion" class="otro-archivo-btn">MODIFICAR</button>';
                echo '</form>';
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>



