<?php
// Inicia la sesión
session_start();

// Resto del código
include "../connexio.php";
$correu = $_SESSION['correu'];
$_SESSION['correu'] = $correu;
$consulta = "select * from categoria";
$res = mysqli_query($con, $consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Elegante</title>
    <link rel="stylesheet" href="comprador.css">
</head>
<body>
    <a href="tancar_sesio.php" class="logout">Tancar sessió</a>

    <a href="comanda.php" class="comanda">Comanda</a>

    <div class="container">
        <h1>Categories dels productes</h1>
        <table>
            <tbody>
                <?php
                while ($reg = mysqli_fetch_array($res)) {
                    ?>
                    <tr>
                        <td>
                            <form action="Productes_categoria.php" method="get">
                                <input type="hidden" name="categoria" value="<?php echo urlencode($reg['nom']); ?>">
                                <button type="submit">
                                    <?php echo $reg['nom']; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
