-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-09-2025 a las 21:30:27
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
-- Base de datos: `palabrato`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `palabras`
--

CREATE TABLE `palabras` (
  `id` int(11) NOT NULL,
  `palabra` varchar(255) NOT NULL,
  `palabra_sin_acento` varchar(255) NOT NULL,
  `longitud` int(11) NOT NULL,
  `definicion` text DEFAULT NULL,
  `pista` text DEFAULT NULL,
  `tipo_acento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `palabras`
--

INSERT INTO `palabras` (`id`, `palabra`, `palabra_sin_acento`, `longitud`, `definicion`, `pista`, `tipo_acento`) VALUES
(1, 'AMIGO', 'AMIGO', 5, 'Persona con la que se mantiene una relación de afecto.', 'Compañero cercano.', 'Aguda'),
(2, 'ARBOL', 'ARBOL', 5, 'Planta de tronco leñoso que se ramifica a cierta altura.', 'Da sombra.', 'Grave'),
(3, 'CASAS', 'CASAS', 5, 'Construcciones para habitar.', 'Lugar donde se vive.', 'Grave'),
(4, 'PERRO', 'PERRO', 5, 'Animal doméstico considerado el mejor amigo del hombre.', 'Animal que ladra.', 'Grave'),
(5, 'GATOS', 'GATOS', 5, 'Felinos domésticos.', 'Animal que maúlla.', 'Grave'),
(6, 'MANGO', 'MANGO', 5, 'Fruta tropical dulce y carnosa.', 'Fruta amarilla o roja.', 'Grave'),
(7, 'LUCES', 'LUCES', 5, 'Emisión de energía visible.', 'Se encienden de noche.', 'Grave'),
(8, 'FUEGO', 'FUEGO', 5, 'Fenómeno de combustión que emite calor y luz.', 'Lo contrario al agua.', 'Grave'),
(9, 'RATON', 'RATON', 5, 'Pequeño roedor.', 'Animal pequeño que corre.', 'Aguda'),
(10, 'NIEVE', 'NIEVE', 5, 'Precipitación congelada.', 'Cae en invierno.', 'Grave'),
(11, 'LLAVE', 'LLAVE', 5, 'Instrumento para abrir cerraduras.', 'Abre puertas.', 'Grave'),
(12, 'CALOR', 'CALOR', 5, 'Sensación térmica elevada.', 'Lo opuesto al frío.', 'Aguda'),
(13, 'FRIAS', 'FRIAS', 5, 'Que tienen baja temperatura.', 'Lo contrario de caliente.', 'Grave'),
(14, 'NUBES', 'NUBES', 5, 'Masa visible de vapor de agua en el cielo.', 'Flotan en el cielo.', 'Grave'),
(15, 'SUEÑO', 'SUENO', 5, 'Deseo o imágenes al dormir.', 'Lo tenés de noche.', 'Grave'),
(16, 'CAMPO', 'CAMPO', 5, 'Extensión de tierra cultivable.', 'Lugar rural.', 'Grave'),
(17, 'CIELO', 'CIELO', 5, 'Espacio en que se mueven los astros.', 'Está sobre tu cabeza.', 'Grave'),
(18, 'MARCO', 'MARCO', 5, 'Estructura que rodea un objeto.', 'Borde de un cuadro.', 'Grave'),
(19, 'LENTE', 'LENTE', 5, 'Pieza de vidrio para ver mejor.', 'Lo usan los miopes.', 'Grave'),
(20, 'ROCAS', 'ROCAS', 5, 'Piedras de gran tamaño.', 'Se encuentran en montañas.', 'Grave'),
(21, 'VIDAS', 'VIDAS', 5, 'Existencias de seres vivos.', 'Lo que se vive.', 'Grave'),
(22, 'NACER', 'NACER', 5, 'Venir al mundo.', 'Opuesto a morir.', 'Aguda'),
(23, 'MUERE', 'MUERE', 5, 'Dejar de vivir.', 'Opuesto a nacer.', 'Grave'),
(24, 'LLORA', 'LLORA', 5, 'Derramar lágrimas.', 'Se hace al estar triste.', 'Grave'),
(25, 'RIEGO', 'RIEGO', 5, 'Acción de regar plantas.', 'Se hace en jardines.', 'Grave'),
(26, 'SALTO', 'SALTO', 5, 'Impulso hacia arriba.', 'Acción de brincar.', 'Grave'),
(27, 'BOTES', 'BOTES', 5, 'Pequeñas embarcaciones.', 'Sirven en el agua.', 'Grave'),
(28, 'PLAYA', 'PLAYA', 5, 'Orilla del mar.', 'Lugar de vacaciones.', 'Grave'),
(29, 'ARENA', 'ARENA', 5, 'Conjunto de partículas finas en la playa.', 'Se pega en los pies.', 'Grave'),
(30, 'RUMBA', 'RUMBA', 5, 'Baile latino.', 'Estilo de música.', 'Grave'),
(31, 'SALSA', 'SALSA', 5, 'Estilo de baile y música.', 'Se baila en pareja.', 'Grave'),
(32, 'TANGO', 'TANGO', 5, 'Baile típico argentino.', 'Música del Río de la Plata.', 'Grave'),
(33, 'FLOTA', 'FLOTA', 5, 'Conjunto de barcos.', 'Grupo de naves.', 'Grave'),
(34, 'VELAS', 'VELAS', 5, 'Lienzos de barcos o veladoras.', 'Se encienden.', 'Grave'),
(35, 'COPAS', 'COPAS', 5, 'Recipientes para beber vino.', 'Se chocan al brindar.', 'Grave'),
(36, 'MATES', 'MATES', 5, 'Infusión típica argentina.', 'Se comparte.', 'Grave'),
(37, 'CAFES', 'CAFES', 5, 'Bebida estimulante.', 'Se toma en la mañana.', 'Grave'),
(38, 'PANES', 'PANES', 5, 'Alimento hecho de harina.', 'Lo venden en panaderías.', 'Grave'),
(39, 'QUESO', 'QUESO', 5, 'Derivado lácteo.', 'Se derrite en pizza.', 'Grave'),
(40, 'PIZZA', 'PIZZA', 5, 'Comida italiana famosa.', 'Con queso y tomate.', 'Grave'),
(41, 'SILLA', 'SILLA', 5, 'Mueble para sentarse.', 'Tiene respaldo.', 'Grave'),
(42, 'MESAS', 'MESAS', 5, 'Muebles para apoyar objetos.', 'Se usan para comer.', 'Grave'),
(43, 'TECHO', 'TECHO', 5, 'Parte superior de una casa.', 'Protege de la lluvia.', 'Grave'),
(44, 'PISOS', 'PISOS', 5, 'Superficie sobre la que se camina.', 'Se limpian.', 'Grave'),
(45, 'LLAMA', 'LLAMA', 5, 'Fuego o animal andino.', 'Animal de los Andes.', 'Grave'),
(46, 'PECES', 'PECES', 5, 'Animales que viven en el agua.', 'Viven en el mar.', 'Grave'),
(47, 'AVION', 'AVION', 5, 'Medio de transporte aéreo.', 'Vuela en el cielo.', 'Aguda'),
(48, 'TRENES', 'TRENE', 5, 'Vehículos sobre rieles.', 'Viaja sobre vías.', 'Grave'),
(49, 'COCHE', 'COCHE', 5, 'Automóvil.', 'Medio de transporte.', 'Grave'),
(50, 'RUEDA', 'RUEDA', 5, 'Objeto circular que gira.', 'Parte del auto.', 'Grave'),
(51, 'BOLAS', 'BOLAS', 5, 'Esferas pequeñas.', 'Se usan en juegos.', 'Grave'),
(52, 'PELOS', 'PELOS', 5, 'Filamentos que crecen en la piel.', 'Lo cortás en la peluquería.', 'Grave'),
(53, 'OJOSO', 'OJOSO', 5, 'Con muchos ojos.', 'Relacionado a ver.', 'Aguda'),
(54, 'CARNE', 'CARNE', 5, 'Tejido animal comestible.', 'Se come en asados.', 'Grave'),
(55, 'FRUTA', 'FRUTA', 5, 'Alimento vegetal dulce.', 'Manzana, pera, etc.', 'Grave'),
(56, 'JUGOS', 'JUGOS', 5, 'Bebidas hechas con frutas.', 'Se toman fríos.', 'Grave'),
(57, 'HUEVO', 'HUEVO', 5, 'Producto de gallinas.', 'Se fríe o hierve.', 'Grave'),
(58, 'LECHE', 'LECHE', 5, 'Bebida blanca de vaca.', 'Se toma en desayunos.', 'Grave'),
(59, 'QUESO', 'QUESO', 5, 'Producto lácteo.', 'Se ralla para pastas.', 'Grave'),
(60, 'LAPIZ', 'LAPIZ', 5, 'Instrumento para escribir.', 'Sirve para dibujar.', 'Aguda'),
(61, 'LIBRO', 'LIBRO', 5, 'Conjunto de hojas encuadernadas.', 'Se lee.', 'Grave'),
(62, 'HOJAS', 'HOJAS', 5, 'Partes planas de las plantas.', 'Son verdes.', 'Grave'),
(63, 'PLUMA', 'PLUMA', 5, 'Estructura de las aves.', 'Sirve para volar.', 'Grave'),
(64, 'PAPEL', 'PAPEL', 5, 'Hoja delgada para escribir.', 'Se usa en cuadernos.', 'Aguda'),
(65, 'SUELO', 'SUELO', 5, 'Superficie de la tierra.', 'Se pisa al caminar.', 'Grave'),
(66, 'LUNAS', 'LUNAS', 5, 'Satélites naturales.', 'Se ven de noche.', 'Grave'),
(67, 'SOLAR', 'SOLAR', 5, 'Relacionado al sol.', 'Energía natural.', 'Aguda'),
(68, 'CRUZ', 'CRUZO', 5, 'Símbolo religioso.', 'Figura con 4 puntas.', 'Aguda'),
(69, 'CLAVE', 'CLAVE', 5, 'Código o solución.', 'Sirve para entrar.', 'Grave'),
(70, 'CODIGO', 'CODIG', 5, 'Conjunto de símbolos.', 'Usado en informática.', 'Grave'),
(71, 'DATOS', 'DATOS', 5, 'Información concreta.', 'Se procesan.', 'Grave'),
(72, 'LISTA', 'LISTA', 5, 'Conjunto ordenado.', 'Puede ser de compras.', 'Grave'),
(73, 'NOTAS', 'NOTAS', 5, 'Apuntes escritos o musicales.', 'Sirven para estudiar.', 'Grave'),
(74, 'PIANO', 'PIANO', 5, 'Instrumento musical.', 'Tiene teclas blancas y negras.', 'Grave'),
(75, 'VIOLA', 'VIOLA', 5, 'Instrumento musical de cuerda.', 'Similar al violín.', 'Grave'),
(76, 'GUITA', 'GUITA', 5, 'Instrumento musical o dinero (arg).', 'Lo tocan músicos.', 'Grave'),
(77, 'BAJOS', 'BAJOS', 5, 'Instrumentos graves.', 'Contrario a altos.', 'Grave'),
(78, 'HUMOR', 'HUMOR', 5, 'Estado de ánimo.', 'Puede ser bueno o malo.', 'Aguda'),
(79, 'RISAS', 'RISAS', 5, 'Expresión de alegría.', 'Se hace al estar feliz.', 'Grave'),
(80, 'CANTO', 'CANTO', 5, 'Acción de cantar.', 'Lo hace un coro.', 'Grave'),
(81, 'VOZES', 'VOZES', 5, 'Variante de voces.', 'Lo que se escucha.', 'Grave'),
(82, 'SONAR', 'SONAR', 5, 'Producir un sonido.', 'Lo hace un timbre.', 'Aguda'),
(83, 'RUIDO', 'RUIDO', 5, 'Sonido desagradable.', 'Opuesto al silencio.', 'Grave'),
(84, 'SILEN', 'SILEN', 5, 'Truncado de silencio.', 'Lo contrario al ruido.', 'Grave'),
(85, 'PAUSA', 'PAUSA', 5, 'Interrupción breve.', 'Momento de descanso.', 'Grave'),
(86, 'FINES', 'FINES', 5, 'Finalidades u objetivos.', 'También días de descanso.', 'Grave'),
(87, 'JUEGO', 'JUEGO', 5, 'Actividad recreativa.', 'Lo que estás jugando.', 'Grave');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_palabra` int(11) DEFAULT NULL,
  `fecha_partida` timestamp NOT NULL DEFAULT current_timestamp(),
  `adivinada` tinyint(1) DEFAULT 0,
  `intentos` int(11) DEFAULT NULL,
  `pistas_usadas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pistas`
--

CREATE TABLE `pistas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion_corta` text DEFAULT NULL,
  `icono_simbolo` varchar(255) DEFAULT NULL,
  `columna_referencia` varchar(255) NOT NULL,
  `valor_monedas` int(11) NOT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `monedas` int(11) DEFAULT 0,
  `puntaje` bigint(20) DEFAULT 0,
  `pfp` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `racha` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `palabras`
--
ALTER TABLE `palabras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_palabra` (`id_palabra`);

--
-- Indices de la tabla `pistas`
--
ALTER TABLE `pistas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `palabras`
--
ALTER TABLE `palabras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pistas`
--
ALTER TABLE `pistas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD CONSTRAINT `partidas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `partidas_ibfk_2` FOREIGN KEY (`id_palabra`) REFERENCES `palabras` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
