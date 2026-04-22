<?php
include "../connexio.php";

// Inicia la sesión
session_start();

// Recupera los datos de la sesión
$correu = $_SESSION['correu'];

// Guardar en la sesión
$_SESSION['correu'] = $correu;
//si arribam per primera vegada, tenim $GET
//si NO arribam per primera vegada, tenim $session

if (isset($_GET['categoria'])) {
    $categoria = urldecode($_GET['categoria']);
    $_SESSION['categoria']=$categoria;
}else{
    $categoria=$_SESSION['categoria'];
}
// Definir la consulta por defecto (mostrar todos los productos)
$consulta = "SELECT 
                PRODUCTE.nom AS nom_producte, 
                VENDA.preu AS preu,
                VENDA.idvenda AS idvenda,
                VENDEDOR.nom AS nom_venedor
            FROM VENDA
            JOIN PRODUCTE ON VENDA.idprod = PRODUCTE.idprod AND VENDA.stock>0
            JOIN CATEGORIA ON PRODUCTE.nomC = CATEGORIA.nom AND CATEGORIA.nom = '$categoria'
            JOIN VENDEDOR ON VENDA.idven = VENDEDOR.correu";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Modificar la consulta según la opción seleccionada por el usuario
    if (isset($_POST['opcion'])) {
        switch ($_POST['opcion']) {
            case 'nomes_el_preu_mes_baix':
                $consulta = "SELECT 
                                PRODUCTE.nom AS nom_producte, 
                                MIN(VENDA.preu) AS preu,
                                VENDA.idvenda AS idvenda,
                                VENDEDOR.nom AS nom_venedor
                            FROM VENDA
                            JOIN PRODUCTE ON VENDA.idprod = PRODUCTE.idprod AND VENDA.stock>0
                            JOIN CATEGORIA ON PRODUCTE.nomC = CATEGORIA.nom AND CATEGORIA.nom = '$categoria'
                            JOIN VENDEDOR ON VENDA.idven = VENDEDOR.correu
                            GROUP BY PRODUCTE.nom";
                break;
                
            case 'mostrar_tots':
                // La consulta per defecte, definida abans del switch
                break;
                
            case 'Ordre_Alfbètic_descendent':
                $consulta = "SELECT 
                                PRODUCTE.nom AS nom_producte, 
                                VENDA.preu AS preu,
                                VENDA.idvenda AS idvenda,
                                VENDEDOR.nom AS nom_venedor
                            FROM VENDA
                            JOIN PRODUCTE ON VENDA.idprod = PRODUCTE.idprod AND VENDA.stock>0
                            JOIN CATEGORIA ON PRODUCTE.nomC = CATEGORIA.nom AND CATEGORIA.nom = '$categoria'
                            JOIN VENDEDOR ON VENDA.idven = VENDEDOR.correu
                            ORDER BY PRODUCTE.nom DESC";
                break;

            case 'preu_més_alt':
                $consulta = "SELECT 
                                PRODUCTE.nom AS nom_producte, 
                                VENDA.preu AS preu,
                                VENDA.idvenda AS idvenda,
                                VENDEDOR.nom AS nom_venedor
                            FROM VENDA
                            JOIN PRODUCTE ON VENDA.idprod = PRODUCTE.idprod AND VENDA.stock>0
                            JOIN CATEGORIA ON PRODUCTE.nomC = CATEGORIA.nom AND CATEGORIA.nom = '$categoria'
                            JOIN VENDEDOR ON VENDA.idven = VENDEDOR.correu
                            ORDER BY preu DESC";
                break;
        }
    }
}

$res = mysqli_query($con, $consulta);
?>

<!DOCTYPE html>
<html lang="ca"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taula Elegant</title> 
    <link rel="stylesheet" href="estil_productes_categoria.css">  
</head>
<body>

<h1 class="titulo">Productes</h1> 
<div class="container">

    <div class="header">
        <a href="comprador.php"  class="inicio-button">
            <button>Inici</button>
        </a>
        
    </div>

    <!-- Formulario para seleccionar la opción -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="opcion">Selecciona una opció:</label>
        <select name="opcion" id="opcion">
            <option value="nomes_el_preu_mes_baix">Només el preu més baix</option>
            <option value="mostrar_todos" selected>Mostrar tots els productes</option> 
            <option value="preu_més_alt">Preu més alt</option>
            <option value="Ordre_Alfbètic_descendent">Ordre Alfbètic Decendent</option>
        </select>
        <button type="submit">Aplicar</button>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nom Producte</th>
                    <th>Preu</th>
                    <th>Nom Venedor</th>
                    <!--<th>Acció</th>-->
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nom_producte']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['preu']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_venedor']) . "</td>";
                    echo "<td>";
                    
                    // Afegir un formulari amb un botó per executar la consulta
                    
                    echo "<form action='Afegir.php' method='post'>";
                    echo "<input type='hidden' name='venda_id' value='" . $row['idvenda'] . "'>";
                    echo "<button type='submit' name='accion' value='anadir'>Afegir</button>"; 
                    echo "</form>";
                    
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
