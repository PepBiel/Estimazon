<?php
include "../connexio.php";
session_start();

// Recupera los datos de la sesión
$id_comprador = $_SESSION['correu'];

// Guardar en la sesión
$_SESSION['correu'] = $id_comprador;

// Verificar si s'ha enviat el formulari
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['venda_id']) && isset($_POST['accion'])) {
        $venda_id = $_POST['venda_id']; 

        // Verificar si el comprador té una comanda sense pagar
        $consulta_comanda = "SELECT idcoman FROM COMANDA WHERE idcomp = '$id_comprador' AND pagat = false";
        $res_comanda = mysqli_query($con, $consulta_comanda);

        if ($res_comanda && mysqli_num_rows($res_comanda) > 0) {
            // El comprador té una comanda sense pagar
            $row_comanda = mysqli_fetch_assoc($res_comanda);
            $id_comanda = $row_comanda['idcoman'];
        } else {
            // El comprador no té comandes sense pagar, llavors crea una nova comanda
            $consulta_insertar_comanda = "INSERT INTO COMANDA (pagat, idcomp) VALUES (false, '$id_comprador')";
            mysqli_query($con, $consulta_insertar_comanda);

            // Obtenir la ID de la nova comanda
            $id_comanda = mysqli_insert_id($con);
        }

        // Verificar si ja existeix una entrada a la taula QUANTITAT per a aquest producte i comanda
        $consulta_existencia = "SELECT * FROM QUANTITAT WHERE idvenda = '$venda_id' AND idcomanda = '$id_comanda'";
        $res_existencia = mysqli_query($con, $consulta_existencia);

        if ($res_existencia && mysqli_num_rows($res_existencia) > 0) {
            // Ja existeix una entrada, llavors incrementar la quantitat
            //decrementar el stock de la venda corresponent 
            $crida_procedure = "CALL Existeix_afegir($venda_id, $id_comanda)";
            mysqli_query($con, $crida_procedure);
            
        } else {
            // No existeix una entrada, llavors inserir una nova
            //decrementar el stock de la venda corresponent
            $crida_procedure = "CALL No_Existeix_afegir($venda_id, $id_comanda)";
            mysqli_query($con, $crida_procedure);
            
        }

        // Pots redirigir l'usuari a una altra pàgina o mostrar un missatge d'èxit
        header("Location: Productes_categoria.php");
        exit();
    }
}

?>



