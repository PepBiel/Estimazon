<?php

include "connexio.php";

// inicial SESSION
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //guardam la contrasenya i el correu dintre del session
    $usuario = $_POST['usuario'];
    $_SESSION['correu'] = $usuario;

    $contrasena = $_POST['contrasena'];
    $_SESSION['contrasena'] = $contrasena;

    // declaram variable
    $tipusUsuari = '';

    //obtenim el rol del usuari que ha polsat el botó
    if (isset($_POST["butVen"])) {
        $tipusUsuari = 'vendedor';
    } elseif (isset($_POST["butComp"])) {
        $tipusUsuari = 'comprador';
    } elseif (isset($_POST["butContr"])) {
        $tipusUsuari = 'controlador';
    }

    // Cridam a la FUNCTION.
    //retorna el tipus d'usuari si la contrasenya i correu son correctes
    //retorna no_valid o no_trobat si el correu o contrasenya son incorrectes o no s'han trobat
    $sql = "SELECT VerificarUsuariContrasenya(?, ?, ?) AS resultat";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $usuario, $contrasena, $tipusUsuari);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $resultat);
    mysqli_stmt_fetch($stmt);

    // Tractam el resultat
   if ($resultat == 'vendedor') {
        // Si el venedor ha introduit la contrasenya i correu correctament, entren com a venedors
        header("Location: venedor/venedor.php"); // ens direccionam al php de venedor
        exit();
    } elseif ($resultat == 'comprador') {
        // Si el comprador ha introduit la contrasenya i correu correctament, entren com a compradors
        header("Location: comprador/comprador.php"); // ens dirigim al php de comprador
        exit();
    } elseif ($resultat == 'controlador') {
        // Si el controlador ha introduit la contrasenya i correu correctament, entren com a controladors
        header("Location: controlador.php"); // ens dirigim al php de controlador
        exit();
    } else {
        // Mostram el missatge de contrasenya o correu incorrectes
        echo '<script>
                alert("Correu o contrasenya incorrectes");
                setTimeout(function() {
                    window.location.href = "inici.php";
                }, 0);
            </script>';
        exit();
    }
}

// Tancam la connexió
mysqli_close($con);

?>
