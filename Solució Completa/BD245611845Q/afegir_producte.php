<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include "../connexio.php";

$nom = $descripcio = $foto = $categoria = '';
$stock = $precio = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nombre'];
    $descripcio = $_POST['descripcion'];
    $foto = $_POST['foto'];
    $categoria = $_POST['categoria'];
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];
    $correuVen = $_SESSION['correu'];

    // Verificar si el producto ya existe en la base de datos
    $check_query = "SELECT * FROM PRODUCTE WHERE nom = '$nom'";
    $result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Si el producto existe, crea una nueva venta para ese producto
        $row = mysqli_fetch_assoc($result);
        $idProducto = $row['idprod'];

        $insert_venta_query = "INSERT INTO VENDA (preu, data, stock, idprod, idven) 
                               VALUES ($precio, CURDATE(), $stock, '$idProducto', '$correuVen')";
        if (mysqli_query($con, $insert_venta_query)) {
            echo '<script>
                    alert("Creada una venta nova per el producte ja existent);
                    window.location.href = "venedor.php";
                </script>';
            exit();
        } else {
            echo "Error al crear la venta: " . mysqli_error($con);
        }
    } else {
        // Si el producto no existe, insertarlo en la tabla PRODUCTE
        $insert_query = "INSERT INTO PRODUCTE (nom, descripcio, foto, nomC) 
                         VALUES ('$nom', '$descripcio', '$foto', '$categoria')";

        if (mysqli_query($con, $insert_query)) {
            // Obtener el ID del nuevo producto insertado
            $idProducto = mysqli_insert_id($con);

            // Crear una nueva venta para el producto recién insertado
            $insert_venta_query = "INSERT INTO VENDA (preu, data, stock, idprod, idven) 
                                   VALUES ($precio, CURDATE(), $stock, '$idProducto', '$correuVen')";
            if (mysqli_query($con, $insert_venta_query)) {
                echo '<script>
                        alert("Nou producte afegit a la tenda. Venta creada correctament");
                        window.location.href = "venedor.php";
                    </script>';
                exit();
            } else {
                echo "Error al crear la venta: " . mysqli_error($con);
            }
        } else {
            echo "Error al agregar el producto: " . mysqli_error($con);
        }
    }

    mysqli_close($con);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Afegir producte</title>
    <link rel="stylesheet" type="text/css" href="../estil_registre.css">
</head>
<body>

<div class="container">
    <div class="image-container">
        <img src="../imatges/logo.jpeg" alt="Imagen de la izquierda">
    </div>

    <a href="venedor.php" class="back-button">Tornar a les ventes</a>

    <div class="form-container">
        <h1>Publicar Productes</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="nombre">Nom del producte:</label>
            <input type="text" id="nombre" name="nombre" required><br><br>

            <label for="descripcion">Descripció:</label>
            <textarea id="descripcion" name="descripcion" rows="4" cols="50"></textarea><br><br>

            <label for="foto">URL de la Foto:</label>
            <input type="text" id="foto" name="foto"><br><br>

            <label for="categoria">Nom de la Categoría:</label>
            <select id="categoria" name="categoria" required>
                <?php
                // Obtener las categorías de la tabla CATEGORIA
                $categorias_query = "SELECT nom FROM CATEGORIA";
                $result_categorias = mysqli_query($con, $categorias_query);

                // Array para almacenar las categorías
                $categorias = array();
                while ($row = mysqli_fetch_assoc($result_categorias)) {
                    $categorias[] = $row['nom'];
                }

                // Mostrar las categorías como opciones en el menú desplegable
                foreach ($categorias as $categoria) {
                    echo "<option value='$categoria'>$categoria</option>";
                }
                ?>
            </select><br><br>

            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required><br><br>

            <label for="precio">Preu:</label>
            <input type="number" id="precio" name="precio" required><br><br>

            <input type="submit" value="Agregar producte">
        </form>
    </div>
</div>

</body>
</html>


