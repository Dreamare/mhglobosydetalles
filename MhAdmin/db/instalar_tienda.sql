-- ============================================================
--  INSTALACIÓN COMPLETA DE LA BASE DE DATOS - MH Globos y Detalles
-- ============================================================
--  Importa ESTE archivo en phpMyAdmin -> Importar (una sola vez).
--  Es seguro volver a importarlo: no borra datos existentes.
--
--  Crea:
--   * Base `card`      -> tienda: categorias, productos, usuarios (admin), pedidos
--   * Base `usuarios`  -> login de clientes (login.php / register.php)
--
--  Accesos de prueba (cámbialos después):
--   * Cliente (login.php):          usuario: cliente   clave: cliente123
--   * Administrador (admin/):       usuario: admin     clave: admin123
-- ============================================================

CREATE DATABASE IF NOT EXISTS `card` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `card`;

CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `precio_normal` decimal(12,2) NOT NULL DEFAULT 0,
  `precio_rebajado` decimal(12,2) NOT NULL DEFAULT 0,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_categoria` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referencia` varchar(60) NOT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `pasarela` varchar(20) NOT NULL DEFAULT 'wompi',
  `total` decimal(12,2) NOT NULL,
  `monto_centavos` bigint(20) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'PENDIENTE',
  `transaccion_id` varchar(80) DEFAULT NULL,
  `metodo_pago` varchar(40) DEFAULT NULL,
  `detalle` text,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referencia` (`referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de ejemplo (solo se insertan si las tablas están vacías)
INSERT INTO `categorias` (`id`, `categoria`)
SELECT * FROM (SELECT 1, 'Desayunos' UNION ALL SELECT 2, 'Arreglos' UNION ALL SELECT 3, 'Peluches' UNION ALL SELECT 4, 'Temporada') t
WHERE NOT EXISTS (SELECT 1 FROM `categorias`);

INSERT INTO `productos` (`nombre`, `descripcion`, `precio_normal`, `precio_rebajado`, `cantidad`, `imagen`, `id_categoria`)
SELECT * FROM (
  SELECT 'Desayuno sorpresa', 'Desayuno con globos y tarjeta personalizada', 95000, 85000, 10, 'desayuno-sorpresa.webp', 1 UNION ALL
  SELECT 'Desayuno especial', 'Desayuno completo con frutas, jugo y detalle', 120000, 110000, 10, 'desayuno-especial.jpeg', 1 UNION ALL
  SELECT 'Arreglo floral', 'Arreglo de flores naturales', 80000, 80000, 10, 'arreglo-floral.jpg', 2 UNION ALL
  SELECT 'Peluche con flores', 'Peluche acompañado de rosas', 98000, 90000, 10, 'peluche-flores.jpg', 3 UNION ALL
  SELECT 'Peluche', 'Peluche mediano', 45000, 45000, 10, 'peluche.jpg', 3 UNION ALL
  SELECT 'Cena navideña', 'Cena navideña sorpresa para dos', 180000, 165000, 5, 'cena-navidena.jpg', 4
) t
WHERE NOT EXISTS (SELECT 1 FROM `productos`);

INSERT IGNORE INTO `usuarios` (`nombre`, `usuario`, `clave`)
VALUES ('Administrador', 'admin', '0192023a7bbd73250516f069df18b500');

-- ------------------------------------------------------------
--  Login de clientes (base `usuarios`)
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `usuarios` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `usuarios`;

CREATE TABLE IF NOT EXISTS `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `correo` varchar(123) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `clave` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `login` (`correo`, `usuario`, `clave`)
VALUES ('cliente@ejemplo.com', 'cliente', 'c494890d96d94e20eaebf80f6989db09e6a4b4fde2869e6e23829ebf7c73a262b5c272b1e8cb981b697c2cc73091ca413d1f2387468db6a513a1b7377fe555e9');
