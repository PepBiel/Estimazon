<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title> <!-- títol de la pantalla -->
    <link rel="stylesheet" href="estil_inicial.css">  <!-- estil de la pantalla-->
    
    <script>
        function toggleOptions() {
            var dropdown = document.getElementById("roles");
            dropdown.options[0].style.display = "none"; 
        }
    

        //panell desplegable per a crear usuari.
        function redirectOnChange() {
            var selectedOption = document.getElementById("roles").value;
            if (selectedOption === "comprador") {
                window.location.href = 'crear_comprador.php';
            } else if (selectedOption === "vendedor") {
                window.location.href = 'venedor/crear_venedor.php';
            } else if (selectedOption === "controlador") {
                window.location.href = 'controlador.php';
            }
        }
    </script>
</head>
<body>
    <form action="procesar_login.php" method="post">
        <!-- posar logo de la pagina web -->
        <div class="image-container">
            <img src="imatges/logo.jpeg" alt="Imagen de inicio de sesión" width="500">
        </div>
        
        <!-- opcions del desplegable -->
        <div class="dropdown">
            <label for="roles">Crear Usuari:</label>
            <select name="roles" id="roles" onclick="toggleOptions()" onchange="redirectOnChange()">
                <option value="" style="display:none;"></option>
                <option value="comprador">Comprador</option>
                <option value="vendedor">Vendedor</option>
                <option value="controlador">Controlador</option>
            </select>
        </div>

        <!-- per a l'introducció de l'ususari -->
        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>

        <!-- per a l'introducció de la contrasenya -->
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <!-- botons de cada rol a elegir -->
        <input type="submit" name="butComp" value="Comprador">
        <input type="submit" name="butVen" value="Venedor">
        <input type="submit" name="butContr" value="Controlador">
    </form>
</body>
</html>


       



