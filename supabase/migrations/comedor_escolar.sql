-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-05-2025 a las 09:12:37
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
-- Base de datos: `comedor_escolar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total_masculino` int(11) NOT NULL,
  `total_femenino` int(11) NOT NULL,
  `id_matricula` int(11) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id_asistencia`, `fecha`, `total_masculino`, `total_femenino`, `id_matricula`, `estado`, `created_at`) VALUES
(43, '2025-04-22', 10, 10, 1, 1, '2025-04-21 22:10:44'),
(44, '2025-04-22', 7, 9, 4, 1, '2025-04-21 22:10:44'),
(45, '2025-04-22', 10, 1, 5, 1, '2025-04-21 22:10:44'),
(46, '2025-04-22', 10, 0, 2, 1, '2025-04-21 22:18:37'),
(47, '2025-04-22', 1, 1, 3, 1, '2025-04-21 22:18:42'),
(48, '2025-04-22', 10, 10, 1, 1, '2025-04-22 03:54:34'),
(49, '2025-04-22', 7, 9, 4, 1, '2025-04-22 03:54:34'),
(50, '2025-04-22', 10, 1, 5, 1, '2025-04-22 03:54:34'),
(51, '2025-04-01', 15, 15, 1, 1, '2025-04-22 04:44:47'),
(52, '2025-04-01', 15, 5, 4, 1, '2025-04-22 04:44:47'),
(53, '2025-04-01', 10, 10, 5, 1, '2025-04-22 04:44:47'),
(54, '2025-04-01', 10, 10, 2, 1, '2025-04-22 04:44:54'),
(55, '2025-04-01', 10, 10, 3, 1, '2025-04-22 04:44:58'),
(56, '2025-04-06', 1, 15, 1, 1, '2025-04-22 04:46:01'),
(57, '2025-04-06', 1, 15, 4, 1, '2025-04-22 04:46:01'),
(58, '2025-04-06', 1, 10, 5, 1, '2025-04-22 04:46:01'),
(59, '2025-04-06', 1, 10, 2, 1, '2025-04-22 04:46:07'),
(60, '2025-04-13', 1, 15, 1, 1, '2025-04-22 04:46:25'),
(61, '2025-04-13', 1, 15, 4, 1, '2025-04-22 04:46:25'),
(62, '2025-04-13', 1, 10, 5, 1, '2025-04-22 04:46:25'),
(63, '2025-04-13', 10, 1, 2, 1, '2025-04-22 04:46:34'),
(64, '2025-04-30', 10, 10, 1, 1, '2025-04-29 23:15:12'),
(65, '2025-04-30', 0, 0, 4, 1, '2025-04-29 23:15:12'),
(66, '2025-04-30', 0, 0, 5, 1, '2025-04-29 23:15:12'),
(67, '2025-04-30', 10, 10, 1, 1, '2025-04-29 23:15:18'),
(68, '2025-04-30', 1, 2, 4, 1, '2025-04-29 23:15:18'),
(69, '2025-04-30', 1, 1, 5, 1, '2025-04-29 23:15:23'),
(70, '2025-04-30', 10, 1, 2, 1, '2025-04-29 23:15:28'),
(71, '2025-04-30', 1, 1, 3, 1, '2025-04-29 23:15:31'),
(72, '2025-04-30', 10, 10, 1, 1, '2025-04-29 23:32:43'),
(73, '2025-04-30', 10, 10, 4, 1, '2025-04-29 23:32:43'),
(74, '2025-04-30', 10, 10, 5, 1, '2025-04-29 23:32:43'),
(75, '2025-04-30', 0, 0, 7, 1, '2025-04-30 05:02:42'),
(76, '2025-04-30', 0, 0, 8, 1, '2025-04-30 05:02:42'),
(77, '2025-04-29', 1, 1, 1, 1, '2025-04-30 05:03:53'),
(78, '2025-04-29', 2, 4, 4, 1, '2025-04-30 05:03:53'),
(79, '2025-04-29', 10, 10, 5, 1, '2025-04-30 05:03:54'),
(80, '2025-04-29', 1, 9, 7, 1, '2025-04-30 05:03:54'),
(81, '2025-04-29', 9, 9, 8, 1, '2025-04-30 05:03:54'),
(82, '2025-05-10', 10, 0, 1, 1, '2025-05-10 15:54:43'),
(83, '2025-05-10', 0, 0, 4, 1, '2025-05-10 15:54:43'),
(84, '2025-05-10', 0, 0, 5, 1, '2025-05-10 15:54:43'),
(85, '2025-05-10', 0, 0, 7, 1, '2025-05-10 15:54:43'),
(86, '2025-05-10', 0, 0, 8, 1, '2025-05-10 15:54:43'),
(87, '2025-05-02', 15, 0, 1, 1, '2025-05-10 15:55:02'),
(88, '2025-05-02', 0, 0, 4, 1, '2025-05-10 15:55:02'),
(89, '2025-05-02', 0, 0, 5, 1, '2025-05-10 15:55:02'),
(90, '2025-05-02', 0, 0, 7, 1, '2025-05-10 15:55:02'),
(91, '2025-05-02', 0, 0, 8, 1, '2025-05-10 15:55:02'),
(92, '2025-05-05', 0, 4, 1, 1, '2025-05-10 15:55:08'),
(93, '2025-05-05', 0, 0, 4, 1, '2025-05-10 15:55:08'),
(94, '2025-05-05', 0, 0, 5, 1, '2025-05-10 15:55:08'),
(95, '2025-05-05', 0, 0, 7, 1, '2025-05-10 15:55:08'),
(96, '2025-05-05', 0, 0, 8, 1, '2025-05-10 15:55:08'),
(97, '2025-05-12', 0, 0, 1, 1, '2025-05-12 17:05:32'),
(98, '2025-05-12', 0, 0, 4, 1, '2025-05-12 17:05:32'),
(99, '2025-05-12', 0, 0, 5, 1, '2025-05-12 17:05:32'),
(100, '2025-05-12', 0, 0, 7, 1, '2025-05-12 17:05:32'),
(101, '2025-05-12', 0, 0, 8, 1, '2025-05-12 17:05:32'),
(102, '2025-05-12', 12, 0, 3, 1, '2025-05-12 17:06:18'),
(103, '2025-05-12', 12, 0, 1, 1, '2025-05-12 18:22:50'),
(104, '2025-05-12', 12, 0, 4, 1, '2025-05-12 18:22:50'),
(105, '2025-05-12', 2, 0, 5, 1, '2025-05-12 18:22:50'),
(106, '2025-05-12', 12, 0, 7, 1, '2025-05-12 18:22:51'),
(107, '2025-05-12', 0, 0, 8, 1, '2025-05-12 18:22:51'),
(108, '2025-05-13', 0, 0, 1, 1, '2025-05-12 22:33:25'),
(109, '2025-05-13', 0, 0, 4, 1, '2025-05-12 22:33:25'),
(110, '2025-05-13', 0, 0, 9, 1, '2025-05-12 22:33:25'),
(111, '2025-05-13', 0, 0, 5, 1, '2025-05-12 22:33:25'),
(112, '2025-05-13', 0, 0, 7, 1, '2025-05-12 22:33:25'),
(113, '2025-05-13', 0, 0, 8, 1, '2025-05-12 22:33:25'),
(114, '2025-05-13', 0, 0, 10, 1, '2025-05-12 22:33:25'),
(115, '2025-05-13', 10, 10, 2, 1, '2025-05-12 22:34:50'),
(116, '2025-05-13', 0, 0, 3, 1, '2025-05-12 22:35:19'),
(126, '2025-05-03', 10, 0, 1, 1, '2025-05-15 01:20:41'),
(127, '2025-05-03', 0, 0, 4, 1, '2025-05-15 01:20:41'),
(128, '2025-05-03', 0, 0, 9, 1, '2025-05-15 01:20:41'),
(129, '2025-05-03', 0, 0, 5, 1, '2025-05-15 01:20:41'),
(130, '2025-05-03', 0, 0, 7, 1, '2025-05-15 01:20:41'),
(131, '2025-05-03', 0, 0, 8, 1, '2025-05-15 01:20:41'),
(132, '2025-05-03', 0, 0, 10, 1, '2025-05-15 01:20:41'),
(133, '2025-05-03', 1, 0, 1, 1, '2025-05-15 01:20:56'),
(134, '2025-05-03', 1, 0, 4, 1, '2025-05-15 01:20:56'),
(135, '2025-05-03', 1, 1, 9, 1, '2025-05-15 01:20:56'),
(136, '2025-05-03', 1, 1, 5, 1, '2025-05-15 01:20:56'),
(137, '2025-05-03', 0, 0, 7, 1, '2025-05-15 01:20:56'),
(138, '2025-05-03', 0, 0, 8, 1, '2025-05-15 01:20:56'),
(139, '2025-05-03', 0, 0, 10, 1, '2025-05-15 01:20:56'),
(199, '2025-05-15', 10, 6, 1, 1, '2025-05-15 02:12:04'),
(200, '2025-05-15', 10, 10, 4, 1, '2025-05-15 02:12:04'),
(201, '2025-05-15', 10, 10, 9, 1, '2025-05-15 02:12:04'),
(202, '2025-05-15', 10, 10, 5, 1, '2025-05-15 02:12:04'),
(203, '2025-05-15', 10, 10, 7, 1, '2025-05-15 02:12:04'),
(204, '2025-05-15', 12, 10, 8, 1, '2025-05-15 02:12:04'),
(205, '2025-05-15', 10, 10, 10, 1, '2025-05-15 02:12:04'),
(206, '2025-05-15', 10, 10, 15, 1, '2025-05-15 02:12:04'),
(207, '2025-05-15', 12, 10, 2, 1, '2025-05-15 02:12:10'),
(208, '2025-05-15', 10, 10, 3, 1, '2025-05-15 02:12:13'),
(209, '2025-05-18', 10, 10, 1, 1, '2025-05-18 00:31:42'),
(210, '2025-05-18', 10, 10, 4, 1, '2025-05-18 00:31:42'),
(211, '2025-05-18', 4, 8, 9, 1, '2025-05-18 00:31:42'),
(212, '2025-05-18', 10, 10, 5, 1, '2025-05-18 00:31:42'),
(213, '2025-05-18', 10, 10, 7, 1, '2025-05-18 00:31:42'),
(214, '2025-05-18', 10, 10, 8, 1, '2025-05-18 00:31:42'),
(215, '2025-05-18', 10, 10, 10, 1, '2025-05-18 00:31:42'),
(216, '2025-05-18', 9, 10, 15, 1, '2025-05-18 00:31:42'),
(217, '2025-05-18', 10, 10, 2, 1, '2025-05-18 07:32:31'),
(218, '2025-05-18', 10, 10, 3, 1, '2025-05-18 07:33:23'),
(219, '2025-05-18', 8, 8, 17, 1, '2025-05-18 22:00:47'),
(220, '2025-05-19', 1, 1, 1, 1, '2025-05-19 06:38:13'),
(221, '2025-05-19', 1, 1, 4, 1, '2025-05-19 06:38:13'),
(222, '2025-05-19', 1, 1, 9, 1, '2025-05-19 06:38:13'),
(223, '2025-05-19', 1, 1, 5, 1, '2025-05-19 06:38:13'),
(224, '2025-05-19', 1, 1, 7, 1, '2025-05-19 06:38:13'),
(225, '2025-05-19', 1, 1, 17, 1, '2025-05-19 06:38:13'),
(226, '2025-05-19', 1, 1, 8, 1, '2025-05-19 06:38:13'),
(227, '2025-05-19', 1, 1, 10, 1, '2025-05-19 06:38:13'),
(228, '2025-05-19', 1, 1, 15, 1, '2025-05-19 06:38:13'),
(229, '2025-05-19', 1, 1, 2, 1, '2025-05-19 06:38:33'),
(230, '2025-05-19', 1, 1, 3, 1, '2025-05-19 06:38:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumo`
--

