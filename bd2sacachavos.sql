-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-12-2023 a las 20:26:03
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd2sacachavos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Actualitzar_Estat` (IN `id_vendedor` VARCHAR(30))   BEGIN
    DECLARE avisos_actuals INT;
    DECLARE estat_actual VARCHAR(20);

    SELECT COUNT(*) INTO avisos_actuals FROM AVIS WHERE idven = id_vendedor;

    -- Obtener el estado actual del vendedor/a
    SELECT estat INTO estat_actual FROM VENDEDOR WHERE correu = id_vendedor;

    -- Incrementar el contador de avisos
    IF avisos_actuals > 3 AND avisos_actuals < 6 THEN
    	UPDATE VENDEDOR SET estat = 'SOSPITOS' WHERE correu = id_vendedor;
    ELSEIF avisos_actuals >= 6 THEN
        UPDATE VENDEDOR SET estat = 'DOLENT' WHERE correu = id_vendedor;
    END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Existeix_afegir` (IN `venda_id` INT, IN `id_comanda` INT)   BEGIN 

	DECLARE EXIT HANDLER FOR SQLEXCEPTION 

	BEGIN 

		ROLLBACK; 

	END; 

    START TRANSACTION; 

    UPDATE QUANTITAT SET nombre = nombre + 1 WHERE idvenda = venda_id AND idcomanda = id_comanda; 

    UPDATE VENDA SET stock = stock - 1 WHERE idvenda = venda_id; 

    COMMIT; 

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Inseriravis` (IN `p_fecha` DATE, IN `p_idven` VARCHAR(30), IN `p_idcon` VARCHAR(30))   BEGIN
    DECLARE hoy DATE;
    DECLARE diferencia INT;

    SET hoy = CURRENT_DATE();

    SET diferencia = DATEDIFF(hoy,p_fecha);

    IF diferencia > 5 THEN
        INSERT INTO AVIS (idven, idcon) VALUES (p_idven, p_idcon);
        SELECT 'Inserció correcte a AVIS' AS resultado;
    ELSE
        SELECT 'La diferencia no és major a 5' AS resultado;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InserirResguard` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE id_con, id_comp, id_dom, id_rep VARCHAR(30);
    DECLARE nif VARCHAR(20);
    DECLARE fecha DATE;

    DECLARE cur CURSOR FOR
        SELECT idcon, idcomp, iddom, NIF, idrep, data 
        FROM COMANDA 
        WHERE comanda.data = CURDATE() AND pagat = 1;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    loop_cursor: LOOP
        FETCH cur INTO id_con, id_comp, id_dom, nif, id_rep, fecha;
        IF done THEN
            LEAVE loop_cursor;
        END IF;

        INSERT INTO resguard_comanda (idcon, idcomp, iddom, NIF, idrep, resguard_comanda.data)
        VALUES (id_con, id_comp, id_dom, nif, id_rep, fecha);
    END LOOP;

    CLOSE cur;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Llevar` (IN `venda_id` INT, IN `id_comanda` INT)   BEGIN 
	DECLARE num INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
	
    BEGIN 

        ROLLBACK; 

    END; 
	
    START TRANSACTION; 

	  UPDATE QUANTITAT SET nombre = nombre - 1 WHERE idvenda = venda_id AND idcomanda = id_comanda;
      UPDATE VENDA SET stock = stock + 1 WHERE idvenda = venda_id; 
      SELECT nombre INTO num FROM quantitat WHERE quantitat.idvenda =venda_id AND quantitat.idcomanda= id_comanda;
      IF num = 0 THEN
      	DELETE FROM quantitat WHERE quantitat.idvenda = venda_id AND quantitat.idcomanda = id_comanda;
      END if;
    COMMIT; 

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `No_Existeix_afegir` (IN `venda_id` INT, IN `id_comanda` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    START TRANSACTION;
      INSERT INTO QUANTITAT (idcomanda, idvenda, nombre) VALUES (id_comanda, venda_id,1);
      UPDATE VENDA SET stock = stock - 1 WHERE idvenda = venda_id;
    COMMIT;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `crearComprador` (`p_correu` VARCHAR(30), `p_nom` VARCHAR(20), `p_llinatges` VARCHAR(30), `p_dataNaix` DATE, `p_telef` INT(11), `p_contrasenya` VARCHAR(20)) RETURNS INT(11)  BEGIN
    DECLARE total INT;

    -- Verificar si el correo ya está registrado
    SELECT COUNT(*) INTO total FROM comprador WHERE correu = p_correu;

    IF total = 0 THEN
        -- No hay registros con ese correo, realizar la inserción
        INSERT INTO comprador (correu, nom, llinatges, dataNaix, telef, contrasenya) 
        VALUES (p_correu, p_nom, p_llinatges, p_dataNaix, p_telef, p_contrasenya);
        
        -- Devolver 1 para indicar que se ha hecho la inserción
        RETURN 1;
    ELSE
        -- Ya hay un registro con ese correo
        -- Devolver 0 para indicar que no se ha hecho la inserción
        RETURN 0;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `crearVenedor` (`p_correu` VARCHAR(30), `p_nom` VARCHAR(20), `p_llinatges` VARCHAR(30), `p_dataNaix` DATE, `p_contrasenya` VARCHAR(20)) RETURNS INT(11)  BEGIN
    DECLARE total INT;

    -- Verificar si el correo ya está registrado
    SELECT COUNT(*) INTO total FROM VENDEDOR WHERE correu = p_correu;

    IF total = 0 THEN
        -- No hay registros con ese correo, realizar la inserción
        INSERT INTO VENDEDOR (correu, estat, nom, llinatges, dataNaix, contrasenya) 
        VALUES (p_correu, NULL, p_nom, p_llinatges, p_dataNaix, p_contrasenya);
        
        -- Devolver 1 para indicar que se ha hecho la inserción
        RETURN 1;
    ELSE
        -- Ya hay un registro con ese correo
        -- Devolver 0 para indicar que no se ha hecho la inserción
        RETURN 0;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `insereixDomicili` (`p_correu` VARCHAR(30), `p_carrer` VARCHAR(50), `p_numero` INT(11), `p_pis` INT(11), `p_porta` VARCHAR(20), `p_codiPostal` INT(11)) RETURNS INT(11)  BEGIN
    DECLARE total INT;
    
    -- Verificar si ya existe un registro con ese código postal en la tabla poblacio
    SELECT COUNT(*) INTO total FROM poblacio WHERE codiPostal = p_codiPostal;

    -- Si no hay registros con ese código postal, realizar la inserción en la tabla domicili
    IF total > 0 THEN
        INSERT INTO domicili (carrer, numero, pis, porta, idcomp, codiPostal) 
        VALUES (p_carrer, p_numero, p_pis, p_porta, p_correu, p_codiPostal);
        RETURN 1;
    ELSE 
    	RETURN 0;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `VerificarCodiPostal` (`p_codiPostal` INT) RETURNS INT(11)  BEGIN
    DECLARE total INT;

    -- Verificar si el código postal existe
    SELECT COUNT(*) INTO total FROM poblacio WHERE codiPostal = p_codiPostal;

    -- Devolver 1 si el código postal existe, 0 si no
    RETURN IF(total > 0, 1, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `VerificarUsuariContrasenya` (`p_usuari` VARCHAR(30), `p_contrasenya` VARCHAR(20), `p_tipusUsuari` VARCHAR(15)) RETURNS VARCHAR(15) CHARSET utf8mb4 COLLATE utf8mb4_general_ci  BEGIN
    DECLARE resultat INT;

    IF p_tipusUsuari = 'vendedor' THEN
        SELECT COUNT(*) INTO resultat FROM vendedor WHERE correu = p_usuari AND contrasenya = p_contrasenya;
    ELSEIF p_tipusUsuari = 'comprador' THEN
        SELECT COUNT(*) INTO resultat FROM comprador WHERE correu = p_usuari AND contrasenya = p_contrasenya;
    ELSEIF p_tipusUsuari = 'controlador' THEN
        SELECT COUNT(*) INTO resultat FROM controlador WHERE correu = p_usuari AND contrasenya = p_contrasenya;
    ELSE
        -- Tipo de usuario no válido
        RETURN 'no_valid';
    END IF;

    IF resultat > 0 THEN
        RETURN p_tipusUsuari;
    ELSE
        RETURN 'no_trobat';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avis`
