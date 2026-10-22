-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-02-2022 a las 17:21:27
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
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `eventos` (
  `codigo` int(11) NOT NULL,
  `cliente` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `barrio` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `precioevento` decimal(65,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO `eventos` (`codigo`, `cliente`, `barrio`, `direccion`, `descripcion`, `precioevento`) VALUES
(2, 'Mario Bautista Fernandez', 'Las palmas', 'Calle 50b #45-70', 'Evento de bodas',500000)
(3, 'Maria Paula Gonzalez', 'Bello', 'Carrera 45 #56-78', 'Evento baby shower',300000)
(4, 'Marcela Rodriguez', 'San diego', 'Calle 50b #45-79', 'Evento para 15s primavera neón',1000000)
(5, 'Simón Suarez', 'Villa Hermosa', 'Calle 45a #45-36', 'Evento de primera comunión',250000);
--
-- Volcado de datos para la tabla `productos`
--
--
-- Índices para tablas volcadas
--
--
-- Indices de la tabla `productos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`codigo`);
COMMIT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
