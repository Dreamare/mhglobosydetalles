-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-02-2022 a las 06:52:23
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `usuarios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `prov_id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `nombreC` varchar(50) DEFAULT NULL,
  `apellidoC` varchar(50) DEFAULT NULL,
  `NITCC` varchar(12) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `status` tinyint(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`prov_id`, `nombre`, `nombreC`, `apellidoC`, `NITCC`, `telefono`, `status`) VALUES
(1, 'Arreglos florales MD', 'David', 'Cano', '10102546987', '6574821645', NULL),
(2, 'Fiestas y variedades', 'Rogers', 'Hernesto', '10104632532', '3125479614', NULL),
(3, 'Globos SA', 'Juan', 'Lopez', '10101547896', '3658974154', NULL),
(1002, 'Bimbo', 'Rosa Maria', 'Gonzales Hernandez', '1000872026', '3013188632', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`prov_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `prov_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1005;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
