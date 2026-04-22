<?php
include "../connexio.php";

if(isset($_GET['id_venta'])) {
    $id_venta = $_GET['id_venta'];

    // Obtener información del producto
    $consulta_product = "SELECT producte.nom
                        FROM producte
                        INNER JOIN venda ON producte.idprod = venda.idprod
                        WHERE venda.idvenda = '$id_venta'";

    $resultado_product = mysqli_query($con, $consulta_product);

    if ($resultado_product) {
        $producto = mysqli_fetch_assoc($resultado_product)['nom'];
    } else {
        echo "Error al obtener información del producto.";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nuevo_stock = $_POST['nuevo_stock'];
        $nuevo_precio = $_POST['nuevo_precio'];

        // Consulta preparada para actualizar el stock y precio
        $consulta_actualizar = "UPDATE venda SET stock = ?, preu = ? WHERE idvenda = ?";
        $stmt = mysqli_prepare($con, $consulta_actualizar);

        mysqli_stmt_bind_param($stmt, "iii", $nuevo_stock, $nuevo_precio, $id_venta);
        $resultado_actualizacion = mysqli_stmt_execute($stmt);

        if($resultado_actualizacion) {
            echo '<script>
                alert("Venta actualizada correctament");
                window.location.href = "venedor.php";
            </script>';
            exit();
        } else {
            echo "Error al actualizar el stock y el precio.";
        }
    }
} else {
    echo "No hi ha venta a modificar.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estil_registre.css"> 
    <title>Modificar Stock y Precio</title>
</head>
<body>
    
    <div class = "container">

        <?php
        if(isset($producto)) {
            echo "<h2>Modificar venta de " . $producto . "</h2>";
        }
        ?>

        <div class="form-container">
            <form method="post" action="">
                <label for="nuevo_stock">Nou Stock:</label>
                <input type="number" id="nuevo_stock" name="nuevo_stock" required><br><br>

                <label for="nuevo_precio">Nou preu:</label>
                <input type="number" id="nuevo_precio" name="nuevo_precio" required><br><br>

                <input type="submit" value="Actualitzar">
            </form>
        </div>
    </div>
     
</body>
</html>