--

CREATE TABLE `avis` (
  `idavis` int(11) NOT NULL,
  `idven` varchar(30) DEFAULT NULL,
  `idcon` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `avis`
--

INSERT INTO `avis` (`idavis`, `idven`, `idcon`) VALUES
(1, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(2, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(3, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(4, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(5, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(6, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(7, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(8, 'Carlos_Rodriguez@correu.com', 'javier_lopez@gmail.com'),
(10, 'Pedro_Martinez@correu.com', 'carlos_gonzalez@gmail.com');

--
-- Disparadores `avis`
--
DELIMITER $$
CREATE TRIGGER `Nou_Avis` AFTER INSERT ON `avis` FOR EACH ROW BEGIN
    DECLARE id_vendedor VARCHAR(30);
    
    -- Obtener el ID del vendedor al que se le está insertando un nuevo aviso
    SELECT idven INTO id_vendedor FROM AVIS WHERE idavis = NEW.idavis;

    -- Llamar al procedimiento para registrar el aviso y comprobar el estado del vendedor
    CALL Actualitzar_Estat(id_vendedor);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `nom` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`nom`) VALUES
('Aliments i Begudes'),
('Eines'),
('Electrònica'),
('Esport'),
('Juguetes i Jocs'),
('Llar i Cuina'),
('Llibres'),
('Música'),
('Roba i Accesoris'),
('Salut i Bellesa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comanda`
--

CREATE TABLE `comanda` (
  `idcoman` int(11) NOT NULL,
  `pagat` tinyint(1) DEFAULT NULL,
  `idcon` varchar(30) DEFAULT NULL,
  `idcomp` varchar(30) DEFAULT NULL,
  `iddom` int(11) DEFAULT NULL,
  `NIF` varchar(20) DEFAULT NULL,
  `idrep` varchar(30) DEFAULT NULL,
  `data` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comanda`
--

INSERT INTO `comanda` (`idcoman`, `pagat`, `idcon`, `idcomp`, `iddom`, `NIF`, `idrep`, `data`) VALUES
(2, 1, 'carlos_gonzalez@gmail.com', 'maria_ramirez@example.com', 23, NULL, NULL, '2023-12-08'),
(3, 0, 'carlos_gonzalez@gmail.com', 'maria_ramirez@example.com', NULL, NULL, NULL, '2023-12-15'),
(4, 0, NULL, 'pep@correu.com', NULL, NULL, NULL, '2023-12-15'),
(5, 0, NULL, 'joan_lora@correu.com', NULL, NULL, NULL, NULL),
(6, 1, NULL, 'ana_gutierrez@correu.com', 41, NULL, NULL, '2023-12-15'),
(7, 0, NULL, 'alex@gmail.com', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprador`
--

CREATE TABLE `comprador` (
  `correu` varchar(30) NOT NULL,
  `nom` varchar(20) DEFAULT NULL,
  `llinatges` varchar(30) DEFAULT NULL,
  `dataNaix` date DEFAULT NULL,
  `telef` int(11) DEFAULT NULL,
  `contrasenya` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comprador`
--

INSERT INTO `comprador` (`correu`, `nom`, `llinatges`, `dataNaix`, `telef`, `contrasenya`) VALUES
('alex@gmail.com', 'Alex', 'Morro', '2002-02-02', 978867563, 'alex123'),
('ana_gutierrez@correu.com', 'Ana', 'Gutiérrez', '1987-08-12', 655123456, 'comprador1'),
('david_lopez@example.com', 'David', 'López', '1992-06-18', 655333444, 'comprador4'),
('elena_fernandez@correu.com', 'Elena', 'Fernández', '1980-11-02', 655111222, 'comprador3'),
('javier_martinez@correu.com', 'Javier', 'Martínez', '1995-03-25', 655987654, 'comprador2'),
('joan_lora@correu.com', 'Joan', 'Lora', '2003-03-20', 983920394, '1234'),
('maria_ramirez@example.com', 'María', 'Ramírez', '1988-09-05', 655555555, 'comprador5'),
('pep@correu.com', 'Josep', 'Fornes', '2000-04-30', 547458521, 'pep');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `controlador`
--

CREATE TABLE `controlador` (
  `correu` varchar(30) NOT NULL,
  `nom` varchar(20) DEFAULT NULL,
  `llinatges` varchar(30) DEFAULT NULL,
  `dataNaix` date DEFAULT NULL,
  `contrasenya` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `controlador`
--

INSERT INTO `controlador` (`correu`, `nom`, `llinatges`, `dataNaix`, `contrasenya`) VALUES
('carlos_gonzalez@gmail.com', 'Carlos', 'González', '1985-04-15', 'controlador1'),
('javier_lopez@gmail.com', 'Javier', 'López', '1988-07-11', 'controlador3'),
('laura_martinez@gmail.com', 'Laura', 'Martínez', '1990-09-22', 'controlador2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `domicili`
--

CREATE TABLE `domicili` (
  `iddom` int(11) NOT NULL,
  `carrer` varchar(50) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `pis` int(11) DEFAULT NULL,
  `porta` varchar(20) DEFAULT NULL,
  `idcomp` varchar(30) DEFAULT NULL,
  `codiPostal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `domicili`
--

INSERT INTO `domicili` (`iddom`, `carrer`, `numero`, `pis`, `porta`, `idcomp`, `codiPostal`) VALUES
(23, 'c/ alzina', 56, 1, 'B', 'maria_ramirez@example.com', 7040),
(24, 'c/ baix', 34, 2, 'A', 'maria_ramirez@example.com', 7313),
(41, 'Sol', 17, 3, 'A', 'ana_gutierrez@correu.com', 7440),
(42, 'Ruu', 2, 1, 'B', 'alex@gmail.com', 7440);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `NIF` varchar(20) NOT NULL,
  `nom` varchar(20) DEFAULT NULL,
  `telf` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`NIF`, `nom`, `telf`) VALUES
('123456789A', 'Envíos Envidiosos', 123456789),
('567890123C', 'Fast & Fly', 567890123),
('987654321B', 'Paquete Astral', 987654321);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrega`
--

CREATE TABLE `entrega` (
  `identrega` int(11) NOT NULL,
  `intents` int(11) DEFAULT NULL,
  `idinici` int(11) DEFAULT NULL,
  `idcomanda` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidencia`
--

CREATE TABLE `incidencia` (
  `idinci` int(11) NOT NULL,
  `nom` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `poblacio`
--

CREATE TABLE `poblacio` (
  `codiPostal` int(11) NOT NULL,
  `nom` varchar(20) DEFAULT NULL,
  `nomZona` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `poblacio`
--

INSERT INTO `poblacio` (`codiPostal`, `nom`, `nomZona`) VALUES
(7040, 'Palma', 'PALMA DE MALLORCA'),
(7100, 'Sóller', 'TRAMUNTANA'),
(7109, 'Fornalutx', 'TRAMUNTANA'),
(7110, 'Bunyola', 'TRAMUNTANA'),
(7140, 'Sencelles', 'PLA DE MALLORCA'),
(7141, 'Marratxí', 'RAIGUER'),
(7142, 'Santa Eugènia', 'PLA DE MALLORCA'),
(7144, 'Costitx', 'PLA DE MALLORCA'),
(7150, 'Antratx', 'TRAMUNTANA'),
(7170, 'Valldemossa', 'TRAMUNTANA'),
(7179, 'Deià', 'TRAMUNTANA'),
(7184, 'Calvià', 'TRAMUNTANA'),
(7190, 'Esporles', 'TRAMUNTANA'),
(7191, 'Banyalbufar', 'TRAMUNTANA'),
(7192, 'Estellencs', 'TRAMUNTANA'),
(7194, 'Puigpunyent', 'TRAMUNTANA'),
(7200, 'Felanitx', 'MIGJORN'),
(7210, 'Algaida', 'PLA DE MALLORCA'),
(7230, 'Montuïri', 'PLA DE MALLORCA'),
(7240, 'Sant Joan', 'PLA DE MALLORCA'),
(7250, 'Villafranca de Bonan', 'PLA DE MALLORCA'),
(7260, 'Porreres', 'PLA DE MALLORCA'),
(7300, 'Inca', 'RAIGUER'),
(7310, 'Campanet', 'RAIGUER'),
(7311, 'Búger', 'RAIGUER'),
(7312, 'Mancor de la Vall', 'RAIGUER'),
(7313, 'Selva', 'RAIGUER'),
(7315, 'Escorca', 'TRAMUNTANA'),
(7320, 'Santa Maria del Camí', 'RAIGUER'),
(7330, 'Consell', 'RAIGUER'),
(7340, 'Alaró', 'RAIGUER'),
(7350, 'Binissalem', 'RAIGUER'),
(7360, 'Lloseta', 'RAIGUER'),
(7400, 'Alcúdia', 'RAIGUER'),
(7420, 'Sa Pobla', 'RAIGUER'),
(7430, 'Llubí', 'PLA DE MALLORCA'),
(7440, 'Muro', 'PLA DE MALLORCA'),
(7450, 'Santa Margalida', 'PLA DE MALLORCA'),
(7460, 'Pollença', 'TRAMUNTANA'),
(7500, 'Manacor', 'LLEVANT'),
(7510, 'Sineu', 'PLA DE MALLORCA'),
(7518, 'Lloret de Vistalegre', 'PLA DE MALLORCA'),
(7519, 'Maria de la Salut', 'PLA DE MALLORCA'),
(7520, 'Petra', 'PLA DE MALLORCA'),
(7529, 'Ariany', 'PLA DE MALLORCA'),
(7530, 'St Llorenç Cardassar', 'LLEVANT'),
(7550, 'Son Servera', 'LLEVANT'),
(7570, 'Artà', 'LLEVANT'),
(7580, 'Capdepera', 'LLEVANT'),
(7620, 'Llucmajor', 'MIGJORN'),
(7630, 'Campos', 'MIGJORN'),
(7640, 'Ses Salines', 'MIGJORN'),
(7650, 'Santanyí', 'MIGJORN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producte`
--

CREATE TABLE `producte` (
  `idprod` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `descripcio` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nomC` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producte`
--

INSERT INTO `producte` (`idprod`, `nom`, `descripcio`, `foto`, `nomC`) VALUES
(1, 'Mòbil Samsung Galaxy S21', 'Telèfon mòbil avançat amb pantalla AMOLED i càmera d\'alta resolució.', 'samsung_s21.jpg', 'Electrònica'),
(2, 'Televisor LG 4K UHD', 'Televisor intel·ligent amb resolució 4K i tecnologia OLED per a una qualitat d\'imatge impressionant.', 'lg_tv.jpg', 'Electrònica'),
(3, 'Portàtil HP Envy', 'Portàtil ultralleuger amb processador potent i pantalla tàctil d\'alta definició.', 'hp_envy.jpg', 'Electrònica'),
(4, 'Auriculars Sony WH-1000XM4', 'Auriculars sense fil amb cancel·lació de soroll i so d\'alta qualitat.', 'sony_headphones.jpg', 'Electrònica'),
(5, 'Càmera Canon EOS R5', 'Càmera mirrorless amb sensor d\'alta resolució i capacitats d\'enregistrament de vídeo 8K.', 'canon_eos_r5.jpg', 'Electrònica'),
(6, 'Smartwatch Apple Watch Series 7', 'Relotge intel·ligent amb pantalla sempre encesa i seguiment avançat de la salut.', 'apple_watch.jpg', 'Electrònica'),
(7, 'Tauleta Samsung Galaxy Tab S7', 'Tauleta Android amb pantalla Super AMOLED i rendiment potent multitasca.', 'samsung_tab_s7.jpg', 'Electrònica'),
(8, 'Consola de Jocs Xbox Series X', 'Consola de nova generació amb capacitat per a jocs en 4K i tecnologia de traçat de raigs.', 'xbox_series_x.jpg', 'Electrònica'),
(9, 'Impressora Epson EcoTank', 'Impressora multifunció amb sistema de tanc de tinta recarregable per imprimir a baix cost.', 'epson_printer.jpg', 'Electrònica'),
(10, 'Altaveus Bose SoundLink Revolve', 'Altaveus Bluetooth amb so envoltant de 360 graus i disseny portàtil.', 'bose_speakers.jpg', 'Electrònica'),
(11, 'Joc de claus de trinquete', 'Joc de claus amb trinquete per a reparacions mecàniques.', 'claus_trinquete.jpg', 'Eines'),
(12, 'Cortacésped eléctrico Bosch', 'Cortacésped elèctric per a manteniment del jardí.', 'cortacesped_bosch.jpg', 'Eines'),
(13, 'Taladro de impacto Makita', 'Taladradora de percussió per a treballs de construcció i bricolatge.', 'taladro_makita.jpg', 'Eines'),
(14, 'Serrucho de poda profesional', 'Serra de poda especialitzada per a jardiners.', 'serrucho_poda.jpg', 'Eines'),
(15, 'Juego de llaves Allen', 'Joc de claus Allen per a ajustos precisos.', 'llaves_allen.jpg', 'Eines'),
(16, 'Motosierra Husqvarna', 'Motosierra potente per a la tala d\'arbres.', 'motosierra_husqvarna.jpg', 'Eines'),
(17, 'Destornillador eléctrico Black & Decker', 'Destornillador elèctric per a tasques de muntatge.', 'destornillador_black_decker.jpg', 'Eines'),
(18, 'Paleta de jardín con mango largo', 'Pala especial amb mànec llarg per a tasques de jardineria.', 'paleta_jardin.jpg', 'Eines'),
(19, 'Compresor de aire Stanley', 'Compresor d\'aire per a eines pneumàtiques.', 'compresor_stanley.jpg', 'Eines'),
(20, 'Set de llaves de vaso', 'Joc de claus de vaso per a reparacions mecàniques.', 'llaves_vaso.jpg', 'Eines'),
(21, 'Camiseta FCBarcelona', 'Nueva camiseta 2023', 'fotofcb', 'Esport'),
(22, 'Bicicleta de Muntanya', 'Bicicleta tot terreny amb suspensió total per a aventures fora de carretera.', 'bicicleta_muntanya.jpg', 'Esport'),
(23, 'Raqueta de Tennis Wilson Pro Staff', 'Raqueta de tennis professional amb tecnologia avançada.', 'raqueta_tennis_wilson.jpg', 'Esport'),
(24, 'Pantalons de Running Adidas', 'Pantalons lleugers i transpirables per a la pràctica de la cursa.', 'pantalons_running_adidas.jpg', 'Esport'),
(25, 'Màquina de Pes ZYX Fitness', 'Màquina de pes amb múltiples estacions per a un entrenament complet.', 'maquina_pes_zyx.jpg', 'Esport'),
(26, 'Cinta de Correr NordicTrack', 'Cinta de córrer elèctrica amb inclinació automàtica i pantalla tàctil.', 'cinta_correr_nordictrack.jpg', 'Esport'),
(27, 'Pala de Pàdel Bullpadel Vertex', 'Pala de pàdel amb forma de llàgrima i tecnologia de carboni per a millor potència.', 'pala_padel_bullpadel.jpg', 'Esport'),
(28, 'Equipació de Futbol Nike', 'Equipació completa amb samarreta, pantalons i mitjons per a jugadors de futbol.', 'equipacio_futbol_nike.jpg', 'Esport'),
(29, 'Canyes de Golf Callaway Apex', 'Canyes de golf premium amb tecnologia avançada per a distàncies llargues.', 'canyes_golf_callaway.jpg', 'Esport'),
(30, 'Patins en Línia Rollerblade', 'Patins en línia ajustables amb rodes de alta qualitat per a un patinatge suau.', 'patins_linea_rollerblade.jpg', 'Esport'),
(31, 'Estació de Fitness Multifunció', 'Estació de fitness amb múltiples opcions d\'entrenament per a tot el cos.', 'estacio_fitness_multifuncio.jpg', 'Esport'),
(32, 'Vi Tinto Reserva 2015', 'Vino tinto reserva con cuerpo y sabor intenso.', 'vino_tinto_reserva.jpg', 'Aliments i Begudes'),
(33, 'Aceite de Oliva Extra Virgen', 'Aceite de oliva de alta calidad, prensado en frío.', 'aceite_oliva_extra_virgen.jpg', 'Aliments i Begudes'),
(34, 'Café Arábica Gourmet', 'Café arábica gourmet de origen único con notas sutiles.', 'cafe_arabica_gourmet.jpg', 'Aliments i Begudes'),
(35, 'Chocolate Negro 70%', 'Chocolate negro con un 70% de cacao, intenso y delicioso.', 'chocolate_negro_70.jpg', 'Aliments i Begudes'),
(36, 'Miel de Abeja Pura', 'Miel de abeja pura y natural, recolectada de manera sostenible.', 'miel_abeja_pura.jpg', 'Aliments i Begudes'),
(37, 'Pasta de Trigo Integral', 'Pasta de trigo integral rica en fibra y nutrientes.', 'pasta_trigo_integral.jpg', 'Aliments i Begudes'),
(38, 'Salmón Ahumado Escocés', 'Salmón ahumado de origen escocés, ahumado lentamente para un sabor único.', 'salmon_ahumado_escoces.jpg', 'Aliments i Begudes'),
(39, 'Mermelada de Fresa Casera', 'Mermelada casera de fresa, sin conservantes ni colorantes artificiales.', 'mermelada_fresa_casera.jpg', 'Aliments i Begudes'),
(40, 'Arroz Basmati Orgánico', 'Arroz basmati orgánico de grano largo y fragante.', 'arroz_basmati_organico.jpg', 'Aliments i Begudes'),
(41, 'Infusión de Hierbas Relajantes', 'Infusión de hierbas relajantes, perfecta para descansar y relajarse.', 'infusion_hierbas_relajantes.jpg', 'Aliments i Begudes'),
(42, 'Puzzle de 1000 Piezas', 'Puzzle desafiante con 1000 piezas para horas de entretenimiento.', 'puzzle_1000_piezas.jpg', 'Juguetes i Jocs'),
(43, 'Muñeca Articulada Barbie', 'Muñeca Barbie con articulaciones para crear diferentes poses.', 'muneca_barbie.jpg', 'Juguetes i Jocs'),
(44, 'Cubo Rubik 3x3', 'Cubo de Rubik clásico en 3x3 para poner a prueba tus habilidades.', 'cubo_rubik_3x3.jpg', 'Juguetes i Jocs'),
(45, 'Set de Construcción LEGO', 'Set de construcción LEGO para crear diversas estructuras y personajes.', 'set_lego.jpg', 'Juguetes i Jocs'),
(46, 'Pelota de Fútbol para Niños', 'Pelota de fútbol diseñada especialmente para niños, ligera y colorida.', 'pelota_futbol_ninos.jpg', 'Juguetes i Jocs'),
(47, 'Rompecabezas de Madera', 'Rompecabezas de madera con formas únicas y colores vibrantes.', 'rompecabezas_madera.jpg', 'Juguetes i Jocs'),
(48, 'Juego de Mesa Monopoly', 'Clásico juego de mesa Monopoly para toda la familia.', 'monopoly.jpg', 'Juguetes i Jocs'),
(49, 'Peluche Suave de Unicornio', 'Peluche suave y adorable de unicornio, perfecto para abrazar.', 'peluche_unicornio.jpg', 'Juguetes i Jocs'),
(50, 'Coche de Control Remoto', 'Coche de control remoto con funciones avanzadas para carreras emocionantes.', 'coche_control_remoto.jpg', 'Juguetes i Jocs'),
(51, 'Juguetes Educativos de Construcción', 'Set de juguetes educativos de construcción para estimular la creatividad.', 'juguetes_educativos_construccion.jpg', 'Juguetes i Jocs');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quantitat`
--

CREATE TABLE `quantitat` (
  `idcomanda` int(11) NOT NULL,
  `idvenda` int(11) NOT NULL,
  `nombre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `quantitat`
--

INSERT INTO `quantitat` (`idcomanda`, `idvenda`, `nombre`) VALUES
(2, 3, 0),
(2, 9, 0),
(2, 15, 1),
(2, 20, 1),
(2, 22, 4),
(2, 24, 1),
(2, 26, 0),
(2, 27, 1),
(2, 28, 1),
(2, 29, 6),
(2, 30, 1),
(2, 35, 4),
(3, 22, 1),
(3, 27, 1),
(4, 9, 1),
(4, 10, 0),
(4, 20, 1),
(4, 22, 0),
(4, 29, 0),
(4, 35, 2),
(5, 20, 3),
(6, 4, 1),
(6, 22, 1),
(6, 29, 1),
(7, 36, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repartidor`
--

CREATE TABLE `repartidor` (
  `correu` varchar(30) NOT NULL,
  `nom` varchar(20) DEFAULT NULL,
  `llinatges` varchar(30) DEFAULT NULL,
  `dataNaix` date DEFAULT NULL,
  `contrasenya` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resguard_comanda`
--

CREATE TABLE `resguard_comanda` (
  `idcoman` int(11) NOT NULL,
  `idcon` varchar(30) DEFAULT NULL,
  `idcomp` varchar(30) DEFAULT NULL,
  `iddom` int(11) DEFAULT NULL,
  `NIF` varchar(20) DEFAULT NULL,
  `idrep` varchar(30) DEFAULT NULL,
  `data` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resguard_comanda`
--

INSERT INTO `resguard_comanda` (`idcoman`, `idcon`, `idcomp`, `iddom`, `NIF`, `idrep`, `data`) VALUES
(17, 'carlos_gonzalez@gmail.com', 'maria_ramirez@example.com', 23, NULL, NULL, '2023-12-15'),
(18, NULL, 'ana_gutierrez@correu.com', 41, NULL, NULL, '2023-12-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `r_zona_empresa`
--

CREATE TABLE `r_zona_empresa` (
  `nomZona` varchar(20) NOT NULL,
  `NIF` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `r_zona_empresa`
--

INSERT INTO `r_zona_empresa` (`nomZona`, `NIF`) VALUES
('LLEVANT', '123456789A'),
('MIGJORN', '123456789A'),
('PALMA DE MALLORCA', '987654321B'),
('PLA DE MALLORCA', '987654321B'),
('RAIGUER', '567890123C'),
('TRAMUNTANA', '567890123C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venda`
--

CREATE TABLE `venda` (
  `idvenda` int(11) NOT NULL,
  `preu` decimal(10,2) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `idprod` int(11) DEFAULT NULL,
  `idven` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `venda`
--

INSERT INTO `venda` (`idvenda`, `preu`, `data`, `stock`, `idprod`, `idven`) VALUES
(1, '599.99', '2023-12-09', 10, 1, 'Juan_Garcia@correu.com'),
(2, '799.99', '2023-12-09', 20, 2, 'Maria_Lopez@correu.com'),
(3, '149.99', NULL, 30, 3, 'Pedro_Martinez@correu.com'),
(4, '249.99', '2023-12-09', 14, 4, 'Laura_Gomez@correu.com'),
(5, '39.99', '2023-12-09', 25, 5, 'Carlos_Rodriguez@correu.com'),
(6, '699.99', '2023-12-09', 8, 1, 'Pedro_Martinez@correu.com'),
(7, '849.99', '2023-12-09', 18, 2, 'Juan_Garcia@correu.com'),
(8, '159.99', '2023-12-09', 27, 3, 'Laura_Gomez@correu.com'),
(9, '229.99', '2023-12-09', 10, 4, 'Maria_Lopez@correu.com'),
(10, '49.99', '2023-12-09', 22, 5, 'Carlos_Rodriguez@correu.com'),
(11, '619.99', '2023-12-09', 6, 1, 'Laura_Gomez@correu.com'),
(12, '779.99', '2023-12-09', 15, 2, 'Pedro_Martinez@correu.com'),
(13, '129.99', '2023-12-09', 35, 3, 'Carlos_Rodriguez@correu.com'),
(14, '259.99', '2023-12-09', 17, 4, 'Juan_Garcia@correu.com'),
(15, '34.99', '2023-12-09', 27, 5, 'Maria_Lopez@correu.com'),
(16, '589.99', '2023-12-09', 8, 6, 'Maria_Lopez@correu.com'),
(17, '759.99', '2023-12-09', 15, 7, 'Juan_Garcia@correu.com'),
(18, '139.99', '2023-12-09', 25, 8, 'Carlos_Rodriguez@correu.com'),
(19, '219.99', '2023-12-09', 10, 9, 'Pedro_Martinez@correu.com'),
(20, '44.99', '2023-12-09', 15, 10, 'Laura_Gomez@correu.com'),
(21, '669.99', '2023-12-09', 5, 11, 'Carlos_Rodriguez@correu.com'),
(22, '819.99', '2023-12-09', 12, 12, 'Juan_Garcia@correu.com'),
(23, '129.99', '2023-12-09', 30, 13, 'Pedro_Martinez@correu.com'),
(24, '239.99', '2023-12-09', 14, 14, 'Laura_Gomez@correu.com'),
(25, '29.99', '2023-12-09', 25, 15, 'Maria_Lopez@correu.com'),
(26, '599.99', '2023-12-09', 12, 16, 'Pedro_Martinez@correu.com'),
(27, '749.99', '2023-12-09', 20, 17, 'Juan_Garcia@correu.com'),
(28, '119.99', '2023-12-09', 31, 18, 'Laura_Gomez@correu.com'),
(29, '249.99', '2023-12-09', 11, 19, 'Carlos_Rodriguez@correu.com'),
(30, '39.99', '2023-12-09', 27, 20, 'Maria_Lopez@correu.com'),
(31, '579.99', '2023-12-09', 8, 6, 'Laura_Gomez@correu.com'),
(32, '739.99', '2023-12-09', 15, 7, 'Pedro_Martinez@correu.com'),
(33, '129.99', '2023-12-09', 27, 8, 'Carlos_Rodriguez@correu.com'),
(34, '209.99', '2023-12-09', 12, 9, 'Juan_Garcia@correu.com'),
(35, '34.99', '2023-12-09', 15, 10, 'Maria_Lopez@correu.com'),
(36, '55.00', '2023-12-14', 24, 21, 'Carlos_Rodriguez@correu.com'),
(37, '86.00', '2023-12-14', 15, 21, 'Carlos_Rodriguez@correu.com'),
(38, '199.99', '2023-12-15', 20, 32, 'Juan_Garcia@correu.com'),
(39, '199.99', '2023-12-15', 15, 33, 'Maria_Lopez@correu.com'),
(40, '299.95', '2023-12-15', 10, 34, 'Pedro_Martinez@correu.com'),
(41, '449.00', '2023-12-15', 25, 35, 'Laura_Gomez@correu.com'),
(42, '349.99', '2023-12-15', 30, 36, 'Carlos_Rodriguez@correu.com'),
(43, '549.95', '2023-12-15', 15, 37, 'Juan_Garcia@correu.com'),
(44, '799.00', '2023-12-15', 10, 38, 'Maria_Lopez@correu.com'),
(45, '399.99', '2023-12-15', 25, 39, 'Pedro_Martinez@correu.com'),
(46, '699.00', '2023-12-15', 20, 40, 'Laura_Gomez@correu.com'),
(47, '299.99', '2023-12-15', 15, 41, 'Carlos_Rodriguez@correu.com'),
(48, '399.99', '2023-12-15', 20, 42, 'Juan_Garcia@correu.com'),
(49, '599.95', '2023-12-15', 10, 32, 'Maria_Lopez@correu.com'),
(50, '899.00', '2023-12-15', 15, 33, 'Pedro_Martinez@correu.com'),
(51, '499.99', '2023-12-15', 25, 34, 'Laura_Gomez@correu.com'),
(52, '299.99', '2023-12-15', 30, 35, 'Carlos_Rodriguez@correu.com'),
(53, '449.95', '2023-12-15', 20, 36, 'Juan_Garcia@correu.com'),
(54, '699.00', '2023-12-15', 15, 37, 'Maria_Lopez@correu.com'),
(55, '349.99', '2023-12-15', 25, 38, 'Pedro_Martinez@correu.com'),
(56, '799.00', '2023-12-15', 30, 39, 'Laura_Gomez@correu.com'),
(57, '499.99', '2023-12-15', 10, 40, 'Carlos_Rodriguez@correu.com'),
(58, '199.99', '2023-12-15', 20, 32, 'Juan_Garcia@correu.com'),
(59, '199.99', '2023-12-15', 15, 33, 'Maria_Lopez@correu.com'),
(60, '299.95', '2023-12-15', 10, 34, 'Pedro_Martinez@correu.com'),
(61, '449.00', '2023-12-15', 25, 35, 'Laura_Gomez@correu.com'),
(62, '349.99', '2023-12-15', 30, 36, 'Carlos_Rodriguez@correu.com'),
(63, '549.95', '2023-12-15', 15, 37, 'Juan_Garcia@correu.com'),
(64, '799.00', '2023-12-15', 10, 38, 'Maria_Lopez@correu.com'),
(65, '399.99', '2023-12-15', 25, 39, 'Pedro_Martinez@correu.com'),
(66, '699.00', '2023-12-15', 20, 40, 'Laura_Gomez@correu.com'),
(67, '299.99', '2023-12-15', 15, 41, 'Carlos_Rodriguez@correu.com'),
(68, '399.99', '2023-12-15', 20, 42, 'Juan_Garcia@correu.com'),
(69, '599.95', '2023-12-15', 10, 32, 'Maria_Lopez@correu.com'),
(70, '899.00', '2023-12-15', 15, 33, 'Pedro_Martinez@correu.com'),
(71, '499.99', '2023-12-15', 25, 34, 'Laura_Gomez@correu.com'),
(72, '299.99', '2023-12-15', 30, 35, 'Carlos_Rodriguez@correu.com'),
(73, '449.95', '2023-12-15', 20, 36, 'Juan_Garcia@correu.com'),
(74, '699.00', '2023-12-15', 15, 37, 'Maria_Lopez@correu.com'),
(75, '349.99', '2023-12-15', 25, 38, 'Pedro_Martinez@correu.com'),
(76, '799.00', '2023-12-15', 30, 39, 'Laura_Gomez@correu.com'),
(77, '499.99', '2023-12-15', 10, 40, 'Carlos_Rodriguez@correu.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedor`
--

CREATE TABLE `vendedor` (
  `correu` varchar(30) NOT NULL,
  `estat` varchar(20) DEFAULT NULL,
  `nom` varchar(20) DEFAULT NULL,
  `llinatges` varchar(30) DEFAULT NULL,
  `dataNaix` date DEFAULT NULL,
  `contrasenya` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vendedor`
--

INSERT INTO `vendedor` (`correu`, `estat`, `nom`, `llinatges`, `dataNaix`, `contrasenya`) VALUES
('Carlos_Rodriguez@correu.com', 'DOLENT', 'Carlos', 'Rodríguez', '1995-07-08', 'venedor5'),
('eduosu@gmail.com', NULL, 'Eduardo', 'Osuna', '2002-02-02', 'eduosu'),
('Juan_Garcia@correu.com', NULL, 'Juan', 'García', '1990-05-15', 'venedor1'),
('Laura_Gomez@correu.com', NULL, 'Laura', 'Gómez', '1988-03-28', 'venedor4'),
('Maria_Lopez@correu.com', NULL, 'Maria', 'López', '1985-09-22', 'venedor2'),
('Pedro_Martinez@correu.com', NULL, 'Pedro', 'Martínez', '1992-12-10', 'venedor3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zona`
--

CREATE TABLE `zona` (
  `nom` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `zona`
--

INSERT INTO `zona` (`nom`) VALUES
('LLEVANT'),
('MIGJORN'),
('PALMA DE MALLORCA'),
('PLA DE MALLORCA'),
('RAIGUER'),
('TRAMUNTANA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`idavis`),
  ADD KEY `idven` (`idven`),
  ADD KEY `idcon` (`idcon`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`nom`);

--
-- Indices de la tabla `comanda`
--
ALTER TABLE `comanda`
  ADD PRIMARY KEY (`idcoman`),
  ADD KEY `idcon` (`idcon`),
  ADD KEY `idcomp` (`idcomp`),
  ADD KEY `iddom` (`iddom`),
  ADD KEY `NIF` (`NIF`),
  ADD KEY `idrep` (`idrep`);

--
-- Indices de la tabla `comprador`
--
ALTER TABLE `comprador`
  ADD PRIMARY KEY (`correu`);

--
-- Indices de la tabla `controlador`
--
ALTER TABLE `controlador`
  ADD PRIMARY KEY (`correu`);

--
-- Indices de la tabla `domicili`
--
ALTER TABLE `domicili`
  ADD PRIMARY KEY (`iddom`),
  ADD KEY `idcomp` (`idcomp`),
  ADD KEY `codiPostal` (`codiPostal`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`NIF`);

--
-- Indices de la tabla `entrega`
--
ALTER TABLE `entrega`
  ADD PRIMARY KEY (`identrega`),
  ADD KEY `idinici` (`idinici`),
  ADD KEY `idcomanda` (`idcomanda`);

--
-- Indices de la tabla `incidencia`
--
ALTER TABLE `incidencia`
  ADD PRIMARY KEY (`idinci`);

--
-- Indices de la tabla `poblacio`
--
ALTER TABLE `poblacio`
  ADD PRIMARY KEY (`codiPostal`),
  ADD KEY `nomZona` (`nomZona`);

--
-- Indices de la tabla `producte`
--
ALTER TABLE `producte`
  ADD PRIMARY KEY (`idprod`),
  ADD KEY `nomC` (`nomC`);

--
-- Indices de la tabla `quantitat`
--
ALTER TABLE `quantitat`
  ADD PRIMARY KEY (`idcomanda`,`idvenda`),
  ADD KEY `idvenda` (`idvenda`);

--
-- Indices de la tabla `repartidor`
--
ALTER TABLE `repartidor`
  ADD PRIMARY KEY (`correu`);

--
-- Indices de la tabla `resguard_comanda`
--
ALTER TABLE `resguard_comanda`
  ADD PRIMARY KEY (`idcoman`),
  ADD KEY `idcon` (`idcon`),
  ADD KEY `idcomp` (`idcomp`),
  ADD KEY `iddom` (`iddom`),
  ADD KEY `NIF` (`NIF`),
  ADD KEY `idrep` (`idrep`);

--
-- Indices de la tabla `r_zona_empresa`
--
ALTER TABLE `r_zona_empresa`
  ADD PRIMARY KEY (`nomZona`,`NIF`),
  ADD KEY `NIF` (`NIF`);

--
-- Indices de la tabla `venda`
--
ALTER TABLE `venda`
  ADD PRIMARY KEY (`idvenda`),
  ADD KEY `idprod` (`idprod`),
  ADD KEY `idven` (`idven`);

--
-- Indices de la tabla `vendedor`
--
ALTER TABLE `vendedor`
  ADD PRIMARY KEY (`correu`);

--
-- Indices de la tabla `zona`
--
ALTER TABLE `zona`
  ADD PRIMARY KEY (`nom`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `avis`
--
ALTER TABLE `avis`
  MODIFY `idavis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `comanda`
--
ALTER TABLE `comanda`
  MODIFY `idcoman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `domicili`
--
ALTER TABLE `domicili`
  MODIFY `iddom` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `entrega`
--
ALTER TABLE `entrega`
  MODIFY `identrega` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producte`
--
ALTER TABLE `producte`
  MODIFY `idprod` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `resguard_comanda`
--
ALTER TABLE `resguard_comanda`
  MODIFY `idcoman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `venda`
--
ALTER TABLE `venda`
  MODIFY `idvenda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`idven`) REFERENCES `vendedor` (`correu`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`idcon`) REFERENCES `controlador` (`correu`);

--
-- Filtros para la tabla `comanda`
--
ALTER TABLE `comanda`
  ADD CONSTRAINT `comanda_ibfk_1` FOREIGN KEY (`idcon`) REFERENCES `controlador` (`correu`),
  ADD CONSTRAINT `comanda_ibfk_2` FOREIGN KEY (`idcomp`) REFERENCES `comprador` (`correu`),
  ADD CONSTRAINT `comanda_ibfk_3` FOREIGN KEY (`iddom`) REFERENCES `domicili` (`iddom`),
  ADD CONSTRAINT `comanda_ibfk_4` FOREIGN KEY (`NIF`) REFERENCES `empresa` (`NIF`),
  ADD CONSTRAINT `comanda_ibfk_5` FOREIGN KEY (`idrep`) REFERENCES `repartidor` (`correu`);

--
-- Filtros para la tabla `domicili`
--
ALTER TABLE `domicili`
  ADD CONSTRAINT `domicili_ibfk_1` FOREIGN KEY (`idcomp`) REFERENCES `comprador` (`correu`),
  ADD CONSTRAINT `domicili_ibfk_2` FOREIGN KEY (`codiPostal`) REFERENCES `poblacio` (`codiPostal`);

--
-- Filtros para la tabla `entrega`
--
ALTER TABLE `entrega`
  ADD CONSTRAINT `entrega_ibfk_1` FOREIGN KEY (`idinici`) REFERENCES `incidencia` (`idinci`),
  ADD CONSTRAINT `entrega_ibfk_2` FOREIGN KEY (`idcomanda`) REFERENCES `comanda` (`idcoman`);

--
-- Filtros para la tabla `poblacio`
--
ALTER TABLE `poblacio`
  ADD CONSTRAINT `poblacio_ibfk_1` FOREIGN KEY (`nomZona`) REFERENCES `zona` (`nom`);

--
-- Filtros para la tabla `producte`
--
ALTER TABLE `producte`
  ADD CONSTRAINT `producte_ibfk_1` FOREIGN KEY (`nomC`) REFERENCES `categoria` (`nom`);

--
-- Filtros para la tabla `quantitat`
--
ALTER TABLE `quantitat`
  ADD CONSTRAINT `quantitat_ibfk_1` FOREIGN KEY (`idcomanda`) REFERENCES `comanda` (`idcoman`),
  ADD CONSTRAINT `quantitat_ibfk_2` FOREIGN KEY (`idvenda`) REFERENCES `venda` (`idvenda`);

--
-- Filtros para la tabla `resguard_comanda`
--
ALTER TABLE `resguard_comanda`
  ADD CONSTRAINT `resguard_comanda_ibfk_1` FOREIGN KEY (`idcon`) REFERENCES `controlador` (`correu`),
  ADD CONSTRAINT `resguard_comanda_ibfk_2` FOREIGN KEY (`idcomp`) REFERENCES `comprador` (`correu`),
  ADD CONSTRAINT `resguard_comanda_ibfk_3` FOREIGN KEY (`iddom`) REFERENCES `domicili` (`iddom`),
  ADD CONSTRAINT `resguard_comanda_ibfk_4` FOREIGN KEY (`NIF`) REFERENCES `empresa` (`NIF`),
  ADD CONSTRAINT `resguard_comanda_ibfk_5` FOREIGN KEY (`idrep`) REFERENCES `repartidor` (`correu`);

--
-- Filtros para la tabla `r_zona_empresa`
--
ALTER TABLE `r_zona_empresa`
  ADD CONSTRAINT `r_zona_empresa_ibfk_1` FOREIGN KEY (`nomZona`) REFERENCES `zona` (`nom`),
  ADD CONSTRAINT `r_zona_empresa_ibfk_2` FOREIGN KEY (`NIF`) REFERENCES `empresa` (`NIF`);

--
-- Filtros para la tabla `venda`
--
ALTER TABLE `venda`
  ADD CONSTRAINT `venda_ibfk_1` FOREIGN KEY (`idprod`) REFERENCES `producte` (`idprod`),
  ADD CONSTRAINT `venda_ibfk_2` FOREIGN KEY (`idven`) REFERENCES `vendedor` (`correu`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `CopiaSeguretat` ON SCHEDULE EVERY 1 DAY STARTS '2023-12-15 17:31:53' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL InserirResguard();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
