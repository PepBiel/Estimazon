<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
    $nif = $_POST['NIF'];
    session_start();
    $idComanda = $_SESSION['idcoman'];
    //echo "nif";
    //echo $nif;
    //echo "idComanda";
    //echo $idComanda;
    // Conectar a la base de datos
    include "../connexio.php";

    // Actualizar la base de datos (ajusta según tu necesidad)
    $actualizacion = "UPDATE comanda SET comanda.NIF = '$nif' WHERE idcoman = '$idComanda'";
    if (mysqli_query($con, $actualizacion)) {
        // La actualización fue exitosa

        // Cerrar la conexión
        mysqli_close($con);

        // Redireccionar a cont_comandes.php
        header("Location: cont_comandes.php");
        exit();
    } else {
        // Hubo un error en la actualización
        echo "Error en la actualización: " . mysqli_error($con);
    }

}else{
    echo "Formulari no rebut";
}
?>