CREATE TABLE `consumo` (
  `id_consumo` int(11) NOT NULL,
  `observacion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `consumo`
--

INSERT INTO `consumo` (`id_consumo`, `observacion`, `fecha`, `id_menu`, `estado`, `created_by`) VALUES
(26, '', '2025-04-21', 1, 1, 1),
(27, 'Salida de stock', '2025-04-21', NULL, 1, 1),
(28, 'Salida de stock', '2025-04-21', NULL, 1, 1),
(29, '', '2025-04-22', 1, 1, 1),
(30, '', '2025-04-30', 2, 1, 1),
(31, NULL, '2025-04-29', NULL, 1, 1),
(32, '', '2025-05-08', 1, 1, 1),
(33, '', '2025-05-10', 1, 1, 1),
(34, '', '2025-05-11', 1, 1, 1),
(35, '', '2025-05-12', 1, 1, 1),
(36, '', '2025-05-13', 2, 1, 1),
(37, '', '2025-05-14', 2, 1, 1),
(38, '', '2025-05-15', 8, 1, 1),
(39, '', '2025-05-15', 8, 1, 4),
(42, '', '2025-05-18', 2, 1, 1),
(43, '', '2025-05-19', 2, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumo_asistencia`
--

CREATE TABLE `consumo_asistencia` (
  `id_consumo_asistencia` int(11) NOT NULL,
  `id_consumo` int(11) DEFAULT NULL,
  `id_asistencia` int(11) DEFAULT NULL,
  `id_matricula` int(11) NOT NULL,
  `platos_servidos` int(11) NOT NULL,
  `platos_devueltos` int(11) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `consumo_asistencia`
--

INSERT INTO `consumo_asistencia` (`id_consumo_asistencia`, `id_consumo`, `id_asistencia`, `id_matricula`, `platos_servidos`, `platos_devueltos`, `fecha`) VALUES
(41, 30, 64, 1, 22, 0, '2025-04-30 00:00:00'),
(42, 30, 65, 4, 23, 0, '2025-04-30 00:00:00'),
(43, 30, 66, 5, 30, 0, '2025-04-30 00:00:00'),
(44, 30, 75, 7, 15, 0, '2025-04-30 00:00:00'),
(45, 30, 76, 8, 8, 0, '2025-04-30 00:00:00'),
(46, 31, 77, 1, 1, 0, '2025-04-29 00:00:00'),
(47, 31, 78, 4, 1, 0, '2025-04-29 00:00:00'),
(48, 31, 79, 5, 1, 0, '2025-04-29 00:00:00'),
(49, 31, 80, 7, 1, 0, '2025-04-29 00:00:00'),
(50, 31, 81, 8, 1, 0, '2025-04-29 00:00:00'),
(51, 33, 82, 1, 10, 0, '2025-05-10 00:00:00'),
(52, 35, 97, 1, 15, 0, '2025-05-12 00:00:00'),
(53, 35, 98, 4, 12, 0, '2025-05-12 00:00:00'),
(54, 35, 99, 5, 12, 0, '2025-05-12 00:00:00'),
(55, 35, 100, 7, 2, 0, '2025-05-12 00:00:00'),
(56, 35, 101, 8, 2, 0, '2025-05-12 00:00:00'),
(57, 36, 108, 1, 10, 0, '2025-05-13 00:00:00'),
(58, 36, 115, 2, 5, 0, '2025-05-13 00:00:00'),
(59, 36, 116, 3, 5, 0, '2025-05-13 00:00:00'),
(60, 36, 109, 4, 10, 0, '2025-05-13 00:00:00'),
(79, 38, 207, 2, 10, 0, '2025-05-15 00:00:00'),
(80, 38, 208, 3, 10, 0, '2025-05-15 00:00:00'),
(91, 42, 209, 1, 35, 0, '2025-05-18 00:00:00'),
(92, 42, 210, 4, 20, 0, '2025-05-18 00:00:00'),
(93, 42, 211, 9, 20, 0, '2025-05-18 00:00:00'),
(94, 42, 212, 5, 20, 0, '2025-05-18 00:00:00'),
(95, 42, 213, 7, 20, 0, '2025-05-18 00:00:00'),
(96, 42, 214, 8, 20, 0, '2025-05-18 00:00:00'),
(97, 42, 215, 10, 20, 0, '2025-05-18 00:00:00'),
(98, 42, 216, 15, 20, 0, '2025-05-18 00:00:00'),
(99, 42, 219, 17, 16, 0, '2025-05-18 00:00:00'),
(100, 42, 217, 2, 20, 0, '2025-05-18 00:00:00'),
(101, 42, 218, 3, 20, 0, '2025-05-18 00:00:00'),
(102, 43, 220, 1, 2, 0, '2025-05-19 00:00:00'),
(103, 43, 221, 4, 2, 0, '2025-05-19 00:00:00'),
(104, 43, 222, 9, 2, 0, '2025-05-19 00:00:00'),
(105, 43, 223, 5, 2, 0, '2025-05-19 00:00:00'),
(106, 43, 224, 7, 2, 0, '2025-05-19 00:00:00'),
(107, 43, 225, 17, 2, 0, '2025-05-19 00:00:00'),
(108, 43, 226, 8, 2, 0, '2025-05-19 00:00:00'),
(109, 43, 227, 10, 2, 0, '2025-05-19 00:00:00'),
(110, 43, 228, 15, 2, 0, '2025-05-19 00:00:00'),
(111, 43, 229, 2, 2, 0, '2025-05-19 00:00:00'),
(112, 43, 230, 3, 3, 0, '2025-05-19 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumo_detalle`
--

CREATE TABLE `consumo_detalle` (
  `id_consumo_detalle` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_consumo` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `consumo_detalle`
--

INSERT INTO `consumo_detalle` (`id_consumo_detalle`, `cantidad`, `id_producto`, `id_consumo`, `fecha`, `created_by`) VALUES
(26, 22.44, 3, NULL, '0000-00-00', NULL),
(27, 14.96, 5, NULL, '0000-00-00', NULL),
(28, 0.37, 4, NULL, '0000-00-00', NULL),
(29, 1.00, 3, NULL, '0000-00-00', NULL),
(30, 22.44, 3, 26, '0000-00-00', NULL),
(31, 14.96, 5, 26, '0000-00-00', NULL),
(32, 0.37, 4, 26, '0000-00-00', NULL),
(33, 2.00, 3, 26, '2025-04-21', 1),
(34, 22.44, 3, 26, '2025-04-21', 1),
(35, 14.96, 5, 26, '2025-04-21', 1),
(36, 0.37, 4, 26, '2025-04-21', 1),
(37, 100.00, 5, 29, '2025-04-22', 1),
(38, 7.08, 3, 30, '2025-04-30', 1),
(39, 4.72, 5, 30, '2025-04-30', 1),
(40, 0.12, 4, 30, '2025-04-30', 1),
(41, 7.08, 3, 30, '2025-04-30', 1),
(42, 4.72, 5, 30, '2025-04-30', 1),
(43, 0.12, 4, 30, '2025-04-30', 1),
(44, 7.08, 3, 30, '2025-04-30', 1),
(45, 4.72, 5, 30, '2025-04-30', 1),
(46, 0.12, 4, 30, '2025-04-30', 1),
(47, 7.08, 3, 30, '2025-04-30', 1),
(48, 4.72, 5, 30, '2025-04-30', 1),
(49, 0.12, 4, 30, '2025-04-30', 1),
(50, 6.90, 3, 30, '2025-04-30', 1),
(51, 5.52, 5, 30, '2025-04-30', 1),
(52, 0.14, 4, 30, '2025-04-30', 1),
(53, 8.28, 3, 30, '2025-04-30', 1),
(54, 5.52, 5, 30, '2025-04-30', 1),
(55, 0.14, 4, 30, '2025-04-30', 1),
(56, 11.76, 2, 30, '2025-04-30', 1),
(57, 0.78, 7, 30, '2025-04-30', 1),
(58, 0.98, 4, 30, '2025-04-30', 1),
(59, 200.00, 3, 30, '2025-04-30', 1),
(60, 55.00, 3, 30, '2025-04-30', 1),
(61, 5.00, 3, 30, '2025-04-30', 1),
(62, 1.20, 3, 33, '2025-05-10', 1),
(63, 0.80, 5, 33, '2025-05-10', 1),
(64, 0.02, 4, 33, '2025-05-10', 1),
(65, 34.00, 3, 33, '2025-05-10', 1),
(66, 0.29, 3, 33, '2025-05-10', 1),
(67, 0.02, 3, 33, '2025-05-10', 1),
(68, 14.00, 3, 33, '2025-05-10', 1),
(69, 8.00, 3, 35, '2025-05-12', 1),
(70, 16.00, 3, 35, '2025-05-12', 1),
(71, 1.00, 3, 35, '2025-05-12', 1),
(72, 5.16, 3, 35, '2025-05-12', 1),
(73, 3.44, 5, 35, '2025-05-12', 1),
(74, 0.09, 4, 35, '2025-05-12', 1),
(75, 10.00, 3, 35, '2025-05-12', 1),
(76, 45.00, 7, 36, '2025-05-13', 1),
(77, 9.00, 2, 36, '2025-05-13', 1),
(78, 12.00, 2, 36, '2025-05-13', 1),
(79, 3.60, 2, 36, '2025-05-13', 1),
(80, 0.24, 7, 36, '2025-05-13', 1),
(81, 0.30, 4, 36, '2025-05-13', 1),
(82, 2.00, 2, 36, '2025-05-13', 1),
(83, 2.00, 2, 36, '2025-05-13', 1),
(84, 2.00, 2, 36, '2025-05-13', 1),
(85, 10.00, 7, 37, '2025-05-14', 1),
(86, 10.92, 3, 38, '2025-05-15', 1),
(87, 7.28, 5, 38, '2025-05-15', 1),
(88, 0.55, 4, 38, '2025-05-15', 1),
(89, 10.92, 3, 38, '2025-05-15', 1),
(90, 7.28, 5, 38, '2025-05-15', 1),
(91, 0.55, 4, 38, '2025-05-15', 1),
(92, 80.00, 3, 38, '2025-05-15', 1),
(93, 10.00, 7, 38, '2025-05-15', 1),
(94, 40.00, 6, 38, '2025-05-15', 1),
(95, 30.00, 1, 38, '2025-05-15', 1),
(96, 50.00, 3, 38, '2025-05-15', 1),
(97, 90.00, 5, 38, '2025-05-15', 1),
(98, 7.00, 2, 38, '2025-05-15', 1),
(99, 190.00, 4, 38, '2025-05-15', 1),
(100, 4.64, 2, 38, '2025-05-15', 1),
(101, 6.75, 4, 38, '2025-05-15', 1),
(102, 95.00, 7, 38, '2025-05-15', 1),
(103, 22.20, 3, 38, '2025-05-15', 1),
(104, 14.80, 5, 38, '2025-05-15', 1),
(105, 1.11, 4, 38, '2025-05-15', 1),
(106, 95.00, 6, 39, '2025-05-15', 4),
(107, 95.00, 1, 39, '2025-05-15', 4),
(108, 22.20, 3, 38, '2025-05-15', 1),
(109, 14.80, 5, 38, '2025-05-15', 1),
(110, 1.11, 4, 38, '2025-05-15', 1),
(111, 22.20, 3, 38, '2025-05-15', 1),
(112, 14.80, 5, 38, '2025-05-15', 1),
(113, 1.11, 4, 38, '2025-05-15', 1),
(114, 22.20, 3, 38, '2025-05-15', 1),
(115, 14.80, 5, 38, '2025-05-15', 1),
(116, 1.11, 4, 38, '2025-05-15', 1),
(117, 19.20, 2, 42, '2025-05-18', 1),
(118, 9.60, 6, 42, '2025-05-18', 1),
(119, 1.44, 4, 42, '2025-05-18', 1),
(120, 23.28, 2, 43, '2025-05-19', 1),
(121, 1.55, 7, 43, '2025-05-19', 1),
(122, 1.94, 4, 43, '2025-05-19', 1),
(123, 23.40, 2, 43, '2025-05-19', 1),
(124, 1.56, 7, 43, '2025-05-19', 1),
(125, 1.95, 4, 43, '2025-05-19', 1),
(126, 2.76, 2, 43, '2025-05-19', 1),
(127, 0.18, 7, 43, '2025-05-19', 1),
(128, 0.23, 4, 43, '2025-05-19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_detalle`
--

CREATE TABLE `ingreso_detalle` (
  `id_ingreso_detalle` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_ingreso_insumo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ingreso_detalle`
--

INSERT INTO `ingreso_detalle` (`id_ingreso_detalle`, `cantidad`, `id_producto`, `id_ingreso_insumo`) VALUES
(63, 60.00, 3, 63),
(64, 60.00, 5, 64),
(65, 1000.00, 3, 65),
(66, 100.00, 5, 66),
(67, 2.00, 5, 67),
(68, 40.00, 5, 68),
(69, 7.00, 5, 69),
(70, 10.00, 5, 70),
(71, 50.00, 7, 71),
(72, 10.00, 5, 72),
(73, 10.00, 5, 73),
(74, 10.00, 5, 74),
(75, 200.00, 3, 75),
(76, 6.00, 2, 76),
(77, 2.00, 3, 77),
(78, 10.00, 3, 78),
(79, 10.00, 3, 79),
(80, 17.00, 3, 80),
(81, 0.01, 3, 81),
(82, 15.00, 3, 82),
(83, 12.00, 3, 83),
(84, 1.00, 3, 84),
(85, 15.00, 3, 85),
(86, 1.00, 3, 86),
(87, 1.00, 3, 87),
(88, 10.00, 3, 88),
(89, 10.00, 3, 89),
(90, 100.00, 5, 90),
(91, 100.00, 4, 91),
(92, 10.00, 3, 92),
(93, 100.00, 3, 93),
(94, 4.00, 2, 94),
(95, 3.00, 2, 95),
(96, 6.00, 2, 96),
(97, 10.00, 2, 97),
(98, 10.00, 7, 98),
(99, 10.00, 2, 99),
(100, 10.00, 7, 100),
(101, 10.00, 3, 101),
(102, 24.00, 3, 102),
(103, 7.00, 3, 103),
(104, 10.00, 1, 104),
(105, 90.00, 3, 105),
(106, 80.00, 1, 106),
(107, 4.00, 5, 107),
(108, 90.00, 5, 108),
(109, 100.00, 2, 109),
(110, 6.02, 7, 110),
(111, 90.00, 7, 111),
(112, 90.00, 6, 112),
(113, 100.00, 4, 113),
(114, 4.00, 8, 114),
(115, 4.00, 8, 115),
(116, 10.00, 6, 116),
(117, 10.00, 1, 117);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_insumo`
--

CREATE TABLE `ingreso_insumo` (
  `id_ingreso_insumo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `observacion` text DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ingreso_insumo`
--

INSERT INTO `ingreso_insumo` (`id_ingreso_insumo`, `fecha`, `observacion`, `estado`, `created_by`) VALUES
(63, '2025-04-21', 'Ingreso de stock', 1, 1),
(64, '2025-04-21', 'Ingreso de stock', 1, 1),
(65, '2025-04-21', 'Ingreso de stock', 1, 1),
(66, '2025-04-21', 'Ingreso de stock', 1, 1),
(67, '2025-04-21', 'Ingreso de stock', 1, 1),
(68, '2025-04-22', 'Ingreso de stock', 1, 1),
(69, '2025-04-22', 'Ingreso de stock', 1, 1),
(70, '2025-04-22', 'Ingreso de stock', 1, 1),
(71, '2025-04-30', 'Ingreso de stock', 1, 1),
(72, '2025-04-30', 'Ingreso de stock', 1, 1),
(73, '2025-04-30', 'Ingreso de stock', 1, 1),
(74, '2025-04-30', 'Ingreso de stock', 1, 1),
(75, '2025-04-30', 'Ingreso de stock', 1, 1),
(76, '2025-04-30', 'Ingreso de stock', 1, 1),
(77, '2025-04-30', 'Ingreso de stock', 1, 1),
(78, '2025-05-10', 'Ingreso de stock', 1, 1),
(79, '2025-05-10', 'Ingreso de stock', 1, 1),
(80, '2025-05-10', 'Ingreso de stock', 1, 1),
(81, '2025-05-10', 'Ingreso de stock', 1, 1),
(82, '2025-05-10', 'Ingreso de stock', 1, 1),
(83, '2025-05-12', 'Ingreso de stock', 1, 1),
(84, '2025-05-12', 'Ingreso de stock', 1, 1),
(85, '2025-05-12', 'Ingreso de stock', 1, 1),
(86, '2025-05-12', 'Ingreso de stock', 1, 1),
(87, '2025-05-12', 'Ingreso de stock', 1, 1),
(88, '2025-05-12', 'Ingreso de stock', 1, 1),
(89, '2025-05-12', 'Ingreso de stock', 1, 1),
(90, '2025-05-13', 'Ingreso de stock', 1, 1),
(91, '2025-05-13', 'Ingreso de stock', 1, 1),
(92, '2025-05-13', 'Ingreso de stock', 1, 1),
(93, '2025-05-13', 'Ingreso de stock', 1, 1),
(94, '2025-05-13', 'Ingreso de stock', 1, 1),
(95, '2025-05-13', 'Ingreso de stock', 1, 1),
(96, '2025-05-13', 'Ingreso de stock', 1, 1),
(97, '2025-05-13', 'Ingreso de stock', 1, 1),
(98, '2025-05-13', 'Ingreso de stock', 1, 1),
(99, '2025-05-14', 'Ingreso de stock', 1, 1),
(100, '2025-05-14', 'Ingreso de stock', 1, 1),
(101, '2025-05-15', 'Ingreso de stock', 1, 1),
(102, '2025-05-15', 'Ingreso de stock', 1, 1),
(103, '2025-05-15', 'Ingreso de stock', 1, 1),
(104, '2025-05-15', 'Ingreso de stock', 1, 1),
(105, '2025-05-15', 'Ingreso de stock', 1, 1),
(106, '2025-05-15', 'Ingreso de stock', 1, 1),
(107, '2025-05-15', 'Ingreso de stock', 1, 1),
(108, '2025-05-15', 'Ingreso de stock', 1, 1),
(109, '2025-05-15', 'Ingreso de stock', 1, 1),
(110, '2025-05-15', 'Ingreso de stock', 1, 1),
(111, '2025-05-15', 'Ingreso de stock', 1, 1),
(112, '2025-05-15', 'Ingreso de stock', 1, 1),
(113, '2025-05-15', 'Ingreso de stock', 1, 1),
(114, '2025-05-15', 'Ingreso de stock', 1, 4),
(115, '2025-05-15', 'Ingreso de stock', 1, 4),
(116, '2025-05-18', 'Ingreso de stock', 1, 1),
(117, '2025-05-18', 'Ingreso de stock', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

CREATE TABLE `matricula` (
  `id_matricula` int(11) NOT NULL,
  `tipo` enum('estudiante','docente','otros') NOT NULL,
  `grado` varchar(20) DEFAULT NULL,
  `seccion` varchar(10) DEFAULT NULL,
  `lapso_academico` varchar(20) NOT NULL,
  `total_masculino` int(11) DEFAULT 0,
  `total_femenino` int(11) DEFAULT 0,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `matricula`
--

INSERT INTO `matricula` (`id_matricula`, `tipo`, `grado`, `seccion`, `lapso_academico`, `total_masculino`, `total_femenino`, `estado`) VALUES
(1, 'estudiante', '1', 'A', '2025-2026', 15, 15, 1),
(2, 'docente', NULL, NULL, '2025-2026', 2, 10, 1),
(3, 'otros', NULL, NULL, '2025-2026', 8, 2, 1),
(4, 'estudiante', '1', 'B', '2025-2026', 15, 15, 1),
(5, 'estudiante', '2', 'A', '2025-2026', 10, 10, 1),
(7, 'estudiante', '2', 'B', '2025-2026', 12, 10, 1),
(8, 'estudiante', '3', 'A', '2025-2026', 12, 10, 1),
(9, 'estudiante', '1', 'C', '2025-2026', 10, 10, 1),
(10, 'estudiante', '3', 'B', '2025-2026', 10, 10, 1),
(15, 'estudiante', '3', 'C', '2025-2026', 10, 10, 1),
(17, 'estudiante', '2', 'C', '2025-2026', 10, 15, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula_limite`
--

CREATE TABLE `matricula_limite` (
  `id_limite` char(36) NOT NULL DEFAULT uuid(),
  `id_matricula` int(11) DEFAULT NULL,
  `limite_masculino` int(11) NOT NULL DEFAULT 0,
  `limite_femenino` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `matricula_limite`
--

INSERT INTO `matricula_limite` (`id_limite`, `id_matricula`, `limite_masculino`, `limite_femenino`, `created_at`, `updated_at`) VALUES
('224ce4e0-2580-11f0-85ba-9c7befb99f01', 7, 12, 10, '2025-04-30 01:01:10', '2025-04-30 01:01:10'),
('25133102-2580-11f0-85ba-9c7befb99f01', 8, 12, 10, '2025-04-30 01:01:15', '2025-04-30 01:01:15'),
('5b658088-3433-11f0-8c0e-9c7befb99f01', 9, 12, 10, '2025-05-18 17:59:22', '2025-05-18 17:59:22'),
('60bf2edf-3433-11f0-8c0e-9c7befb99f01', 15, 9, 15, '2025-05-18 17:59:31', '2025-05-18 17:59:31'),
('790275ed-1d9c-11f0-9c60-9c7befb99f01', 1, 15, 15, '2025-04-20 00:03:53', '2025-04-20 00:03:53'),
('7f50263e-1d9c-11f0-9c60-9c7befb99f01', 4, 15, 15, '2025-04-20 00:04:03', '2025-04-20 00:04:37'),
('884c2217-3146-11f0-8d80-9c7befb99f01', 10, 10, 10, '2025-05-15 00:39:05', '2025-05-15 00:39:05'),
('8a9e7d64-3433-11f0-8c0e-9c7befb99f01', 17, 10, 15, '2025-05-18 18:00:42', '2025-05-18 18:00:42'),
('976c733e-1d9c-11f0-9c60-9c7befb99f01', 5, 10, 10, '2025-04-20 00:04:44', '2025-04-20 00:04:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `observacion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id_menu`, `nombre`, `observacion`, `fecha`, `estado`, `created_by`, `created_at`) VALUES
(1, 'Arroz con carne', 'menu de arroz con carne', '2025-04-20', 1, 1, '2025-04-20 03:41:53'),
(2, 'Pasta con carne', '', '2025-04-20', 1, 1, '2025-04-20 04:06:00'),
(3, 'Pasta con queso', '', '2025-04-20', 1, 1, '2025-04-20 06:52:25'),
(8, 'ensalada', 'ensalada cocida ', '2025-05-15', 0, 4, '2025-05-15 05:18:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_producto`
--

CREATE TABLE `menu_producto` (
  `id_menu_producto` int(11) NOT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad_por_plato` decimal(10,5) NOT NULL DEFAULT 0.00000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `menu_producto`
--

INSERT INTO `menu_producto` (`id_menu_producto`, `id_menu`, `id_producto`, `cantidad_por_plato`, `created_at`, `updated_at`) VALUES
(21, 3, 2, 0.12000, '2025-04-20 06:52:39', '2025-04-20 06:52:39'),
(22, 3, 6, 0.06000, '2025-04-20 06:52:39', '2025-04-20 06:52:39'),
(23, 3, 4, 0.00900, '2025-04-20 06:52:39', '2025-04-20 06:52:39'),
(36, 2, 2, 0.12000, '2025-04-30 13:35:17', '2025-04-30 13:35:17'),
(37, 2, 7, 0.00800, '2025-04-30 13:35:17', '2025-04-30 13:35:17'),
(38, 2, 4, 0.01000, '2025-04-30 13:35:17', '2025-04-30 13:35:17'),
(49, 1, 3, 0.12000, '2025-05-14 19:14:52', '2025-05-14 19:14:52'),
(50, 1, 5, 0.08000, '2025-05-14 19:14:52', '2025-05-14 19:14:52'),
(51, 1, 4, 0.00600, '2025-05-14 19:14:52', '2025-05-14 19:14:52'),
(52, 8, 3, 0.10000, '2025-05-15 05:20:30', '2025-05-15 05:20:30'),
(53, 8, 5, 0.10000, '2025-05-15 05:20:30', '2025-05-15 05:20:30'),
(54, 8, 6, 0.01000, '2025-05-15 05:20:30', '2025-05-15 05:20:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `stock` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `id_unidad` int(11) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre`, `stock`, `stock_minimo`, `id_unidad`, `estado`) VALUES
(1, 'Azucar', 15.00, 10.00, 1, 1),
(2, 'Pasta', 31.36, 5.00, 1, 1),
(3, 'Arroz', 11.20, 5.00, 1, 1),
(4, 'Sal', 90.00, 10.00, 1, 1),
(5, 'Carne', 40.80, 10.00, 1, 1),
(6, 'Queso', 5.40, 10.00, 1, 1),
(7, 'Pimiento', 1.71, 5.00, 1, 1),
(8, 'kom', 12.00, 4.00, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad`
--

CREATE TABLE `unidad` (
  `id_unidad` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `simbolo` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `unidad`
--

INSERT INTO `unidad` (`id_unidad`, `nombre`, `simbolo`) VALUES
(1, 'Kilogramos', 'kg'),
(2, 'Litros', 'L'),
(3, 'Unidades', 'u'),
(4, 'Kilogramos', 'kg'),
(5, 'Litros', 'L'),
(6, 'Unidades', 'u'),
(7, 'Kilogramos', 'kg'),
(8, 'Litros', 'L'),
(9, 'Unidades', 'u');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `rol` enum('admin','base','cocinero') NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nombre_completo`, `rol`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'yerson', '$2y$10$jhcI16gNevrCSfODTSXknOcYuvAoGK15yF7MAH41tNzxU7DTBh2Dm', 'Administrador', 'admin', 1, '2025-04-20 03:34:54', '2025-04-20 03:40:04'),
(4, 'Cocinero', '$2y$10$.7X48A/2PQW9/hdV3axQXOtaIgPza3KoABds2K1MvRu8bpjgVPmXK', 'Cocinero', 'cocinero', 1, '2025-04-21 18:16:20', '2025-04-21 18:16:20'),
(5, 'usuario', '$2y$10$lXTHmMe6V6Eq0sP9unOa4uKmsWxMbQkmmjvfoHrg4BJTMooVd753q', 'Usuario normal', 'base', 1, '2025-04-21 18:16:35', '2025-05-15 03:50:31');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_matricula` (`id_matricula`);

--
-- Indices de la tabla `consumo`
--
ALTER TABLE `consumo`
  ADD PRIMARY KEY (`id_consumo`),
  ADD KEY `id_menu` (`id_menu`),
  ADD KEY `idx_consumo_fecha` (`fecha`);

--
-- Indices de la tabla `consumo_asistencia`
--
ALTER TABLE `consumo_asistencia`
  ADD PRIMARY KEY (`id_consumo_asistencia`),
  ADD KEY `id_consumo` (`id_consumo`),
  ADD KEY `id_asistencia` (`id_asistencia`),
  ADD KEY `id_matricula` (`id_matricula`);

--
-- Indices de la tabla `consumo_detalle`
--
ALTER TABLE `consumo_detalle`
  ADD PRIMARY KEY (`id_consumo_detalle`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_consumo` (`id_consumo`);

--
-- Indices de la tabla `ingreso_detalle`
--
ALTER TABLE `ingreso_detalle`
  ADD PRIMARY KEY (`id_ingreso_detalle`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_ingreso_insumo` (`id_ingreso_insumo`);

--
-- Indices de la tabla `ingreso_insumo`
--
ALTER TABLE `ingreso_insumo`
  ADD PRIMARY KEY (`id_ingreso_insumo`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id_matricula`);

--
-- Indices de la tabla `matricula_limite`
--
ALTER TABLE `matricula_limite`
  ADD PRIMARY KEY (`id_limite`),
  ADD UNIQUE KEY `id_matricula` (`id_matricula`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `menu_producto`
--
ALTER TABLE `menu_producto`
  ADD PRIMARY KEY (`id_menu_producto`),
  ADD UNIQUE KEY `unique_menu_producto` (`id_menu`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_unidad` (`id_unidad`);

--
-- Indices de la tabla `unidad`
--
ALTER TABLE `unidad`
  ADD PRIMARY KEY (`id_unidad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT de la tabla `consumo`
--
ALTER TABLE `consumo`
  MODIFY `id_consumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `consumo_asistencia`
--
ALTER TABLE `consumo_asistencia`
  MODIFY `id_consumo_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de la tabla `consumo_detalle`
--
ALTER TABLE `consumo_detalle`
  MODIFY `id_consumo_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `ingreso_detalle`
--
ALTER TABLE `ingreso_detalle`
  MODIFY `id_ingreso_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `ingreso_insumo`
--
ALTER TABLE `ingreso_insumo`
  MODIFY `id_ingreso_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de la tabla `matricula`
--
ALTER TABLE `matricula`
  MODIFY `id_matricula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `menu_producto`
--
ALTER TABLE `menu_producto`
  MODIFY `id_menu_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `unidad`
--
ALTER TABLE `unidad`
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`);

--
-- Filtros para la tabla `consumo`
--
ALTER TABLE `consumo`
  ADD CONSTRAINT `consumo_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`);

--
-- Filtros para la tabla `consumo_asistencia`
--
ALTER TABLE `consumo_asistencia`
  ADD CONSTRAINT `consumo_asistencia_ibfk_1` FOREIGN KEY (`id_consumo`) REFERENCES `consumo` (`id_consumo`),
  ADD CONSTRAINT `consumo_asistencia_ibfk_2` FOREIGN KEY (`id_asistencia`) REFERENCES `asistencia` (`id_asistencia`);

--
-- Filtros para la tabla `consumo_detalle`
--
ALTER TABLE `consumo_detalle`
  ADD CONSTRAINT `consumo_detalle_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `consumo_detalle_ibfk_2` FOREIGN KEY (`id_consumo`) REFERENCES `consumo` (`id_consumo`);

--
-- Filtros para la tabla `ingreso_detalle`
--
ALTER TABLE `ingreso_detalle`
  ADD CONSTRAINT `ingreso_detalle_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  ADD CONSTRAINT `ingreso_detalle_ibfk_2` FOREIGN KEY (`id_ingreso_insumo`) REFERENCES `ingreso_insumo` (`id_ingreso_insumo`);

--
-- Filtros para la tabla `ingreso_insumo`
--
ALTER TABLE `ingreso_insumo`
  ADD CONSTRAINT `ingreso_insumo_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `matricula_limite`
--
ALTER TABLE `matricula_limite`
  ADD CONSTRAINT `fk_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`);

--
-- Filtros para la tabla `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `menu_producto`
--
ALTER TABLE `menu_producto`
  ADD CONSTRAINT `menu_producto_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`),
  ADD CONSTRAINT `menu_producto_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_unidad`) REFERENCES `unidad` (`id_unidad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
