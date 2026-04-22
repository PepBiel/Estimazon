<?php

// Iniciar sesió
session_start();

// Natejam totes les variables de la sessió
$_SESSION = array();

// Destruir la sesió
session_destroy();

// Ens redirigim a la pàgina d'inici
header("Location: ../inici.php");
exit();
?>