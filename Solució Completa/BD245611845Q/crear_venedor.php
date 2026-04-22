<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de conexión a la base de datos
include "../connexio.php";
// Variables para almacenar los datos del formulario
$correu = $nom = $llinatges = $dataNaix = $contrasenya = '';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $correu = $_POST['correu'];
    $nom = $_POST['nom'];
    $llinatges = $_POST['llinatges'];
    $dataNaix = $_POST['dataNaix'];
    $contrasenya = $_POST['contrasenya'];

    /// Llamar a la función almacenada
    $sql = "SELECT crearVenedor(?, ?, ?, ?, ?) AS resultado";
        
    // Inicializar la sentencia preparada
    $stmt = mysqli_prepare($con, $sql);

    // Vincular parámetros y definir los tipos de datos correspondientes
    mysqli_stmt_bind_param($stmt, "sssss", $correu, $nom, $llinatges, $dataNaix, $contrasenya);

    // Ejecutar la consulta
    mysqli_stmt_execute($stmt);

    // Vincular el resultado
    mysqli_stmt_bind_result($stmt, $resultado);

    // Obtener resultados de la función
    mysqli_stmt_fetch($stmt);

    // Manejar el resultado según lo que devuelva la función
    if ($resultado == 1) {
        // Inserción exitosa
        session_start();
        $_SESSION['correu'] = $correu;
        header("Location: venedor.php");
        exit();
    } else {
        // No se ha realizado la inserción
        echo '<script>
                alert("Correu ja registrat");
                setTimeout(function() {
                    window.location.href = "crear_venedor.php";
                }, 0);
            </script>';
        exit();
        
    }

    mysqli_close($con);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Alta</title>
    <link rel="stylesheet" type="text/css" href="../estil_registre.css"> 
</head>
<body>

<div class="container">

    <div class="image-container">
        <img src="../imatges/logo.jpeg" alt="Imagen de la izquierda">
    </div>

    <div class="form-container">
        <h1>Donar alta venedor</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            
            <label for='correu'>Correu:</label>
            <input type="text" name="correu" required><br><br>

            <label for='nom'>Nom:</label>
            <input type="text" name="nom" required ><br><br>

            <label for='llinatge'>Llinatges:</label>
            <input type="text" name="llinatges" required><br><br>

            <label for='data'>Data Naixement:</label>
            <input type="date" name="dataNaix" required><br><br>

            <label for='correu'>Contrasenya:</label>
            <input type="password" name="contrasenya" required><br><br>

            <input type="submit" value="Agregar Vendedor">
        </form>
    </div>
</div>

</body>
</html>

