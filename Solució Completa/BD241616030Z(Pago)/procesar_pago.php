<?php

include "../connexio.php";

// iniciam sessió
session_start();

if (isset($_POST['boton-pago'])) {
    // Obtenim dades del formulari
    $nombre_titular = $_POST['nombre'];
    $numero_tarjeta = $_POST['numero'];
    $fecha_vencimiento = $_POST['fecha'];
    $cvv = $_POST['cvv'];

    // Obtenim la id comanda
    $idcoman = $_SESSION['idcoman'];

    // realitzam l'update amb pagat true, la id domicili i la data actual
    $sqlUpdateComanda = "UPDATE comanda SET pagat = 1, iddom = ?, data = CURRENT_DATE() WHERE idcoman = ?";
    $stmtUpdateComanda = mysqli_prepare($con, $sqlUpdateComanda);

    // Vinculam paràmetres
    $iddom = $_SESSION['iddom'];
    mysqli_stmt_bind_param($stmtUpdateComanda, "ii", $iddom, $idcoman);

    // Executam sentència
    if (mysqli_stmt_execute($stmtUpdateComanda)) {
        
        mysqli_stmt_close($stmtUpdateComanda);
        mysqli_close($con);

        // Mostram missatge d'ecceptació
        echo '<script>
                alert("Pagament correcte. Putja Acceptar i et redigiras a la pàgina principal");
                setTimeout(function() {
                    window.location.href = "../comprador.php";
                }, 0);
              </script>';
        exit(); 
    } else {
        echo "Error en el procesamiento del pago: " . mysqli_error($con);
    }

    // Tancam sessió
    mysqli_stmt_close($stmtUpdateComanda);
    mysqli_close($con);

} else {
    echo "Error: El formulario no fue enviado correctamente.";
}
?>
