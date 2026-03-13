-- BASE DE DATOS IGUALDAD
CREATE DATABASE IF NOT EXISTS igualdad;
USE igualdad;
-- --------------------------------------------------------

--
-- Esctrutura para la tabla ROL
--

CREATE TABLE `rol`(
 `id`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
 `nombre`VARCHAR(100) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estrucutra para la tabla USUARIO
-- 

CREATE TABLE `usuario`(
 `id_usuario`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
 `nombre_usuario`VARCHAR(255) UNIQUE NOT NULL,
 `apellidos`VARCHAR(255) DEFAULT NULL,
 `email`VARCHAR(255) UNIQUE NOT NULL,
 `telefono`VARCHAR(20)  DEFAULT NULL,
 `direccion`VARCHAR(255) DEFAULT NULL,
 `localidad`VARCHAR(20) DEFAULT NULL,
 `password`VARCHAR(255) NOT NULL,
 `rol_id`INT NOT NULL,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES rol(id) ON DELETE RESTRICT,
INDEX idx_usuario_rol (rol_id)
)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla CLIENTE(EMPRESA)
--

CREATE TABLE `cliente`(
`id_cliente`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`razon_social`VARCHAR(255) UNIQUE NOT NULL,
`nif`VARCHAR(255) DEFAULT NULL,
`sector`VARCHAR(255) DEFAULT NULL,
`domicilio_social`VARCHAR(255) DEFAULT NULL,
`convenio`VARCHAR(255) DEFAULT NULL,
`telefono`VARCHAR(20) DEFAULT NULL,
`responsble`VARCHAR(255) DEFAULT NULL,
`cargo`VARCHAR(150) DEFAULT NULL,
`contacto`VARCHAR(255) DEFAULT NULL,
`email`VARCHAR(255) DEFAULT NULL,
`ano_constitucional`VARCHAR(255) DEFAULT NULL,
`inicio_medida`DATE NOT NULL,
`fin_medida`DATE NOT NULL,
`inicio_contratacion`DATE NOT NULL,
`fin_contratacion`DATE NOT NULL,
`id_usuario`INT NOT NULL,
 CONSTRAINT fk_usuario_cliente FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE RESTRICT,
 INDEX idx_cliente_usuario (id_usuario),
 INDEX idx_cliente_nif (nif))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla AREA DEL PLAN 
--
CREATE TABLE `area_plan`(
`id_plan`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`nombre`VARCHAR(255) NOT NULL)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla medida
--
CREATE TABLE `medida`(
`id_medida`INT AUTO_INCREMENT PRIMARY KEY,
`id_plan`INT NOT NULL,
`descripcion`TEXT NOT NULL ,
`indicador`VARCHAR(255) DEFAULT NULL, 
CONSTRAINT fk_medida_area FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan),
INDEX idx_medida_plan (id_plan)
)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla PLAN_CLIENTE
--
-- --------------------------------------------------------

CREATE TABLE `plan_cliente`(
`id_plan_cliente`INT AUTO_INCREMENT PRIMARY KEY,
`id_cliente`INT NOT NULL,
`id_plan`INT NOT NULL,
  CONSTRAINT fk_plan_cliente_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
  CONSTRAINT fk_plan_cliente_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan) ON DELETE RESTRICT,
INDEX idx_plan_cliente_cliente (id_cliente),
INDEX idx_plan_cliente_plan (id_plan)
)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla CLIENTE_MEDIDAD para que seleccione medidas de areas que ya selecciono.
--
-- --------------------------------------------------------
CREATE TABLE `cliente_medida` (
  `id_cliente_medida` INT AUTO_INCREMENT PRIMARY KEY,
  `id_plan_cliente` INT NOT NULL,   -- referencia al área seleccionada (cliente + área)
  `id_medida` INT NOT NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cliente_medida_plan_cliente FOREIGN KEY (id_plan_cliente) REFERENCES plan_cliente(id_plan_cliente) ON DELETE CASCADE,
  CONSTRAINT fk_cliente_medida_medida FOREIGN KEY (id_medida) REFERENCES medida(id_medida) ON DELETE RESTRICT,

  UNIQUE KEY uq_plancliente_medida (id_plan_cliente, id_medida),
  INDEX idx_cliente_medida_plancliente (id_plan_cliente),
  INDEX idx_cliente_medida_medida (id_medida)
) ENGINE=InnoDB;

