<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendes</title>
    <link rel="stylesheet" href="controlador.css">
</head>
<body>

    <h1>Empreses</h1>

    <?php

    include "../connexio.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
            $idcoman = intval($_POST['idcoman']);
            //echo "siuuu:";
            //echo $idcoman;
            $consulta_iddom = "SELECT iddom
                    FROM COMANDA
                    WHERE idcoman = '$idcoman'";
            $res_iddom = mysqli_query($con, $consulta_iddom);
            //echo "....";
            $fila = mysqli_fetch_array($res_iddom);
            //echo $fila['iddom'];
            $iddom = $fila['iddom'];

            if(!empty($iddom)){

                $consulta_empresa = "SELECT EMPRESA.nom,EMPRESA.NIF FROM COMANDA 
                                JOIN DOMICILI ON COMANDA.iddom = DOMICILI.iddom 
                                JOIN POBLACIO ON DOMICILI.codiPostal = POBLACIO.codiPostal 
                                JOIN ZONA ON ZONA.nom = poblacio.nomZona 
                                JOIN R_ZONA_EMPRESA ON r_zona_empresa.nomZona = ZONA.nom 
                                JOIN EMPRESA ON R_ZONA_EMPRESA.NIF = EMPRESA.NIF 
                                WHERE COMANDA.iddom = $iddom;";
    
                $resultado_empresa = mysqli_query($con, $consulta_empresa);
    
                // Verificar si hay resultados
                if ($resultado_empresa->num_rows > 0) {
                    session_start();
    
                    echo "<table>";
                    echo "<tr><th>Empresa</th><th>Acció</th></tr>";
    
                    // Imprimir datos en filas de la tabla
                    while ($fila2 = $resultado_empresa->fetch_assoc()) {
                        $_SESSION['idcoman'] = $idcoman;
                        echo "<tr>";
                        echo $fila2['NIF'];
                        echo "<td>" . $fila2['nom'] . "</td>";
                        echo "<td><form action='upd_empresa.php' method='post'>";
                        echo "<input type='hidden' name='NIF' value='" . $fila2['NIF'] . "'>";
                        echo "<button type='submit' class='btnAvis'>Seleccionar</button>";
                        echo "</form></td>";
                        echo "</tr>";
                    }
    
                    echo "</table>";
                } else {
                    echo "No hay resultados.";
                }

            }else{
                echo "No existeix un domicili associat.";
            }

            

    }else{
        echo "Formulari no rebut";
    }
    

    // Cerrar la conexión
    $con->close();
    ?>

    <script src="script.js"></script>
</body>
</html>