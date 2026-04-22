<?php

include "../connexio.php";

// iniciam sessió
session_start();

if (isset($_POST['guardar_iddom'])) {
    // recuperam i guardam id domicili
    $iddom = $_POST['iddom'];
    $_SESSION['iddom'] = $iddom;

    // recuperam id comanda
    $idcoman = $_SESSION['idcoman'];

} else {
    echo "Error: El formulario no fue enviado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagament</title> <!-- titol -->
    <link rel="stylesheet" type="text/css" href="estil_pagar.css"> <!-- estil de pàgina -->
</head>

<body>

    <div class="container">
        <h1>Formulari de Pagament</h1> <!-- titol -->

        <!-- formulari -->
        <form action="procesar_pago.php" method="post" class="formulario-pago">
            <label for="nombre">Nom del titular:</label>
            <input type="text" name="nombre" required>

            <label for="numero">Nombre de tarjeta:</label>
            <input type="text" name="numero" required>

            <label for="fecha">Data de venciment:</label>
            <input type="text" name="fecha" placeholder="MM/AA" required>

            <label for="cvv">CVV:</label>
            <input type="text" name="cvv" required>

            <!-- botó de pagament -->
            <input type="submit" name="boton-pago" value="Pagar">
        </form>
    </div>

</body>

</html>