--
-- Estructura para la tabla AREA (Ejercicio corresponsable de los derechos de la vida personal, familiar y laboral) 
--

CREATE TABLE `area_ejercicio`(
`id_ejercicio`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`medida`text,
`solicita_mujeres`INT NOT NULL DEFAULT 0,
`solicita_hombres`INT NOT NULL DEFAULT 0,
`concede_mujeres`INT NOT NULL DEFAULT 0,
`concede_hombres`INT NOT NULL DEFAULT 0,
`id_plan`INT NOT NULL,
CONSTRAINT fk_ejercicio_plan FOREIGN KEY (id_plan) REFERENCES area_cliente(id_plan) ON DELETE RESTRICT,
INDEX idx_area_ejercicio_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla AREA (Infrarrepresentación femenina ) 
--

CREATE TABLE `area_infra`(
`id_infra`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`plantilla_mujeres`INT NOT NULL DEFAULT 0 ,
`plantilla_hombres`INT NOT NULL DEFAULT 0, 
`id_plan`INT NOT NULL,
CONSTRAINT fk_infra_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_infra_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla AREA(Retribuciones y auditoría salarial ) 
--

CREATE TABLE `area_retribuciones`(
`id_retribuciones`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`permisos`VARCHAR(255),
`num_mujeres`INT NOT NULL DEFAULT 0,
`num_hombres`INT NOT NULL DEFAULT 0,
`id_plan`INT NOT NULL,
CONSTRAINT fk_retribuciones_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_retribuciones_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla AREA (Prevención del acoso sexual y por razón de sexo ) 
--

CREATE TABLE `area_acoso`(
`id_acoso`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`incidente`VARCHAR(255) NOT NULL,
`procedimiento`VARCHAR(255) NOT NULL,
`grado_incidencia`VARCHAR(255) NOT NULL,
`fecha_alta`DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
`acciones`VARCHAR(255) DEFAULT NULL,
`id_plan`INT NOT NULL,
CONSTRAINT fk_acoso_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_acoso_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla AREA (Violencia de género) 
--

CREATE TABLE `area_violencia`(
`id_violencia`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`acciones`VARCHAR(255) NOT NULL,
`observaciones`VARCHAR(255) NOT NULL,
`fecha_alta`DATE NOT NULL,
`solicita_mujeres`INT NOT NULL DEFAULT 0,
`id_plan`INT NOT NULL,
CONSTRAINT fk_violencia_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_violencia_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------
-- 
-- Tabla area_Responsable_de_igualdad
-- 

CREATE TABLE `area_responsable_igualdad` (
`id_responsable_de_igualdad`INT AUTO_INCREMENT PRIMARY KEY,
 `nombre`VARCHAR(100) NOT NULL,
 `email`VARCHAR(255) NOT NULL,
`id_plan_cliente`INT NOT NULL,
CONSTRAINT fk_responsable_plancliente FOREIGN KEY (id_plan_cliente) REFERENCES plan_cliente(id_plan_cliente) ON DELETE CASCADE,
INDEX idx_area_responsable_plancliente (id_plan_cliente)
)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla Proceso_de_seleccion_y_contratacion
--

CREATE TABLE `area_seleccion`(
`id_seleccion`INT AUTO_INCREMENT PRIMARY KEY,
`puesto_actual`VARCHAR(100) NOT NULL,
`fecha_alta`DATE NOT NULL,
`responsable`VARCHAR(100) NOT NULL,
`responsable_Int_Ext`VARCHAR(100) NOT NULL,
`crgo_responsable`ENUM('Masculino','Femenino') NOT NULL,
`gnro_seleccionado`ENUM('Masculino','Femenino') NOT NULL,
`c_mujeres`INT NOT NULL,
`c_hombres`INT NOT NULL,
`criterio_seleccion`VARCHAR(100) NOT NULL,
`id_plan`INT NOT NULL,
CONSTRAINT fk_seleccion_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_seleccion_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Tablas de area CALIFICAICON PROFESIONAL
-- Son todo graficos hay que mirarlo
--

CREATE TABLE `area_clasificacion`(
 `id_clasificacion`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
 `clasificacion`VARCHAR(100) NOT NULL,
 `promocion`VARCHAR(100) NOT NULL,
 `seleccion`VARCHAR(100) NOT NULL,
 `formacion`VARCHAR(100) NOT NULL,
 `id_plan`INT NOT NULL,
CONSTRAINT fk_clasificacion_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_clasificacion_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Tablas de area FORMACION
--

CREATE TABLE `area_formacion`(
`id_formacion`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
 `nombre`VARCHAR(100) NOT NULL,
 `fecha_inicio`DATE NOT NULL,
 `fecha_fin`DATE NOT NULL,
 `laboral`ENUM('Dentro','Fuera') NOT NULL,
 `modalidad`VARCHAR(100) NOT NULL,
 `voluntaria_obligatoria`ENUM('Voluntaria','Obligatoria') NOT NULL,
 `n_horas`INT NOT NULL,
 `n_hombres`INT NOT NULL,
`n_mujeres`INT NOT NULL,
 `informado_plantilla`VARCHAR(100) NOT NULL,
 `criterio_seleccion`VARCHAR(100) NOT NULL,
`id_plan`INT NOT NULL,
CONSTRAINT fk_formacion_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_formacion_plan (id_plan))ENGINE=InnoDB;


-- --------------------------------------------------------

--
-- Tablas de AREA PROMOCION Y ASCENSO PERSONAL
--

CREATE TABLE `area_promocion_ascenso_personal` (
     `id_promocion` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `puesto_origen` VARCHAR(100) NOT NULL,
    `puesto_destino` VARCHAR(100) NOT NULL,
    `aumento_economico` INT NOT NULL,
   `n_candidaturas` INT NOT NULL,
   `n_hombres` INT NOT NULL,
    `n_mujeres` INT NOT NULL,
    `responsable` VARCHAR(100) NOT NULL,
    `cargo_responsable` VARCHAR(100) NOT NULL,
    `genero_responsable` ENUM('Masculino','Femenino') NOT NULL,
    `genero_promocionado` ENUM('Masculino','Femenino') NOT NULL,
    `interna_externa` ENUM('Interna','Externa'),
    `contrato_inicial` VARCHAR(100) NOT NULL,
    `contrato_final` VARCHAR(100) NOT NULL,
    `tipo_promocion` VARCHAR(100) NOT NULL,
    `fecha_de_alta` DATE NOT NULL,
    `porcentaje_jornada` INT NOT NULL,
    `disfruta_conciliacion` BOOLEAN, 
    `criterio` VARCHAR(100) NOT NULL,
    `id_plan` INT NOT NULL,
CONSTRAINT fk_promocion_ascenso_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_promocion_ascenso_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Tablas de AREA CONDICIONES DE TRABAJO
--


CREATE TABLE `area_condiciones_trabajo`(
 `id_condiciones`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`n_conversiones_contrato`VARCHAR(100) NOT NULL,
`n_jornadas_ampliadas`VARCHAR(100) NOT NULL,
 `evaluacion_condiciones_trabajo`VARCHAR(100) NOT NULL,
 `muestreo`VARCHAR(100) NOT NULL,
 `contrataciones_realizadas`INT(11) NOT NULL,
 `id_plan`INT NOT NULL,
CONSTRAINT fk_condiciones_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_condiciones_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Tablas de AREA DE SALUD LABORAL
--

CREATE TABLE `area_salud`(
`id_salud`INT AUTO_INCREMENT PRIMARY KEY,
`nombre`VARCHAR(100) NOT NULL,
`procedencia`VARCHAR(255) NOT NULL,
`observaciones`VARCHAR(100),
 `id_plan`INT NOT NULL,
CONSTRAINT fk_salud_plan FOREIGN KEY (id_plan) REFERENCES area_plan(id_plan)ON DELETE RESTRICT,
INDEX idx_area_salud_plan (id_plan))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla CONTRATO
--
CREATE TABLE `contrato`(
`id_contrato`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`nombre`ENUM ('CONTRATO','PLAN') NOT NULL
)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla BAJAS
--
CREATE TABLE `bajas`(
`id_bajas`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`tipo`ENUM('TEMPORALES' , 'DEFINITIVAS')
)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla BAJA TEMPORALES
--
CREATE TABLE `baja_temporales`(
`id_temporales`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`motivo`VARCHAR(255) NOT NULL,
`num_mujeres`INT NOT NULL DEFAULT 0,
`num_hombres`INT NOT NULL DEFAULT 0, 
`id_bajas`INT NOT NULL,
CONSTRAINT fk_temporales_bajas FOREIGN KEY (id_bajas) REFERENCES bajas(id_bajas)ON DELETE CASCADE)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla BAJA DEFINITIVAS
--

CREATE TABLE `baja_definitivas`(
`id_definitivas`INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
`motivo`VARCHAR(255) NOT NULL,
`num_mujeres`INT NOT NULL DEFAULT 0,
`num_hombres`INT NOT NULL DEFAULT 0,
`id_bajas`INT NOT NULL,
CONSTRAINT fk_definitivas_bajas FOREIGN KEY (id_bajas) REFERENCES bajas(id_bajas)ON DELETE CASCADE)ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla EXCENDENCIAS
--

CREATE TABLE `area_excedencias`(
`id_excedencias`INT AUTO_INCREMENT PRIMARY KEY,
`excedencia`VARCHAR(100) NOT NULL,
`n_mujeres`INT DEFAULT 0,
`n_hombres`INT DEFAULT 0,
`id_cliente` INT NOT NULL,
  CONSTRAINT fk_excedencias_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    INDEX idx_excedencias_cliente (id_cliente))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla reducciones_jornada
--

CREATE TABLE `area_reducciones_jornada`(
`id_permisos`INT AUTO_INCREMENT PRIMARY KEY,
`reduccion_jornada`VARCHAR(100) NOT NULL,
`n_mujeres`INT DEFAULT 0,
`n_hombres`INT DEFAULT 0,
`id_cliente` INT NOT NULL,
  CONSTRAINT fk_reducciones_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
   INDEX idx_reducciones_cliente (id_cliente))ENGINE=InnoDB;
   -- --------------------------------------------------------

--
-- Estructura para la tabla adaptaciones_jornada
--

CREATE TABLE `area_adaptaciones_jornada`(
`id_permisos`INT AUTO_INCREMENT PRIMARY KEY,
`adaptacion`VARCHAR(100) NOT NULL,
`n_mujeres`INT DEFAULT 0,
`n_hombres`INT DEFAULT 0,
`id_cliente` INT NOT NULL,
  CONSTRAINT fk_adaptaciones_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
   INDEX idx_adaptaciones_cliente (id_cliente))ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Estructura para la tabla archivo_registro_retributivo
--

CREATE TABLE `archivos`(
  `id_archivo` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo` ENUM('IGUALDAD','SELECCION','SALUD','REGISTRO_RETRIBUTIVO','COMUNICACION','LGTBI') NOT NULL,
  `nombre_original` VARCHAR(255) NOT NULL,
  `nombre_guardado` VARCHAR(255) NOT NULL,
  `ruta_relativa` VARCHAR(255) NOT NULL,
  `tamano_bytes` BIGINT NOT NULL DEFAULT 0,
  `mime` VARCHAR(120) NULL,
  `sha256` CHAR(64) NULL,
  `subido_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_cliente_medida` INT NOT NULL,
  CONSTRAINT fk_archivo_cliente_medida FOREIGN KEY (id_cliente_medida) REFERENCES cliente_medida(id_cliente_medida) ON DELETE CASCADE,
  INDEX idx_archivos_cliente_medida (id_cliente_medida),
  INDEX idx_archivos_tipo (tipo),
  INDEX idx_archivos_cliente_medida_fecha (id_cliente_medida, subido_en)
) ENGINE=InnoDB;

