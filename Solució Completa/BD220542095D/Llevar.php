<?php
include "../connexio.php";
// Inicia la sesión
session_start();

// Recupera los datos de la sesión
$correu = $_SESSION['correu'];

// Guardar en la sesión
$_SESSION['correu'] = $correu;



// Verificar si s'ha enviat el formulari
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['venda_id']) && isset($_POST['accion'])) {
        
        $venda_id = $_POST['venda_id']; 
        

        // Verificar si el comprador té una comanda sense pagar
        $consulta_comanda = "SELECT idcoman FROM COMANDA WHERE idcomp = '$correu' AND pagat = false";
        $res_comanda = mysqli_query($con, $consulta_comanda);

        
        if ($res_comanda && mysqli_num_rows($res_comanda) > 0) {
            
            // El comprador té una comanda sense pagar
            $row_comanda = mysqli_fetch_assoc($res_comanda);
            $id_comanda = $row_comanda['idcoman'];
        }
        
        // Verificar si ja existeix una entrada a la taula QUANTITAT per a aquest producte i comanda
        $consulta_existencia = "SELECT * FROM QUANTITAT WHERE idvenda = '$venda_id' AND idcomanda = '$id_comanda'";
        $res_existencia = mysqli_query($con, $consulta_existencia);

        if ($res_existencia && mysqli_num_rows($res_existencia) > 0) {
            // Ja existeix una entrada, llavors incrementar la quantitat
            //decrementar el stock de la venda corresponent 
            $crida_procedure = "CALL Llevar($venda_id, $id_comanda)";
            mysqli_query($con, $crida_procedure);
            
            
        }

        // Pots redirigir l'usuari a una altra pàgina o mostrar un missatge d'èxit
        header("Location: comanda.php");
        exit();
    }
}

