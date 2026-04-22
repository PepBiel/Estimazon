<?php
// Incluir archivo de conexión a la base de datos
include "connexio.php";

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

    // Consulta SQL para verificar si el correo y la contraseña coinciden
    $sqlConsultaUsuario = "SELECT * FROM controlador WHERE correu = ?";
        
    // Inicializar la sentencia preparada
    $stmtConsultaUsuario = mysqli_prepare($con, $sqlConsultaUsuario);

    // Vincular parámetros
    mysqli_stmt_bind_param($stmtConsultaUsuario, "s", $correu);

    // Ejecutar la consulta
    mysqli_stmt_execute($stmtConsultaUsuario);

    // Obtener resultados de la consulta
    $resultadoConsultaUsuario = mysqli_stmt_get_result($stmtConsultaUsuario);

    if ($resultadoConsultaUsuario && mysqli_num_rows($resultadoConsultaUsuario) > 0) {
        // El correo y la contraseña coinciden
        echo '<script>
                alert("Correu ja registrat");
                setTimeout(function() {
                    window.location.href = "crear_controlador.php";
                }, 0);
            </script>';
        exit(); 
    } else {
        // Preparar la consulta SQL para insertar un nuevo vendedor
        $sql = "INSERT INTO controlador (correu, nom, llinatges, dataNaix, contrasenya) 
        VALUES ('$correu', '$nom', '$llinatges', '$dataNaix', '$contrasenya')";

        // Ejecutar la consulta y verificar si fue exitosa
        if (mysqli_query($con, $sql)) {
        // Almacenar datos en sesión

        header("Location: inici.php");
        exit();
        } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }

        // Cerrar la conexión a la base de datos
        mysqli_close($con);
        
    }


}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta</title>
    <link rel="stylesheet" type="text/css" href="estil_registre.css"> 
</head>
<body>

<div class="container">
    <!-- Contenedor de la imagen a la izquierda -->
    <div class="image-container">
        <img src="imatges/logo.jpeg" alt="Imagen de la izquierda">
    </div>

    <!-- Contenedor del formulario a la derecha -->
    <div class="form-container">
        <h1>Donar alta controlador</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="llinatges">Llinatge:</label>
            <input type="text" id="llinatges" name="llinatges" required>

            <label for="dataNaix">Data de naixament:</label>
            <input type="date" id="dataNaix" name="dataNaix" required>

            <label for="correu">Correu:</label>
            <input type="text" id="correu" name="correu" required>

            <label for="contrasenya">Contrasenya:</label>
            <input type="password" id="contrasenya" name="contrasenya" required>
            
            <input type="submit" value="Agregar Controlador">
        </form>

    </div>
</div>

</body>
</html>