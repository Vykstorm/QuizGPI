-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 13, 2016 at 02:19 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gpi`
--

-- --------------------------------------------------------

--
-- Table structure for table `Partida`
--

CREATE TABLE `Partida` (
  `id` int(11) NOT NULL,
  `usuario1` int(11) DEFAULT NULL,
  `usuario2` int(11) DEFAULT NULL,
  `puntuacion1` int(3) NOT NULL,
  `puntuacion2` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pregunta`
--

CREATE TABLE `Pregunta` (
  `id` int(11) NOT NULL,
  `tema` int(11) NOT NULL,
  `pregunta` varchar(200) COLLATE latin1_spanish_ci NOT NULL,
  `respuesta1` varchar(40) COLLATE latin1_spanish_ci NOT NULL,
  `respuesta2` varchar(40) COLLATE latin1_spanish_ci NOT NULL,
  `respuesta3` varchar(40) COLLATE latin1_spanish_ci NOT NULL,
  `respuesta4` varchar(40) COLLATE latin1_spanish_ci NOT NULL,
  `correcta` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `Pregunta`
--

INSERT INTO `Pregunta` (`id`, `tema`, `pregunta`, `respuesta1`, `respuesta2`, `respuesta3`, `respuesta4`, `correcta`) VALUES
(1, 1, 'Los ordenadores sólo son capaces de manejar señales', 'Analógicas', 'Digitales', 'Discretas', 'Simbólicas', 2),
(2, 1, 'Parte física de un ordenador', 'Software', 'Hardware', 'USB', 'Teclado', 2),
(3, 1, 'Parte lógica de un ordenador', 'Hardware', 'Software', 'Altavoz', 'Bus', 2),
(4, 1, 'Memoria donde están los datos para que el ordenador arranque', 'RAM', 'RUM', 'REM', 'ROM', 4),
(5, 1, 'Memoria donde el ordenador guarda los datos que está utilizando en ese momento', 'RIM', 'REM', 'RAM', 'ROM', 3),
(6, 1, 'Dispositivos mediante los cuales el ordenador se comunica con el exterior', 'DLCs', 'Móviles', 'Extras', 'Periféricos', 4),
(7, 1, 'Un escáner es un periférico de', 'Salida', 'Entrada', 'Entrada/salida', 'Ninguna', 2),
(8, 1, 'Unos auriculares son un periférico de', 'Salida', 'Entrada/Salida', 'Entrada', 'Comunicación', 1),
(9, 1, '¿Cuál de los siguientes periféricos es de entrada?', 'Ratón', 'Monitor', 'Impresora', 'Altavoz', 1),
(10, 1, '¿Cuál de los siguientes periféricos es de salida?', 'Disco Duro', 'Ratón', 'Impresora', 'Teclado', 3),
(11, 1, '¿Cuál de los siguientes periféricos es de entrada/salida?', 'Pantalla táctil', 'Teclado', 'Micrófono', 'Impresora', 1),
(12, 1, '¿Cuál de las siguientes impresoras es de impacto?', 'La de líneas', 'La láser', 'La térmica', 'La de burbuja', 1),
(13, 1, 'Con el término QWERTY nos referimos a un tipo de', 'Impresora', 'Virus', 'Teclado', 'Firewall', 3),
(14, 1, 'Con el término CRT nos referimos a un tipo de', 'Ratón', 'Disquete', 'Teclado', 'Monitor', 4),
(15, 1, 'Winchester es un tipo de', 'Ratón', 'Disco Duro', 'Teclado', 'Disquete', 2),
(16, 1, '¿Cuál de las siguientes no es una marca de procesadores?', 'Atom', 'AMD', 'VIA', 'INTEL', 1),
(17, 1, '¿Cuál de los siguientes no es un sistema operativo?', 'Fedora', 'Mandriva', 'Sabayon', 'Razzia', 4),
(18, 1, '¿Cuál de los siguientes es un sistema operativo?', 'Tuquito', 'Berserk', 'Kefir', 'Gleba', 1),
(19, 1, '¿Cuál de los siguientes es un sistema operativo?', 'Mies', 'Loor', 'Tizen', 'Nadir', 3),
(20, 1, '¿Cuál de los siguientes es un sistema operativo?', 'Uruk', 'Bada', 'Camama', 'Venial', 2),
(21, 1, 'Sistema de numeración que utilizan los ordenadores que sólo usa ceros y unos', 'Binario', 'Decimal', 'Octal', 'Hexadecimal', 1),
(22, 1, 'Unidad más pequeña de información en un ordenador', 'Byte', 'Nibble', 'Bit', 'Yobibyte', 3),
(23, 1, 'Conjunto de 4 bits', 'Nibble', 'Palabra', 'Tetrabit', 'Byte', 1),
(24, 1, 'Conjunto de 8 bits', 'Palabra', 'Octabit', 'Byte', 'Nibble', 3),
(25, 1, 'Conjunto de 16 bits', 'Hexabit', 'Palabra', 'Nibble', 'Byte', 2),
(26, 1, 'Un nibble son', '2 bytes', '8 bytes', '4 bits', '10 bits', 3),
(27, 1, 'Un byte son', '1000 bits', '8 bits', '1024 bits', '10 bits', 2),
(28, 1, 'Una palabra son', '16 bits', '1024 bits', '10 bits', '2 bytes', 1),
(29, 1, 'Un megabyte equivale a', '1.000.000 bytes', '10.000 kilobytes', '10 gigabytes', '100 kilobytes', 1),
(30, 1, 'Un terabyte equivale a', '1.000.000.000.000 bits', '1.000 megabytes', '1.000.000.000 bytes', '1.000.000.000 kilobytes', 4),
(31, 1, '1.000.000.000.000 de bytes son un', 'Megabyte', 'Petabyte', 'Gigabyte', 'Terabyte', 4),
(32, 1, '1.000.000.000.000 de gigabytes son un', 'Petabyte', 'Zettabyte', 'Yottabyte', 'Exabyte', 2),
(33, 1, '¿Cuál de las siguientes unidades de información es mayor?', 'Petabyte', 'Zettabyte', 'Exabyte', 'Yottabyte', 4),
(34, 1, 'Atajo de teclado para copiar un texto, carpeta o fichero seleccionado', 'Ctrl+C', 'Ctrl+W', 'Ctrl+D', 'Ctrl+V', 1),
(35, 1, 'Atajo de teclado para cortar un texto, carpeta o fichero seleccionado', 'Ctrl+X', 'Ctrl+Z', 'Ctrl+S', 'Ctrl+J', 1),
(36, 1, 'Atajo de teclado para rehacer una acción previamente deshecha', 'Ctrl+F5', 'Ctrl+A', 'Ctrl+Y', 'Ctrl+Z', 3),
(37, 1, 'Atajo de teclado para abrir una nueva pestaña en un navegador', 'Ctrl+T', 'Ctrl+Y', 'Ctrl+N', 'Ctrl+P', 1),
(38, 1, 'Atajo de teclado para cerrar la ventana activa', 'Ctrl+P', 'Ctrl+W', 'Ctrl+D', 'Ctrl+E', 2),
(39, 1, 'Atajo de teclado para cambiar entre las aplicaciones que tengamos abiertas', 'Ctrl+Tab', 'Ctrl+Esc', 'Alt+Esc', 'Alt+Tab', 4),
(40, 1, 'Atajo de teclado para borrar un documento sin pasar por la papelera de reciclaje', 'Ctrl+Del', 'F2+Del', 'Shift+Del', 'Ctrl+Alt+Supr', 3),
(41, 1, 'Atajo de teclado para renombrar un elemento', 'Ctrl+R', 'Ctrl+Shift+R', 'Ctrl+Shift+N', 'F2', 4),
(42, 1, 'Atajo de teclado para abrir el menú de inicio', 'Ctrl+Esc', 'Ctrl+Shift+I', 'Ctrl+S', 'Ctrl+Shift+S', 1),
(43, 1, 'Atajo de teclado para Windows que nos muestra el escritorio', 'Win+D', 'Win+E', 'Win+Esc', 'Win+W', 1),
(44, 1, 'Atajo de teclado para Windows que abre el explorador de archivos', 'Win+X', 'Win+E', 'Win+F', 'Win+F2', 2),
(45, 1, 'El puerto 13 es usado por el protocolo de aplicación', 'FTP', 'TELNET', 'DNS', 'DAYTIME', 4),
(46, 1, 'El puerto 25 es usado por el protocolo de aplicación', 'SMTP', 'TIME', 'SFTP', 'TELNET', 1),
(47, 1, 'El puerto 42 es usado por el protocolo de aplicación', 'NAME', 'RAP', 'SSH', 'GOPHER', 1),
(48, 1, 'El puerto 88 es usado por el protocolo de aplicación', 'HTTPS', 'KERBEROS', 'FINGER', 'TFTP', 2),
(49, 1, 'El puerto 443 es usado por el protocolo de aplicación', 'IMAP 3', 'IRC', 'SYSLOG', 'SHTTP', 4),
(50, 1, 'Puerto para protocolo SSH', '20', '22', '23', '25', 2),
(51, 1, 'Puerto para protocolo GOPHER', '107', '79', '70', '39', 3),
(52, 1, 'Puerto para protocolo HTTP', '80', '53', '22', '7', 1),
(53, 1, 'Puerto para protocolo POP3', '88', '25', '110', '13', 3),
(54, 1, 'Puerto para protocolo SNMP', '161', '530', '119', '220', 1),
(55, 1, '¿Cuál es el protocolo para el envío de correo electrónico?', 'SMTP', 'POP', 'HTTPS', 'TFTP', 1),
(56, 1, '¿Cuál no es un protocolo del nivel de aplicación de la pila TCP/IP?', 'HTTP', 'FTP', 'DNS', 'UDP', 4),
(57, 1, '¿Cuál de las siguientes es una t-norma?', 'Máximo', 'Lukasiewicz', 'Suma algebraica', 'Ninguna', 2),
(58, 1, '¿Cuál de las siguientes es una t-conorma?', 'Producto algebraico', 'Mínimo', 'Lukasiewicz', 'Ninguna', 4),
(59, 1, '¿Cuál de los siguientes no es un operador de implicación?', 'Mamdani', 'Larsen', 'Morgan', 'Zadeh', 3),
(60, 1, 'Conjunto de intensidades que forman parte de una imagen', 'Rango dinámico', 'Histograma', 'Contraste', 'Color', 1),
(61, 1, 'Número de píxeles de una imagen con un determinado nivel de intesidad', 'Borde', 'Histograma', 'Región', 'Rango dinámico', 2),
(62, 1, 'Variaciones del brillo en una imagen', 'Fusión', 'Borde', 'Contraste', 'Intensidad', 3),
(63, 1, 'Método para convertir una imagen de niveles de gris en una binaria', 'Transformación', 'Segmentación', 'Fusión', 'Umbralización', 4),
(64, 1, 'Método para utilizar un menor número de bytes para almacenar/transmitir imágenes', 'Compresión', 'Reducción', 'Transformación', 'Segmentación', 1),
(65, 1, '¿Cuál de las siguientes no es un tipo de interpolación en tratamiento de imágenes?', 'Vecino más cercano', 'Funcional', 'Bilineal', 'Bicúbica', 2),
(66, 1, 'Método que a partir de un conjunto de imágenes permite obtener una nueva que contiene la máxima información de todas ellas', 'Interpolación', 'Umbralización', 'Fusión', 'Transformación', 3),
(67, 1, '¿Cuál de las siguientes no es una tarea propia de un sistema de visión artificial?', 'Captación', 'Segmentación', 'Reconocimiento', 'Especificación', 4),
(68, 1, 'Los archivos escritos en Pascal tienen extensión', '.pas', '.p', '.pascal', '.pasc', 1),
(69, 1, 'Los archivos escritos en MATLAB tienen extensión', '.m', '.mat', '.mt', '.c', 1),
(70, 1, 'Los archivos escritos en LISP tienen extensión', '.l', '.lisp', '.lp', '.li', 2),
(71, 1, 'Los archivos escritos en Phyton tienen extensión', '.phy', '.p', '.py', '.ph', 3),
(72, 1, 'Los archivos escritos en ensamblador tienen extensión', '.asm', '.ens', '.ase', '.as', 1),
(73, 1, 'Los archivos escritos en Ada tienen extensión', '.a', '.ada', '.adb', '.adby', 3),
(74, 1, 'La empresa de informática Alienware es de', 'Estados Unidos', 'Canada', 'Inglaterra', 'Camboya', 1),
(75, 1, 'La empresa de informática ASUS es de', 'Estados Unidos', 'Taiwan', 'Alemania', 'China', 2),
(76, 1, 'Empresa de informática estadounidense fundada en 1911', 'Apple', 'AT&T', 'IBM', 'Siemens', 3),
(77, 1, 'Empresa de informática estadounidense fundada en 1977', 'Microsoft', 'Intel', 'Apple', 'Oracle', 4),
(78, 1, 'Empresa de informática china fundada en 1984', 'Samsung', 'Toshiba', 'Lenovo', 'ASUS', 3),
(79, 1, 'El primer nombre de dominio registrado fue', 'MCC.com', 'DEC.com', 'Think.com', 'Symbolics.com', 4),
(80, 1, 'Año en el que se puso en venta el primer portátil de la historia', '1990', '1982', '1995', '1987', 2),
(81, 1, 'País que genera más ataques de denegación de servicio (DDoS)', 'Brasil', 'Estados Unidos', 'India', 'China', 4),
(82, 1, 'Principiante en el mundo del hacking que copia el trabajo de otros hackers', 'Newbie', 'Phreaker', 'Lammer', 'Virii', 3),
(83, 1, 'Primer gusano que paralizó practicamente Internet en 1988', 'Paco', 'Morris', 'Melissa', 'Wilson', 2),
(84, 1, '¿Qué es un backup?', 'Programa de Windows', 'Anti-virus', 'Virus', 'Copia de seguridad', 4),
(85, 1, '¿Cuál de los siguientes no es un anti-virus?', 'Hoax', 'Panda', 'Kaspersky', 'AVG', 1),
(86, 1, 'Serie de televisión cuyo protagonista es un hacker', 'Mr Robot', 'La Casa de la Pradera', 'Boardwalk Empire', 'Juego de Tronos', 1),
(87, 1, 'El famoso hacker Kevin Mitnick era conocido como', 'El Zorro', 'La Ardilla', 'El Cóndor', 'El Cuervo', 3),
(88, 1, 'Modelo de las bases de datos de primera generación (1960)', 'Jerárquico', 'Relacional', 'Orientado a objetos', 'noSQL', 1),
(89, 1, '¿Cuál no es un sistema gestor de base de datos?', 'MySQL', 'mongoDB', 'Wfuzz', 'Postgre', 3),
(90, 1, 'Equipo campeón del mundial de LOL del año 2011', 'Moscow Five', 'Fnatic', 'Azubu Frost', 'Team SoloMid', 2),
(91, 1, 'Equipo campeón del mundial de LOL del año 2012', 'Taipei Assassins', 'CLG Europe', 'Team SoloMid', 'Moscow Five', 1),
(92, 1, 'Equipo campeón del mundial de LOL del año 2013', 'NaJin Black Sword', 'Royal Club', 'SK Telecom T1', 'Fnatic', 3),
(93, 1, 'Equipo campeón del mundial de LOL del año 2014', 'Samsung Galaxy White', 'Samsung Galaxy Blue', 'Star Horn Royal Club', 'OMG', 1),
(94, 1, 'Equipo campeón del mundial de LOL del año 2015', 'KOO Tigers', 'SK Telecom T1', 'Fnatic', 'Origen', 2),
(95, 1, 'Equipo campeón del mundial de LOL del año 2016', 'Samsung Galaxy', 'H2K-Gaming', 'ROX Tigers', 'SK Telecom T1', 4),
(96, 1, 'País donde se celebró el primer mundial de LOL en el año 2011', 'Inglaterra', 'Estados Unidos', 'Suecia', 'Corea del Sur', 3),
(97, 1, 'País donde se celebró el mundial de LOL del año 2012', 'Estados Unidos', 'Austria', 'Sudáfrica', 'China', 1),
(98, 1, 'País donde se celebró el mundial de LOL del año 2013', 'Estados Unidos', 'Alemania', 'Japón', 'Rusia', 1),
(99, 1, 'País donde se celebró el mundial de LOL del año 2014', 'Japón', 'Corea del Sur', 'Alemania', 'Suecia', 2),
(100, 1, 'País donde se celebró el mundial de LOL del año 2015', 'México', 'Alemania', 'Corea del Sur', 'China', 2),
(101, 1, 'País donde se celebró el mundial de LOL del año 2016', 'Francia', 'Argentina', 'China', 'Estados Unidos', 4),
(102, 1, 'Resultado de la final del mundial de LOL del 2012 entre Taipei Assassins y Azubu Frost', '3-2', '0-3', '1-3', '3-1', 4),
(103, 1, 'Resultado de la final del mundial de LOL del 2014 entre Star Horn Royal Club y Samsung Galaxy White', '3-1', '3-2', '1-3', '3-0', 3),
(104, 1, 'Resultado de la final del mundial de LOL del 2016 entre SK Telecom T1 y Samsung Galaxy', '3-2', '3-0', '3-1', '1-3', 1),
(105, 1, '¿Cuál de los siguientes es el sufijo del dominio de Youtube?', '.org', '.com', '.es', '.net', 2),
(106, 1, '¿Cuál de los siguientes es el sufijo del dominio de Marca?', '.net', '.es', '.esp', '.com', 4),
(107, 1, '¿Cuál de los siguientes es el sufijo del dominio de Sport?', '.fr', '.com', '.es', '.cat', 3),
(108, 1, '¿Cuál de los siguientes es el sufijo del dominio de El Pais?', '.com', '.org', '.net', '.es', 1),
(109, 1, '¿Cuál de los siguientes es el sufijo del dominio de El Mundo?', '.net', '.es', '.com', '.org', 2),
(110, 1, '¿Cuál de los siguientes es el sufijo del dominio de Washington Post?', '.ca', '.org', '.us', '.com', 4),
(111, 1, '¿Cuál de los siguientes es el sufijo del dominio de Bild?', '.it', '.fr', '.de', '.at', 3),
(112, 1, '¿Cuál de los siguientes es el sufijo del dominio de Wikipedia?', '.org', '.com', '.uk', '.es', 1),
(113, 1, '¿Cuál de los siguientes es el sufijo del dominio de IMDB?', '.us', '.com', '.uk', '.br', 2),
(114, 1, '¿Cuál de los siguientes es el sufijo del dominio de Fino Filipino?', '.es', '.com', '.net', '.org', 4),
(115, 1, '¿Cuál de los siguientes es el sufijo del dominio de La Repubblica?', '.es', '.sw', '.it', '.com', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ssi`
--

CREATE TABLE `ssi` (
  `id` int(11) NOT NULL,
  `name` varchar(25) COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(25) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `ssi`
--

INSERT INTO `ssi` (`id`, `name`, `password`) VALUES
(1, 'pepito', 'menudoformulario'),
(2, 'gonzalo', 'holacaracola'),
(3, 'damnvulnerable', 'site'),
(4, 'admin', '1234'),
(5, 'admin', 'admin'),
(6, '1234', '1234'),
(7, 'user', 'house'),
(8, 'alberto', 'perro'),
(9, 'paco', 'piscina'),
(10, 'manolo', 'piedra'),
(11, 'soyelmasguay', 'home');

-- --------------------------------------------------------

--
-- Table structure for table `ssi2`
--

CREATE TABLE `ssi2` (
  `id` int(11) NOT NULL,
  `number` varchar(30) COLLATE latin1_spanish_ci NOT NULL,
  `pin` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `ssi2`
--

INSERT INTO `ssi2` (`id`, `number`, `pin`) VALUES
(1, 'NTI0MzYwNjIyMzA3ODI3MA==', 6522),
(2, 'NTExNDc1MTgzMjcyOTA0MA==', 9877);

-- --------------------------------------------------------

--
-- Table structure for table `Tema`
--

CREATE TABLE `Tema` (
  `id` int(11) NOT NULL,
  `nombre` varchar(40) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `Tema`
--

INSERT INTO `Tema` (`id`, `nombre`) VALUES
(1, 'Informatica');

-- --------------------------------------------------------

--
-- Table structure for table `Usuario`
--

CREATE TABLE `Usuario` (
  `id` int(11) NOT NULL,
  `name` varchar(40) COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(40) COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

--
-- Dumping data for table `Usuario`
--

INSERT INTO `Usuario` (`id`, `name`, `password`) VALUES
(1, 'daniel', '098f6bcd4621d373cade4e832627b4f6'),
(2, 'Gonzalo', '25de3d3ce47a570756d406ee4cfa52f4'),
(3, 'Victor', 'af854c7c0a007420938e7fa19aace469'),
(4, 'Edson', '964fe6242ca987d24f0fdfd851983c68'),
(5, '12345', '827ccb0eea8a706c4c34a16891f84e7b'),
(6, '     ', 'a384b6463fc216a5f8ecb6670f86456a'),
(7, 'capullo', '0771a6f40b8b8e53b173b939398109bf'),
(8, 'Â¿?*+', 'd939e7a6b17e374c1e3db59b4df2ae97'),
(9, 'Pepe', '259823af837e251e560ca1158a4e77c7'),
(10, 'titoMC', 'd88c2bd2daf52c09cbe97549b95457af'),
(11, 'elmendas', '098f6bcd4621d373cade4e832627b4f6'),
(12, 'elmendas2', '098f6bcd4621d373cade4e832627b4f6'),
(13, 'elmendas3', '098f6bcd4621d373cade4e832627b4f6'),
(14, 'elmendas4', '098f6bcd4621d373cade4e832627b4f6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Partida`
--
ALTER TABLE `Partida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario1` (`usuario1`),
  ADD KEY `usuario2` (`usuario2`);

--
-- Indexes for table `Pregunta`
--
ALTER TABLE `Pregunta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tema` (`tema`);

--
-- Indexes for table `ssi`
--
ALTER TABLE `ssi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ssi2`
--
ALTER TABLE `ssi2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Tema`
--
ALTER TABLE `Tema`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Usuario`
--
ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Partida`
--
ALTER TABLE `Partida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Pregunta`
--
ALTER TABLE `Pregunta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;
--
-- AUTO_INCREMENT for table `ssi`
--
ALTER TABLE `ssi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `ssi2`
--
ALTER TABLE `ssi2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `Tema`
--
ALTER TABLE `Tema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Usuario`
--
ALTER TABLE `Usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Partida`
--
ALTER TABLE `Partida`
  ADD CONSTRAINT `Partida_ibfk_1` FOREIGN KEY (`usuario1`) REFERENCES `Usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Partida_ibfk_2` FOREIGN KEY (`usuario2`) REFERENCES `Usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `Pregunta`
--
ALTER TABLE `Pregunta`
  ADD CONSTRAINT `Pregunta_ibfk_1` FOREIGN KEY (`tema`) REFERENCES `Tema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
