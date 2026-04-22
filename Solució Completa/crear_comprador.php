<?php

include "connexio.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperam dedes del formulari
    $correu = $_POST['correu'];
    $nom = $_POST['nom'];
    $llinatges = $_POST['llinatges'];
    $dataNaix = $_POST['dataNaix'];
    $telef = $_POST['telef'];
    $contrasenya = $_POST['contrasenya'];

    // Cridam a la FUNCTION
    $sql = "SELECT crearComprador(?, ?, ?, ?, ?, ?) AS resultado";
        
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $correu, $nom, $llinatges, $dataNaix, $telef, $contrasenya);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $resultado);
    mysqli_stmt_fetch($stmt);

    // Manejam el resultat de la FUNCTION
    if ($resultado == 1) {
        // Si s'ha inserit el comprador, guardam el correu
        session_start();
        $_SESSION['correu'] = $correu;
        header("Location: domicili/domicilis.php");
        exit();
    } else {
        // Missatge de confirmació
        echo '<script>
                alert("Correu ja registrat");
                setTimeout(function() {
                    window.location.href = "crear_comprador.php";
                }, 0);
            </script>';
        exit();
        
    }

    // tancam connexió
    mysqli_close($con);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta</title> <!-- titol -->
    <link rel="stylesheet" type="text/css" href="estil_registre.css"> <!-- estil pàgina -->
</head>
<body>

<div class="container">
    <!-- imatge -->
    <div class="image-container">
        <img src="imatges/logo.jpeg" alt="Imagen de la izquierda">
    </div>

    <div class="form-container">
        <h1>Donar alta comprador</h1> <!-- titol -->

        <!-- formulari -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="llinatges">Llinatge:</label>
            <input type="text" id="llinatges" name="llinatges" required>

            <label for="dataNaix">Data de naixament:</label>
            <input type="date" id="dataNaix" name="dataNaix" required>

            <label for="telef">Telèfon:</label>
            <input type="text" id="telef" name="telef" required>

            <label for="correu">Correu:</label>
            <input type="text" id="correu" name="correu" required>

            <label for="contrasenya">Contrasenya:</label>
            <input type="password" id="contrasenya" name="contrasenya" required>
            
            <!-- botó -->
            <input type="submit" value="Agregar Comprador">
        </form>

    </div>
</div>

</body>
</html>