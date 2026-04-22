<?php

include "../connexio.php";

// Iniciam SESSION
session_start();

// Guardam i recuperam el correu
$correu = $_SESSION['correu'];
$_SESSION['correu'] = $correu;

// Variables que utilitzarem per el formulari
$carrer = $numero = $pis = $porta = $codiPostal = '';

// Variables que utilitzarem per el resultat del SELECT del codi postal
$poblacio = $zona = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperam les dades del formulari
    $carrer = isset($_POST['carrer']) ? $_POST['carrer'] : '';
    $numero = isset($_POST['numero']) ? $_POST['numero'] : '';
    $pis = isset($_POST['pis']) ? $_POST['pis'] : null;
    $porta = isset($_POST['porta']) ? $_POST['porta'] : '';
    $codiPostal = isset($_POST['codiPostal']) ? $_POST['codiPostal'] : '';

    if (isset($_POST["btnAgregar"])) {
        // Cridam al FUNCTION per a inserir domicili
        $sql = "SELECT insereixDomicili(?, ?, ?, ?, ?, ?) AS resultado";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $correu, $carrer, $numero, $pis, $porta, $codiPostal);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $resultado);
        mysqli_stmt_fetch($stmt);

        // Tractam el resultat del FUNCTION
        if ($resultado == 1) {
            // S'ha inserit el domicili correctament
            header("Location: dades_comprador.php");
            exit();
        } else {
            // No s'ha pogut realitzar la inserció
            // Missatge que indica que el codi postal es incorrecte
            echo '<script>
                    alert("Codi postal inexistent");
                    setTimeout(function() {
                        window.location.href = "domicilis.php";
                    }, 0);
                </script>';
            exit();
        }

    } elseif (isset($_POST["btnBuscar"])) {
        // Cridam al FUNCTION per a verificar el codi postal
        $sqlVerificarCodigoPostal = "SELECT VerificarCodiPostal(?) AS existeix";
        $stmtVerificarCodigoPostal = mysqli_prepare($con, $sqlVerificarCodigoPostal);
        mysqli_stmt_bind_param($stmtVerificarCodigoPostal, "s", $codiPostal);
        mysqli_stmt_execute($stmtVerificarCodigoPostal);
        $resultadoVerificarCodigoPostal = mysqli_stmt_get_result($stmtVerificarCodigoPostal);
        $filaVerificarCodigoPostal = mysqli_fetch_assoc($resultadoVerificarCodigoPostal);

        // Obtenim resultat
        $existeCodigoPostal = $filaVerificarCodigoPostal['existeix'];

        // Tractam resultat
        if ($existeCodigoPostal == 1) {
            // Si existeix el codi postal, obtenim la població i zona corresponent
            $sqlConsulta = "SELECT poblacio.nom, poblacio.nomZona FROM poblacio WHERE poblacio.codiPostal = ?";
            $stmtConsulta = mysqli_prepare($con, $sqlConsulta);
            mysqli_stmt_bind_param($stmtConsulta, "s", $codiPostal);
            mysqli_stmt_execute($stmtConsulta);
            $resultadoConsulta = mysqli_stmt_get_result($stmtConsulta);

            // RObtenim resultat
            $fila = mysqli_fetch_assoc($resultadoConsulta);

            // Asignar los valores a las variables
            $poblacio = $fila['nom'];
            $zona = $fila['nomZona'];
        } else {
            // Codi postal inexistent
            echo '<script>
                    alert("Codi postal inexistent");
                    setTimeout(function() {
                        window.location.href = "domicilis.php";
                    }, 0);
                </script>';
            exit();
        }
    }
    
    // Tancam sessió
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Alta Domicili</title> <!-- titol de la pàgina -->
    <link rel="stylesheet" type="text/css" href="../estil_registre.css"> <!-- estil de la pagina -->
</head>

<body>

<div class="container">
    <div class="form-container">

        <h1>Afegir domicili</h1>    <!-- titol -->

        <form method="post">

            <!-- FORMULARI -->
            <label for="carrer">Carrer:</label>
            <input type="text" name="carrer" value="<?php echo $carrer; ?>" required>

            <label for="numero">Numero:</label>
            <input type="text" name="numero" value="<?php echo $numero; ?>" required>

            <label for="pis">Pis:</label>
            <input type="text" name="pis" value="<?php echo $pis; ?>">

            <label for="porta">Porta:</label>
            <input type="text" name="porta" value="<?php echo $porta; ?>">

            <label for="codiPostal">Codi Postal:</label>
            <input type="text" name="codiPostal" value="<?php echo $codiPostal; ?>" required>

             <!-- Botó de comprobar codi postal -->
            <input type="submit" name="btnBuscar" value="Confirmar Codi Postal">

            <label for="poblacio">Població:</label>
            <input type="text" name="poblacio" value="<?php echo $poblacio; ?>" readonly>

            <label for="zona">Zona:</label>
            <input type="text" name="zona" value="<?php echo $zona; ?>" readonly>
            
            <!-- Botó d'agregar domicili -->
            <input type="submit" name="btnAgregar" value="Agregar Domicili">

        </form>

        <?php
        ?>
    </div>

    <!-- Imatge -->
    <div class="image-container">
        <img src="../imatges/logo.jpeg" alt="Imagen de la izquierda">
    </div>

</div>


</body>

</html>

