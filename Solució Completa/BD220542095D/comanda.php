<?php
include "../connexio.php";

// Inicia la sesión
session_start();

// Recupera los datos de la sesión
$id_comprador = $_SESSION['correu'];

// Guardar en la sesión
$_SESSION['correu'] = $id_comprador;

$consulta_comanda = "SELECT idcoman FROM COMANDA WHERE idcomp = '$id_comprador' AND pagat = false";
$res_comanda = mysqli_query($con, $consulta_comanda);

if ($res_comanda && mysqli_num_rows($res_comanda) > 0) {
    $row_comanda = mysqli_fetch_assoc($res_comanda);
    $id_comanda = $row_comanda['idcoman'];

    // Guardar idcoman en la sesión
    $_SESSION['idcoman'] = $id_comanda;
} else {
    echo '<script>
                alert("no hi ha productes a la comanda");
                setTimeout(function() {
                    window.location.href = "comprador.php";
                }, 0);
              </script>';
        
    exit();  
}

$carrito_p = "SELECT 
    QUANTITAT.nombre AS quantitat, 
    PRODUCTE.nom AS nom, 
    VENDA.preu AS preu, 
    QUANTITAT.idvenda AS id_venda,
    QUANTITAT.nombre * VENDA.preu AS total
FROM QUANTITAT 
JOIN VENDA  ON QUANTITAT.idcomanda = $id_comanda AND QUANTITAT.nombre > 0 AND QUANTITAT.idvenda = VENDA.idvenda 
JOIN PRODUCTE  ON VENDA.idprod = PRODUCTE.idprod";

$resultat = mysqli_query($con, $carrito_p);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estil_comanda.css">
</head>
<body>

    <div class="bottom-right-container">
        <form action="domicili/elegir_domicili.php" method="post" class="add-domicili-form">
            <button type="submit" class="otro-archivo-btn">Continuar</button>
        </form>
    </div>

    <div class="top-right-container">
        <form action="comprador.php" method="post" class="inici">
            <button type="submit" class="otro-archivo-btn">Retorn al inici</button>
        </form>
    </div>
    

    <table>
        <tr>
            <th>Producte</th>
            <th>Quantitat</th>
            <th>Preu</th>
            <th>Total</th>
            <th>Acció</th>
        </tr>
    <?php
    while ($row = mysqli_fetch_assoc($resultat)) {
        echo '<tr>';
        echo '<td>' . $row['nom'] . '</td>';
        echo '<td>' . $row['quantitat'] . '</td>';
        echo '<td>' . $row['preu'] . '</td>';
        echo '<td>' . $row['total'] . '</td>';
        echo '<td>';
        echo '<form action="Llevar.php" method="post">';
        echo '<input type="hidden" name="venda_id" value="' . $row['id_venda'] . '">';
        echo '<button type="submit" name="accion" class="otro-archivo-btn">Eliminar</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    ?>
    
    <?php
        mysqli_free_result($resultat);
    ?>
    </table>
</body>
</html>
