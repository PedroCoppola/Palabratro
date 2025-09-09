-- --------------------------------------------------------
-- -- Creación de la base de datos
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS Palabrato;
USE Palabrato;

-- --------------------------------------------------------
-- -- Estructura de la tabla `usuarios`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    monedas INT DEFAULT 0,
    puntaje BIGINT DEFAULT 0,
    pfp VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    racha BOOLEAN DEFAULT FALSE
);

-- --------------------------------------------------------
-- -- Estructura de la tabla `palabras`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS palabras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palabra VARCHAR(255) NOT NULL,
    palabra_sin_acento VARCHAR(255) NOT NULL,
    longitud INT NOT NULL,
    definicion TEXT,
    pista TEXT,
    tipo_acento VARCHAR(50)
);

-- --------------------------------------------------------
-- -- Estructura de la tabla `pistas`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS pistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion_corta TEXT,
    icono_simbolo VARCHAR(255),
    columna_referencia VARCHAR(255) NOT NULL,
    valor_monedas INT NOT NULL,
    activa BOOLEAN DEFAULT TRUE
);

-- --------------------------------------------------------
-- -- Estructura de la tabla `partidas`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_palabra INT,
    fecha_partida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    adivinada BOOLEAN DEFAULT FALSE,
    intentos INT,
    pistas_usadas VARCHAR(255),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (id_palabra) REFERENCES palabras(id) ON DELETE SET NULL
);
