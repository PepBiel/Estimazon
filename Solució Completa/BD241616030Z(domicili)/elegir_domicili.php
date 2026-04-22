<?php

include "../connexio.php";

// Inicia la sessió
session_start();

// Recuperam i guardam correu
$correu = $_SESSION['correu'];
$_SESSION['correu'] = $correu;

// Obtenim dades de comprador
$sqlConsultaComprador = "SELECT correu, comprador.nom, llinatges, dataNaix, telef
                         FROM comprador
                         WHERE correu = ?";

$stmtComprador = mysqli_prepare($con, $sqlConsultaComprador);
mysqli_stmt_bind_param($stmtComprador, "s", $correu);
mysqli_stmt_execute($stmtComprador);
$resultadoConsultaComprador = mysqli_stmt_get_result($stmtComprador);

// Miram si hem obtingut correctament els resultats
if ($resultadoConsultaComprador && mysqli_num_rows($resultadoConsultaComprador) > 0) {
    $datosComprador = mysqli_fetch_assoc($resultadoConsultaComprador);
} else {
    $datosComprador = array();
}

// Obtenim dades del domicili del comprador
$sqlConsultaDomicilio = "SELECT iddom, carrer, numero, pis, porta, domicili.codiPostal, poblacio.nom AS poblacio, poblacio.nomZona AS zona
                        FROM domicili
                        INNER JOIN poblacio ON domicili.codiPostal = poblacio.codiPostal and domicili.idcomp = ?";

$stmtDomicilio = mysqli_prepare($con, $sqlConsultaDomicilio);
mysqli_stmt_bind_param($stmtDomicilio, "s", $correu);
mysqli_stmt_execute($stmtDomicilio);
$resultadoConsultaDomicilio = mysqli_stmt_get_result($stmtDomicilio);

// Miram si hem obtingut correctament els resultats
if ($resultadoConsultaDomicilio && mysqli_num_rows($resultadoConsultaDomicilio) > 0) {
    $datosDomicilio = mysqli_fetch_all($resultadoConsultaDomicilio, MYSQLI_ASSOC);
} else {
    $datosDomicilio = array();
}

// tancam sessió
mysqli_close($con);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dades del comprador</title> <!-- titol de la pàgina -->
    <link rel="stylesheet" type="text/css" href="estils_dades.css"> <!-- estil de la pagina -->
</head>

<body>

    <div class="container">
        <h1>Dades del Comprador</h1> <!-- titol tocprador -->

        <div class="top-right-container">
            <form action="domicilis.php" method="post" class="add-domicili-form">
                <button type="submit" class="otro-archivo-btn">Afegir domicili</button> <!-- botó afegir domicili -->
            </form>
        </div>

        <!-- dades del comprador -->
        <?php if (!empty($datosComprador)): ?>
            <div class="dades-container">
                <div class="comprador-container">

                    <h2>Dades personals</h2>

                    <ul>
                        <label for="correu">Correu:</label>
                        <input type="text" name="correu" value="<?php echo $datosComprador['correu']; ?>" readonly>
                        
                        <label for="nom">Nom:</label>
                        <input type="text" name="nom" value="<?php echo $datosComprador['nom']; ?>" readonly>
                        
                        <label for="llinatges">Llinatge:</label>
                        <input type="text" name="llinatges" value="<?php echo $datosComprador['llinatges']; ?>" readonly>
                        
                        <label for="dataNaix">Data de naixament:</label>
                        <input type="text" name="dataNaix" value="<?php echo $datosComprador['dataNaix']; ?>" readonly>
                        
                        <label for="telef">Telèfon:</label>
                        <input type="text" name="telef" value="<?php echo $datosComprador['telef']; ?>" readonly>
                    </ul>
                </div>
                <div class="domicilio-container">

                    <h2>Dades del domicili</h2> <!-- titol -->

                    <!-- domicilis -->
                    <?php foreach ($datosDomicilio as $domicilio): ?>
                        <ul>
                            <label for="carrer">Carrer:</label>
                            <input type="text" name="carrer" value="<?php echo $domicilio['carrer']; ?>" readonly>

                            <label for="numero">Numero:</label>
                            <input type="text" name="numero" value="<?php echo $domicilio['numero']; ?>" readonly>

                            <label for="pis">Pis:</label>
                            <input type="text" name="pis" value="<?php echo $domicilio['pis']; ?>" readonly>

                            <label for="porta">Porta:</label>
                            <input type="text" name="porta" value="<?php echo $domicilio['porta']; ?>" readonly>

                            <label for="codiPostal">Codi Postal:</label>
                            <input type="text" name="codiPostal" value="<?php echo $domicilio['codiPostal']; ?>" readonly>

                            <label for="poblacio">Població:</label>
                            <input type="text" name="poblacio" value="<?php echo $domicilio['poblacio']; ?>" readonly>

                            <label for="zona">Zona:</label>
                            <input type="text" name="zona" value="<?php echo $domicilio['zona']; ?>" readonly>
                            
                            <!-- botó per a elegir domicili-->
                            <form action="../Pago/pagar.php" method="post" class="domicilio-form">
                                <input type="hidden" name="iddom" value="<?php echo $domicilio['iddom']; ?>">
                                <button type="submit" class="otro-archivo-btn" name="guardar_iddom">Triar domicili i continuar</button>
                            </form>
                        </ul>
                        <hr class="separator"> <!-- linea de separació -->
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p>No se encontraron datos del comprador.</p>
        <?php endif; ?>
    </div>

</body>

</html>
