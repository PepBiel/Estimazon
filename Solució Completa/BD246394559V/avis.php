<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Conectar a la base de datos
    include "../connexio.php";
    $idcoman = intval($_POST["idcoman"]);
    $idven = $_POST["idven"];

    // Primer query
    $consulta = "SELECT comanda.idcon, comanda.data
                FROM comanda
                WHERE comanda.idcoman = $idcoman;";
    $resultado = mysqli_query($con, $consulta);
    $fila = mysqli_fetch_array($resultado);

    // Obtener valores del primer query
    $idcon = $fila['idcon'];
    $data = $fila['data'];

    echo $data . "...." . $idven . "...." . $idcon . "....";

    // Segundo query
    $inseriravis = "CALL Inseriravis('$data', '$idven', '$idcon');";
    $resultado2 = mysqli_query($con, $inseriravis);

    if ($resultado2) {
        // La inserción fue exitosa

        // Cerrar la conexión
        mysqli_close($con);

        // Redireccionar a cont_comandes.php
        header("Location: cont_comandes.php");
        exit();
    } else {
        // Hubo un error en la inserción
        echo "Error en la inserción: " . mysqli_error($con);
    }

} else {
    echo "Formulario no recibido";
}


?>