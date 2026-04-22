-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-04-2026 a las 17:09:08
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `perfilacademico`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `carrera` enum('programacion','sistemas-computacionales','derecho','contaduria','administracion','artes-culinarias') NOT NULL,
  `direccion` text NOT NULL,
  `celular` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `nombre`, `apellido_paterno`, `apellido_materno`, `matricula`, `correo`, `contrasena`, `carrera`, `direccion`, `celular`) VALUES
(1, 'Melanie', 'Alcocer', 'Esquivel', 'alu001', 'melanie@example.com', '$2y$10$hash1', 'programacion', 'Calle 123, Campeche', '9811111111'),
(2, 'Carlos', 'Ramirez', 'Perez', 'alu002', 'carlos@example.com', '$2y$10$hash2', 'sistemas-computacionales', 'Col. Centro, Campeche', '9812222222'),
(3, 'Ana', 'Torres', 'Diaz', 'alu003', 'ana@example.com', '$2y$10$hash3', 'derecho', 'Av. Gobernadores', '9813333333'),
(4, 'Luis', 'Martinez', 'Hernandez', 'alu004', 'luis@example.com', '$2y$10$hash4', 'contaduria', 'Fracc. Las Flores', '9814444444'),
(5, 'Sofia', 'Castro', 'Ruiz', 'alu005', 'sofia@example.com', '$2y$10$hash5', 'administracion', 'Col. San Rafael', '9815555555'),
(6, 'Diego', 'Morales', 'Vega', 'alu006', 'diego@example.com', '$2y$10$hash6', 'artes-culinarias', 'Centro Histórico', '9816666666');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id` int(11) NOT NULL,
  `alumno_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `periodo_id` int(11) NOT NULL,
  `parcial_1` int(11) NOT NULL,
  `parcial_2` int(11) NOT NULL,
  `parcial_3` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificaciones`
--

INSERT INTO `calificaciones` (`id`, `alumno_id`, `materia_id`, `periodo_id`, `parcial_1`, `parcial_2`, `parcial_3`) VALUES
(85, 1, 1, 1, 0, 0, 0),
(86, 1, 2, 1, 0, 0, 0),
(87, 1, 1, 2, 0, 0, 0),
(88, 1, 2, 2, 0, 0, 0),
(89, 1, 1, 3, 0, 0, 0),
(90, 2, 2, 1, 0, 0, 0),
(91, 2, 2, 2, 0, 0, 0),
(92, 2, 1, 2, 0, 0, 0),
(93, 2, 1, 3, 0, 0, 0),
(94, 3, 3, 1, 0, 0, 0),
(95, 3, 3, 2, 0, 0, 0),
(96, 3, 3, 3, 0, 0, 0),
(97, 4, 4, 1, 0, 0, 0),
(98, 4, 4, 2, 0, 0, 0),
(99, 4, 4, 3, 0, 0, 0),
(100, 5, 5, 1, 0, 0, 0),
(101, 5, 5, 2, 0, 0, 0),
(102, 5, 5, 3, 0, 0, 0),
(103, 6, 5, 1, 0, 0, 0),
(104, 6, 5, 2, 0, 0, 0),
(105, 6, 5, 3, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `creditos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `creditos`) VALUES
(1, 'Programación Web', 8),
(2, 'Bases de Datos', 7),
(3, 'Derecho Civil', 6),
(4, 'Contabilidad Básica', 7),
(5, 'Administración I', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos`
--

CREATE TABLE `periodos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `periodos`
--

INSERT INTO `periodos` (`id`, `nombre`) VALUES
(1, 'Enero-Abril PRIMER AÑO'),
(2, 'Mayo-Agosto PRIMER AÑO'),
(3, 'Septiembre-Diciembre PRIMER AÑO'),
(4, 'Enero-Abril SEGUNDO AÑO'),
(5, 'Mayo-Agosto SEGUNDO AÑO'),
(6, 'Septiembre-Diciembre SEGUNDO AÑO'),
(7, 'Enero-Abril TERCER AÑO'),
(8, 'Mayo-Agosto TERCER AÑO'),
(9, 'Septiembre-Diciembre TERCER AÑO');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alumno_id` (`alumno_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `periodo_id` (`periodo_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`alumno_id`) REFERENCES `alumnos` (`id`),
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`),
  ADD CONSTRAINT `calificaciones_ibfk_3` FOREIGN KEY (`periodo_id`) REFERENCES `periodos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
