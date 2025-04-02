-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-04-2025 a las 11:44:15
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `aplicacion_vuelos_sec`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aeropuertos_canarias`
--

CREATE TABLE `aeropuertos_canarias` (
  `isla` varchar(50) DEFAULT NULL,
  `codigo_iata` varchar(3) NOT NULL,
  `provincia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aeropuertos_canarias`
--

INSERT INTO `aeropuertos_canarias` (`isla`, `codigo_iata`, `provincia`) VALUES
('Lanzarote', 'ACE', 'Las Palmas'),
('Fuerteventura', 'FUE', 'Las Palmas'),
('La Gomera', 'GMZ', 'Santa Cruz de Tenerife'),
('Gran Canaria', 'LPA', 'Las Palmas'),
('La Palma', 'SPC', 'Santa Cruz de Tenerife'),
('Tenerife Norte', 'TFN', 'Santa Cruz de Tenerife'),
('Tenerife Sur', 'TFS', 'Santa Cruz de Tenerife'),
('El Hierro', 'VDE', 'Santa Cruz de Tenerife');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_vuelo`
--

CREATE TABLE `estados_vuelo` (
  `id` int(11) NOT NULL,
  `nombre_estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados_vuelo`
--

INSERT INTO `estados_vuelo` (`id`, `nombre_estado`) VALUES
(1, 'activo'),
(2, 'encuestado'),
(3, 'expirado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises_banderas`
--

CREATE TABLE `paises_banderas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviacion` varchar(5) NOT NULL,
  `nombre_espanol` varchar(100) NOT NULL,
  `nombre_ingles` varchar(100) NOT NULL,
  `nombre_nativo` varchar(100) NOT NULL,
  `bandera` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paises_banderas`
--

INSERT INTO `paises_banderas` (`id`, `nombre`, `abreviacion`, `nombre_espanol`, `nombre_ingles`, `nombre_nativo`, `bandera`) VALUES
(1, 'PAIS DESCONOCIDO', 'DES', 'PAÍS DESCONOCIDO', 'UNKNOWN COUNTRY', 'UNKNOWN', '../assets/image_flags/unknown.png'),
(2, 'ALBANIA', 'AL', 'ALBANIA', 'ALBANIA', 'SHQIPËRIA', '../assets/image_flags/al.png'),
(3, 'ALEMANIA', 'DE', 'ALEMANIA', 'GERMANY', 'DEUTSCHLAND', '../assets/image_flags/de.png'),
(4, 'ANDORRA', 'AD', 'ANDORRA', 'ANDORRA', 'ANDORRA', '../assets/image_flags/ad.png'),
(5, 'AUSTRIA', 'AT', 'AUSTRIA', 'AUSTRIA', 'ÖSTERREICH', '../assets/image_flags/at.png'),
(6, 'BELGICA', 'BE', 'BÉLGICA', 'BELGIUM', 'BELGIË', '../assets/image_flags/be.png'),
(7, 'BIELORRUSIA', 'BY', 'BIELORRUSIA', 'BELARUS', 'БЕЛАРУСЬ', '../assets/image_flags/by.png'),
(8, 'BOSNIA Y HERZEGOVINA', 'BA', 'BOSNIA Y HERZEGOVINA', 'BOSNIA AND HERZEGOVINA', 'BOSNA I HERCEGOVINA', '../assets/image_flags/ba.png'),
(9, 'BULGARIA', 'BG', 'BULGARIA', 'BULGARIA', 'БЪЛГАРИЯ', '../assets/image_flags/bg.png'),
(10, 'CHIPRE', 'CY', 'CHIPRE', 'CYPRUS', 'ΚΥΠΡΟΣ', '../assets/image_flags/cy.png'),
(11, 'CIUDAD DEL VATICANO', 'VA', 'CIUDAD DEL VATICANO', 'VATICAN CITY', 'CITTÀ DEL VATICANO', '../assets/image_flags/va.png'),
(12, 'CROACIA', 'HR', 'CROACIA', 'CROATIA', 'HRVATSKA', '../assets/image_flags/hr.png'),
(13, 'DINAMARCA', 'DK', 'DINAMARCA', 'DENMARK', 'DANMARK', '../assets/image_flags/dk.png'),
(14, 'ESLOVAQUIA', 'SK', 'ESLOVAQUIA', 'SLOVAKIA', 'SLOVENSKO', '../assets/image_flags/sk.png'),
(15, 'ESLOVENIA', 'SI', 'ESLOVENIA', 'SLOVENIA', 'SLOVENIJA', '../assets/image_flags/si.png'),
(16, 'ESPAÑA', 'ES', 'ESPAÑA', 'SPAIN', 'ESPAÑA', '../assets/image_flags/es.png'),
(17, 'ESTONIA', 'EE', 'ESTONIA', 'ESTONIA', 'EESTI', '../assets/image_flags/ee.png'),
(18, 'FINLANDIA', 'FI', 'FINLANDIA', 'FINLAND', 'SUOMI', '../assets/image_flags/fi.png'),
(19, 'FRANCIA', 'FR', 'FRANCIA', 'FRANCE', 'FRANCE', '../assets/image_flags/fr.png'),
(20, 'GRECIA', 'GR', 'GRECIA', 'GREECE', 'ΕΛΛΆΔΑ', '../assets/image_flags/gr.png'),
(21, 'HUNGRIA', 'HU', 'HUNGRÍA', 'HUNGARY', 'MAGYARORSZÁG', '../assets/image_flags/hu.png'),
(22, 'IRLANDA', 'IE', 'IRLANDA', 'IRELAND', 'ÉIRE', '../assets/image_flags/ie.png'),
(23, 'ISLANDIA', 'IS', 'ISLANDIA', 'ICELAND', 'ÍSLAND', '../assets/image_flags/is.png'),
(24, 'ITALIA', 'IT', 'ITALIA', 'ITALY', 'ITALIA', '../assets/image_flags/it.png'),
(25, 'KOSOVO', 'XK', 'KOSOVO', 'KOSOVO', 'KOSOVË', '../assets/image_flags/xk.png'),
(26, 'LETONIA', 'LV', 'LETONIA', 'LATVIA', 'LATVIJA', '../assets/image_flags/lv.png'),
(27, 'LIECHTENSTEIN', 'LI', 'LIECHTENSTEIN', 'LIECHTENSTEIN', 'LIECHTENSTEIN', '../assets/image_flags/li.png'),
(28, 'LITUANIA', 'LT', 'LITUANIA', 'LITHUANIA', 'LIETUVA', '../assets/image_flags/lt.png'),
(29, 'LUXEMBURGO', 'LU', 'LUXEMBURGO', 'LUXEMBOURG', 'LUXEMBOURG', '../assets/image_flags/lu.png'),
(30, 'MACEDONIA DEL NORTE', 'MK', 'MACEDONIA DEL NORTE', 'NORTH MACEDONIA', 'СЕВЕРНА МАКЕДОНИЈА', '../assets/image_flags/mk.png'),
(31, 'MALTA', 'MT', 'MALTA', 'MALTA', 'MALTA', '../assets/image_flags/mt.png'),
(32, 'MOLDAVIA', 'MD', 'MOLDAVIA', 'MOLDOVA', 'MOLDOVA', '../assets/image_flags/md.png'),
(33, 'MÓNACO', 'MC', 'MÓNACO', 'MONACO', 'MONACO', '../assets/image_flags/mc.png'),
(34, 'MONTENEGRO', 'ME', 'MONTENEGRO', 'MONTENEGRO', 'CRNA GORA', '../assets/image_flags/me.png'),
(35, 'NORUEGA', 'NO', 'NORUEGA', 'NORWAY', 'NORGES', '../assets/image_flags/no.png'),
(36, 'HOLANDA', 'NL', 'HOLANDA', 'NETHERLANDS', 'NEDERLAND', '../assets/image_flags/nl.png'),
(37, 'POLONIA', 'PL', 'POLONIA', 'POLAND', 'POLSKA', '../assets/image_flags/pl.png'),
(38, 'PORTUGAL', 'PT', 'PORTUGAL', 'PORTUGAL', 'PORTUGAL', '../assets/image_flags/pt.png'),
(39, 'REINO UNIDO', 'GB', 'REINO UNIDO', 'UNITED KINGDOM', 'UNITED KINGDOM', '../assets/image_flags/gb.png'),
(40, 'REPÚBLICA CHECA', 'CZ', 'REPÚBLICA CHECA', 'CZECH REPUBLIC', 'ČESKO', '../assets/image_flags/cz.png'),
(41, 'RUMANIA', 'RO', 'RUMANIA', 'ROMANIA', 'ROMÂNIA', '../assets/image_flags/ro.png'),
(42, 'RUSIA', 'RU', 'RUSIA', 'RUSSIA', 'РОССИЯ', '../assets/image_flags/ru.png'),
(43, 'SAN MARINO', 'SM', 'SAN MARINO', 'SAN MARINO', 'SAN MARINO', '../assets/image_flags/sm.png'),
(44, 'SERBIA', 'RS', 'SERBIA', 'SERBIA', 'СРБИЈА', '../assets/image_flags/rs.png'),
(45, 'SUECIA', 'SE', 'SUECIA', 'SWEDEN', 'SVERIGE', '../assets/image_flags/se.png'),
(46, 'SUIZA', 'CH', 'SUIZA', 'SWITZERLAND', 'SCHWEIZ', '../assets/image_flags/ch.png'),
(47, 'UCRANIA', 'UA', 'UCRANIA', 'UKRAINE', 'УКРАЇНА', '../assets/image_flags/ua.png'),
(72, 'ARGELIA', 'DZ', 'ARGELIA', 'ALGERIA', 'الجزائر', '../assets/image_flags/dz.png'),
(73, 'AUSTRALIA', 'AU', 'AUSTRALIA', 'AUSTRALIA', 'AUSTRALIA', '../assets/image_flags/au.png'),
(74, 'BRASIL', 'BR', 'BRASIL', 'BRAZIL', 'BRASIL', '../assets/image_flags/br.png'),
(75, 'CANADA', 'CA', 'CANADÁ', 'CANADA', 'CANADA', '../assets/image_flags/ca.png'),
(76, 'CHINA', 'CN', 'CHINA', 'CHINA', '中国', '../assets/image_flags/cn.png'),
(77, 'COREA DEL SUR', 'KR', 'COREA DEL SUR', 'SOUTH KOREA', '대한민국', '../assets/image_flags/kr.png'),
(78, 'EGIPTO', 'EG', 'EGIPTO', 'EGYPT', 'مصر', '../assets/image_flags/eg.png'),
(79, 'EMIRATOS ARABES UNIDOS', 'AE', 'EMIRATOS ÁRABES UNIDOS', 'UNITED ARAB EMIRATES', 'الإمارات العربية المتحدة', '../assets/image_flags/ae.png'),
(80, 'ESTADOS UNIDOS', 'US', 'ESTADOS UNIDOS', 'UNITED STATES', 'UNITED STATES', '../assets/image_flags/us.png'),
(81, 'INDIA', 'IN', 'INDIA', 'INDIA', 'भारत', '../assets/image_flags/in.png'),
(82, 'ISRAEL', 'IL', 'ISRAEL', 'ISRAEL', 'יִשְׂרָאֵל', '../assets/image_flags/il.png'),
(83, 'JAPON', 'JP', 'JAPÓN', 'JAPAN', '日本', '../assets/image_flags/jp.png'),
(84, 'MARRUECOS', 'MA', 'MARRUECOS', 'MOROCCO', 'المغرب', '../assets/image_flags/ma.png'),
(85, 'MEXICO', 'MX', 'MÉXICO', 'MEXICO', 'MÉXICO', '../assets/image_flags/mx.png'),
(86, 'NUEVA ZELANDA', 'NZ', 'NUEVA ZELANDA', 'NEW ZEALAND', 'AOTEAROA', '../assets/image_flags/nz.png'),
(87, 'PERU', 'PE', 'PERÚ', 'PERU', 'PERÚ', '../assets/image_flags/pe.png'),
(88, 'QATAR', 'QA', 'QATAR', 'QATAR', 'قطر', '../assets/image_flags/qa.png'),
(89, 'SUDÁFRICA', 'ZA', 'SUDÁFRICA', 'SOUTH AFRICA', 'SOUTH AFRICA', '../assets/image_flags/za.png'),
(90, 'TURQUIA', 'TR', 'TURQUÍA', 'TURKEY', 'TÜRKİYE', '../assets/image_flags/tr.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido1` varchar(50) DEFAULT NULL,
  `apellido2` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_conexion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido1`, `apellido2`, `password`, `email`, `fecha_creacion`, `ultima_conexion`) VALUES
(1, 'usuario1', '', '', '$2y$10$Fsa/CKNoqRPKQJtNjeEDr.BumZwXNb4hpunu8kITmV0DQafj4hARi', 'usuario1@gmail.com', '2025-03-09 19:03:05', '2025-04-02 11:02:15'),
(2, 'usuario2', '', '', '$2y$10$jLgSAGa2wcFzCSMx9MWsi.0XyLfq1tmc2gTe5JeFfcVm3QeCE7tQy', 'usuario2@usuario.com', '2025-03-09 19:05:00', '2025-03-11 14:18:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelos`
--

CREATE TABLE `vuelos` (
  `id` int(11) NOT NULL,
  `obs` int(11) NOT NULL,
  `hora_salida` time NOT NULL,
  `origen` varchar(10) NOT NULL,
  `dia_semana` varchar(10) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `ciudad_destino` varchar(255) NOT NULL,
  `pais_destino` varchar(255) NOT NULL,
  `escala` varchar(10) DEFAULT NULL,
  `aeronave` varchar(10) DEFAULT NULL,
  `num_vuelo` varchar(20) NOT NULL,
  `opera_desde` date NOT NULL,
  `opera_hasta` date NOT NULL,
  `fecha` date NOT NULL,
  `estado_id` int(11) NOT NULL DEFAULT 1,
  `encuestas_realizadas` smallint(5) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vuelos`
--

INSERT INTO `vuelos` (`id`, `obs`, `hora_salida`, `origen`, `dia_semana`, `codigo`, `ciudad_destino`, `pais_destino`, `escala`, `aeronave`, `num_vuelo`, `opera_desde`, `opera_hasta`, `fecha`, `estado_id`, `encuestas_realizadas`) VALUES
(1, 1, '06:50:00', 'ACE', 'L', 'LIS', 'LISBOA/HUMBERTO DELGADO', 'PORTUGAL', 'N/A', '320', 'IBE4321', '2024-10-15', '2025-02-20', '2025-01-12', 1, 0),
(2, 2, '07:15:00', 'ACE', 'M', 'BRU', 'BRUSELAS/ZAVENTEM', 'BÉLGICA', 'N/A', '7M8', 'RYR8820', '2024-11-05', '2025-03-15', '2025-01-10', 1, 0),
(3, 3, '07:40:00', 'ACE', 'X', 'FCO', 'ROMA/FIUMICINO', 'ITALIA', 'N/A', '73H', 'RYR1034', '2024-10-22', '2025-01-10', '2025-01-04', 1, 0),
(4, 4, '08:00:00', 'ACE', 'J', 'CDG', 'PARÍS/CHARLES DE GAULLE', 'FRANCIA', 'N/A', '321', 'VLG9022', '2024-12-12', '2025-01-25', '2025-01-17', 1, 0),
(5, 5, '08:10:00', 'ACE', 'V', 'TXL', 'BERLÍN/TEGEL', 'ALEMANIA', 'N/A', '32A', 'TAP3190', '2024-11-28', '2025-02-12', '2025-01-05', 1, 0),
(6, 6, '08:25:00', 'ACE', 'S', 'GVA', 'GINEBRA', 'SUIZA', 'N/A', '320', 'BAW1543', '2024-12-01', '2025-03-22', '2025-01-09', 1, 0),
(7, 7, '08:35:00', 'ACE', 'D', 'SVO', 'MOSCÚ/SHEREMETYEVO', 'RUSIA', 'N/A', '32N', 'EJU2145', '2024-11-10', '2025-01-05', '2025-01-02', 1, 0),
(8, 8, '08:45:00', 'LPA', 'L', 'PMI', 'PALMA DE MALLORCA', 'ESPAÑA', 'N/A', '7M8', 'RYR7710', '2024-11-17', '2025-01-12', '2025-01-10', 1, 0),
(9, 9, '09:00:00', 'LPA', 'M', 'AGP', 'MÁLAGA/COSTA DEL SOL', 'ESPAÑA', 'N/A', '73H', 'TUI7744', '2024-10-05', '2025-03-15', '2025-02-04', 1, 0),
(10, 10, '09:05:00', 'LPA', 'X', 'FRA', 'FRÁNCFORT', 'GERMANIA', 'N/A', '32N', 'EZS9901', '2024-11-02', '2025-01-20', '2025-01-07', 1, 0),
(11, 11, '09:15:00', 'GMZ', 'J', 'PRG', 'PRAGA', 'R. CHECA', 'N/A', '73H', 'RYR3456', '2024-10-31', '2025-03-01', '2025-01-06', 1, 0),
(12, 12, '09:30:00', 'VDE', 'VS', 'LYS', 'LYON/SAINT-EXUPÉRY', 'FRANCIA', 'N/A', '320', 'AFR2830', '2024-11-22', '2025-03-15', '2025-01-08', 1, 0),
(13, 13, '09:45:00', 'TFS', 'LMJ', 'KRK', 'CRACOVIA', 'BIELORRUSIA', 'N/A', '321', 'IBE1010', '2024-12-10', '2025-02-28', '2025-01-17', 1, 0),
(14, 14, '09:55:00', 'TFS', 'X', 'VIE', 'VIENA', 'AUSTRIA', 'N/A', '32Q', 'RYR1289', '2024-11-06', '2025-04-01', '2025-01-13', 1, 0),
(15, 15, '10:10:00', 'TFN', 'D', 'CPH', 'COPENHAGUE', 'DINAMARCA', 'N/A', '7M8', 'EIN345', '2024-10-01', '2025-02-20', '2025-01-15', 1, 0),
(16, 16, '10:25:00', 'TFN', 'L', 'HEL', 'HELSINKI', 'FINLANDIA', 'N/A', '73H', 'EJU3098', '2024-11-13', '2025-01-25', '2025-01-19', 1, 0),
(17, 17, '10:35:00', 'SPC', 'J', 'MXP', 'MILÁN/MALPENSA', 'ITALIA', 'N/A', '32N', 'EWG9902', '2024-10-02', '2025-01-12', '2025-01-10', 1, 0),
(18, 18, '10:50:00', 'SPC', 'M', 'ATH', 'ATENAS', 'GRECIA', 'N/A', '7S8', 'TOM3211', '2024-10-18', '2025-01-10', '2025-01-08', 1, 0),
(19, 19, '11:05:00', 'FUE', 'V', 'OSL', 'OSLO', 'NORUEGA', 'N/A', '320', 'EZY2033', '2024-11-26', '2025-01-30', '2025-01-20', 1, 0),
(20, 20, '11:20:00', 'FUE', 'L', 'ARN', 'ESTOCOLMO/ARLANDA', 'SUECIA', 'N/A', '32A', 'EZY4442', '2024-10-05', '2025-03-12', '2025-01-16', 1, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aeropuertos_canarias`
--
ALTER TABLE `aeropuertos_canarias`
  ADD PRIMARY KEY (`codigo_iata`);

--
-- Indices de la tabla `estados_vuelo`
--
ALTER TABLE `estados_vuelo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_estado` (`nombre_estado`);

--
-- Indices de la tabla `paises_banderas`
--
ALTER TABLE `paises_banderas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `vuelos`
--
ALTER TABLE `vuelos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estado_id` (`estado_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `estados_vuelo`
--
ALTER TABLE `estados_vuelo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paises_banderas`
--
ALTER TABLE `paises_banderas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `vuelos`
--
ALTER TABLE `vuelos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `vuelos`
--
ALTER TABLE `vuelos`
  ADD CONSTRAINT `vuelos_ibfk_1` FOREIGN KEY (`estado_id`) REFERENCES `estados_vuelo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
