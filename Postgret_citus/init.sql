-- =====================================================================
-- init_hcd.sql
-- Inicializar esquema HCD con catálogos, tablas clínicas,
-- Hoja de Vida (HC) con 57 campos, constraints, UUID y datos de ejemplo.
-- Compatible con PostgreSQL 14+ y Citus.
-- =====================================================================

-- ======================================================
-- 0. CONFIGURACIÓN INICIAL
-- ======================================================
CREATE SCHEMA IF NOT EXISTS hcd;
SET search_path TO hcd, public;

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
-- CREATE EXTENSION IF NOT EXISTS citus; -- Descomenta si usas Citus

-- ======================================================
-- 1. CATÁLOGOS DE REFERENCIA
-- ======================================================

-- 1.1 ISO-3166
DROP TABLE IF EXISTS catalogo_iso3166 CASCADE;
CREATE TABLE catalogo_iso3166 (
    code VARCHAR(3) PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);
INSERT INTO catalogo_iso3166 (code, name) VALUES
('CO','Colombia'),('US','Estados Unidos'),('BR','Brasil'),
('AR','Argentina'),('MX','México'),('CL','Chile'),
('PE','Perú'),('ES','España'),('FR','Francia'),('DE','Alemania');

-- 1.2 UCUM
DROP TABLE IF EXISTS catalogo_ucum CASCADE;
CREATE TABLE catalogo_ucum (
    code VARCHAR(20) PRIMARY KEY,
    description VARCHAR(120) NOT NULL
);
INSERT INTO catalogo_ucum (code, description) VALUES
('mg','milligram'),('g','gram'),('mL','milliliter'),('L','liter'),
('IU','international unit'),('h','hour'),('d','day'),('kg','kilogram'),
('mcg','microgram'),('tab','tablet');

-- 1.3 CIE-10
DROP TABLE IF EXISTS catalogo_cie10 CASCADE;
CREATE TABLE catalogo_cie10 (
    codigo VARCHAR(8) PRIMARY KEY,
    descripcion VARCHAR(255) NOT NULL
);
INSERT INTO catalogo_cie10 (codigo, descripcion) VALUES
('A00','Cólera'),('B20','Enfermedad por VIH'),('E11','Diabetes mellitus tipo 2'),
('I10','Hipertensión esencial (primaria)'),('J45','Asma'),('K21','ERGE'),
('N39','Otros trastornos del tracto urinario'),('O80','Parto único espontáneo'),
('S06','Traumatismo intracraneal'),('Z00','Examen general sin quejas');

-- 1.4 SNOMED
DROP TABLE IF EXISTS catalogo_snomed CASCADE;
CREATE TABLE catalogo_snomed (
    concept_id VARCHAR(20) PRIMARY KEY,
    term VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL
);
INSERT INTO catalogo_snomed (concept_id, term, category) VALUES
('26643006','Vía oral','route'),
('46713006','Vía intravenosa','route'),
('78421000','Vía intramuscular','route'),
('91936005','Alergia a penicilina','allergy'),
('235595009','Procedimiento de administración de medicamento','procedure');

-- ======================================================
-- 2. TABLAS CLÍNICAS
-- ======================================================

-- 2.1 USUARIO
DROP TABLE IF EXISTS usuario CASCADE;
CREATE TABLE usuario (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    documento_id BIGINT UNIQUE NOT NULL,
    pais_nacionalidad VARCHAR(100) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    edad INT,
    sexo VARCHAR(10) NOT NULL,
    genero VARCHAR(20),
    ocupacion VARCHAR(100),
    voluntad_anticipada BOOLEAN DEFAULT FALSE,
    categoria_discapacidad VARCHAR(50),
    pais_residencia VARCHAR(100),
    municipio_residencia VARCHAR(100),
    etnia VARCHAR(50),
    comunidad_etnica VARCHAR(100),
    zona_residencia VARCHAR(50),
    CONSTRAINT chk_usuario_sexo CHECK (sexo IN ('M','F','Intersex')),
    CONSTRAINT chk_usuario_zona CHECK (zona_residencia IS NULL OR zona_residencia IN ('Urbana','Rural','Dispersa'))
);

ALTER TABLE usuario
    ADD CONSTRAINT fk_usuario_pais_nacionalidad FOREIGN KEY (pais_nacionalidad) REFERENCES catalogo_iso3166(code) ON UPDATE CASCADE,
    ADD CONSTRAINT fk_usuario_pais_residencia FOREIGN KEY (pais_residencia) REFERENCES catalogo_iso3166(code) ON UPDATE CASCADE;

CREATE INDEX idx_usuario_nombre ON usuario (nombre_completo);

CREATE OR REPLACE FUNCTION calcular_edad_usuario()
RETURNS TRIGGER AS $$
BEGIN
    NEW.edad := EXTRACT(YEAR FROM age(current_date, NEW.fecha_nacimiento));
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_calcular_edad_usuario
BEFORE INSERT OR UPDATE OF fecha_nacimiento ON usuario
FOR EACH ROW
EXECUTE FUNCTION calcular_edad_usuario();

-- 2.2 PROFESIONAL DE SALUD
DROP TABLE IF EXISTS profesional_salud CASCADE;
CREATE TABLE profesional_salud (
    id_personal_salud UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nombre VARCHAR(255) NOT NULL,
    especialidad VARCHAR(100) NOT NULL
);
CREATE INDEX idx_prof_nombre ON profesional_salud (nombre);

-- 2.3 ATENCION
DROP TABLE IF EXISTS atencion CASCADE;
CREATE TABLE atencion (
    atencion_id SERIAL PRIMARY KEY,
    atencion_uuid UUID DEFAULT uuid_generate_v4(),
    documento_id BIGINT NOT NULL,
    entidad_salud VARCHAR(255) NOT NULL,
    fecha_ingreso TIMESTAMP NOT NULL,
    modalidad_entrega VARCHAR(50) NOT NULL,
    entorno_atencion VARCHAR(50) NOT NULL,
    via_ingreso VARCHAR(50),
    causa_atencion TEXT,
    fecha_triage TIMESTAMP,
    clasificacion_triage VARCHAR(10),
    CONSTRAINT fk_atencion_paciente FOREIGN KEY (documento_id) REFERENCES usuario(documento_id),
    CONSTRAINT chk_atencion_modalidad CHECK (modalidad_entrega IN ('Presencial','Telemedicina','Domiciliaria')),
    CONSTRAINT chk_atencion_entorno CHECK (entorno_atencion IN ('Urgencias','Hospitalización','Consulta','UCI','Procedimiento','Domiciliaria')),
    CONSTRAINT chk_atencion_triage CHECK (clasificacion_triage IS NULL OR clasificacion_triage IN ('1','2','3','4','5')),
    CONSTRAINT chk_atencion_fechas CHECK (fecha_triage IS NULL OR fecha_triage >= fecha_ingreso)
);
CREATE INDEX idx_atencion_doc ON atencion (documento_id);
CREATE INDEX idx_atencion_fecha ON atencion (fecha_ingreso);

-- 2.4 TECNOLOGIA EN SALUD
DROP TABLE IF EXISTS tecnologia_salud CASCADE;
CREATE TABLE tecnologia_salud (
    tecnologia_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    atencion_id INT NOT NULL,
    descripcion_medicamento VARCHAR(255) NOT NULL,
    dosis VARCHAR(50),
    via_administracion VARCHAR(50),
    frecuencia VARCHAR(50),
    dias_tratamiento INT,
    unidades_aplicadas INT DEFAULT 0,
    id_personal_salud UUID,
    finalidad_tecnologia VARCHAR(255),
    CONSTRAINT fk_tec_atencion FOREIGN KEY (atencion_id) REFERENCES atencion(atencion_id),
    CONSTRAINT fk_tec_route_snomed FOREIGN KEY (via_administracion) REFERENCES catalogo_snomed(concept_id),
    CONSTRAINT fk_tec_profesional FOREIGN KEY (id_personal_salud) REFERENCES profesional_salud(id_personal_salud),
    CONSTRAINT chk_tec_unidades CHECK (unidades_aplicadas >= 0),
    CONSTRAINT chk_tec_dias_trat CHECK (dias_tratamiento IS NULL OR (dias_tratamiento BETWEEN 0 AND 365))
);

-- 2.5 DIAGNOSTICO
DROP TABLE IF EXISTS diagnostico CASCADE;
CREATE TABLE diagnostico (
    diagnostico_id SERIAL PRIMARY KEY,
    diagnostico_uuid UUID DEFAULT uuid_generate_v4(),
    atencion_id INT NOT NULL,
    tipo_diagnostico_ingreso VARCHAR(50),
    diagnostico_ingreso VARCHAR(255) NOT NULL,
    tipo_diagnostico_egreso VARCHAR(50),
    diagnostico_egreso VARCHAR(255),
    diagnostico_rel1 VARCHAR(255),
    diagnostico_rel2 VARCHAR(255),
    diagnostico_rel3 VARCHAR(255),
    CONSTRAINT fk_dx_atencion FOREIGN KEY (atencion_id) REFERENCES atencion(atencion_id),
    CONSTRAINT fk_dx_ingreso_cie FOREIGN KEY (diagnostico_ingreso) REFERENCES catalogo_cie10(codigo),
    CONSTRAINT fk_dx_egreso_cie FOREIGN KEY (diagnostico_egreso) REFERENCES catalogo_cie10(codigo),
    CONSTRAINT fk_dx_rel1_cie FOREIGN KEY (diagnostico_rel1) REFERENCES catalogo_cie10(codigo),
    CONSTRAINT fk_dx_rel2_cie FOREIGN KEY (diagnostico_rel2) REFERENCES catalogo_cie10(codigo),
    CONSTRAINT fk_dx_rel3_cie FOREIGN KEY (diagnostico_rel3) REFERENCES catalogo_cie10(codigo),
    CONSTRAINT chk_dx_tipo_ingreso CHECK (tipo_diagnostico_ingreso IS NULL OR tipo_diagnostico_ingreso IN ('Presuntivo','Confirmado')),
    CONSTRAINT chk_dx_tipo_egreso CHECK (tipo_diagnostico_egreso IS NULL OR tipo_diagnostico_egreso IN ('Presuntivo','Confirmado','Definitivo'))
);

-- 2.6 EGRESO
DROP TABLE IF EXISTS egreso CASCADE;
CREATE TABLE egreso (
    egreso_id SERIAL PRIMARY KEY,
    egreso_uuid UUID DEFAULT uuid_generate_v4(),
    atencion_id INT NOT NULL,
    fecha_salida TIMESTAMP NOT NULL,
    condicion_salida VARCHAR(100) NOT NULL,
    diagnostico_muerte VARCHAR(255),
    codigo_prestador VARCHAR(20),
    tipo_incapacidad VARCHAR(100),
    dias_incapacidad INT,
    dias_lic_maternidad INT,
    alergias TEXT,
    antecedente_familiar TEXT,
    riesgos_ocupacionales TEXT,
    responsable_egreso VARCHAR(255),
    CONSTRAINT fk_egreso_atencion FOREIGN KEY (atencion_id) REFERENCES atencion(atencion_id),
    CONSTRAINT fk_egreso_diag_muerte FOREIGN KEY (diagnostico_muerte) REFERENCES catalogo_cie10(codigo),
    CONSTRAINT chk_egreso_condicion CHECK (condicion_salida IN ('Vivo','Mejorado','Remitido','Fallecido')),
    CONSTRAINT chk_egreso_dias_incap CHECK (dias_incapacidad IS NULL OR dias_incapacidad BETWEEN 0 AND 180),
    CONSTRAINT chk_egreso_dias_licmat CHECK (dias_lic_maternidad IS NULL OR dias_lic_maternidad BETWEEN 0 AND 126)
);

-- 2.7 HOJA DE VIDA CLÍNICA (HC) - 57 campos
DROP TABLE IF EXISTS hc CASCADE;
CREATE TABLE hc (
    hc_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    documento_id BIGINT NOT NULL,
    atencion_id INT,
    fecha_registro TIMESTAMP NOT NULL DEFAULT now(),
    motivo_consulta TEXT,
    sintomas_principales TEXT,
    antecedentes_personales TEXT,
    antecedentes_familiares TEXT,
    alergias TEXT,
    medicamentos_actuales TEXT,
    habitos VARCHAR(255),
    alcohol BOOLEAN,
    tabaco BOOLEAN,
    drogas BOOLEAN,
    cirugias_previas TEXT,
    transfusiones_previas TEXT,
    inmunizaciones TEXT,
    signos_vitales_peso NUMERIC(5,2),
    signos_vitales_talla NUMERIC(5,2),
    signos_vitales_imc NUMERIC(5,2),
    signos_vitales_fc INT,
    signos_vitales_fr INT,
    signos_vitales_ta VARCHAR(20),
    signos_vitales_temperatura NUMERIC(5,2),
    examen_fisico_general TEXT,
    examen_fisico_cabeza TEXT,
    examen_fisico_cuello TEXT,
    examen_fisico_torax TEXT,
    examen_fisico_abdomen TEXT,
    examen_fisico_extremidades TEXT,
    examen_fisico_piel TEXT,
    examen_fisico_neurologico TEXT,
    examen_fisico_otro TEXT,
    diagnostico_principal VARCHAR(255),
    diagnosticos_secundarios TEXT,
    plan_tratamiento TEXT,
    plan_examenes TEXT,
    plan_intervenciones TEXT,
    observaciones TEXT,
    profesional_responsable UUID,
    estado_paciente VARCHAR(50),
    condicion_salida VARCHAR(50),
    riesgo_caida BOOLEAN,
    riesgo_infeccion BOOLEAN,
    plan_seguimiento TEXT,
    evaluacion_funcional TEXT,
    evaluacion_psicologica TEXT,
    evaluacion_social TEXT,
    notas_enfermeria TEXT,
    notas_medicas TEXT,
    consentimiento_informado BOOLEAN,
    prioridad_atencion VARCHAR(20),
    riesgo_cardiaco BOOLEAN,
    riesgo_diabetes BOOLEAN,
    riesgo_renal BOOLEAN,
    alergias_medicamentos TEXT,
    reacciones_adversas TEXT,
    familiares_enfermedades TEXT,
    zona_residencia VARCHAR(50),
    etnia VARCHAR(50),
    municipio_residencia VARCHAR(100),
    pais_residencia VARCHAR(100),
    fecha_ultima_actualizacion TIMESTAMP DEFAULT now(),
    CONSTRAINT fk_hc_usuario FOREIGN KEY (documento_id) REFERENCES usuario(documento_id),
    CONSTRAINT fk_hc_profesional FOREIGN KEY (profesional_responsable) REFERENCES profesional_salud(id_personal_salud),
    CONSTRAINT fk_hc_atencion FOREIGN KEY (atencion_id) REFERENCES atencion(atencion_id)
);
CREATE INDEX idx_hc_documento ON hc(documento_id);
CREATE INDEX idx_hc_profesional ON hc(profesional_responsable);
CREATE INDEX idx_hc_fecha ON hc(fecha_registro);

-- ======================================================
-- LOGIN USUARIO
-- ======================================================
DROP TABLE IF EXISTS login_usuario CASCADE;

CREATE TABLE login_usuario (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    nombre_usuario VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    correo VARCHAR(150),
    rol_id VARCHAR(50) NOT NULL UNIQUE,
    documento_id BIGINT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    ultimo_login TIMESTAMP,
    creado_en TIMESTAMP DEFAULT now(),
    actualizado_en TIMESTAMP DEFAULT now(),
    
    -- Relaciones
    CONSTRAINT fk_login_usuario FOREIGN KEY (documento_id) REFERENCES usuario(documento_id) ON DELETE CASCADE
);

-- Índices opcionales para mejorar búsquedas
CREATE INDEX idx_login_nombre ON login_usuario(nombre_usuario);
CREATE INDEX idx_login_documento ON login_usuario(documento_id);

-- ======================================================
-- 3. DATOS DE EJEMPLO
-- ======================================================

-- Profesionales
INSERT INTO profesional_salud (id_personal_salud, nombre, especialidad) VALUES
(uuid_generate_v4(),'Dra. Andrea Borrero','Medicina Interna'),
(uuid_generate_v4(),'Dr. Luis Pineda','Urgencias'),
(uuid_generate_v4(),'Enf. Camila Hoyos','Enfermería');

-- Usuarios
INSERT INTO usuario (documento_id, pais_nacionalidad, nombre_completo, fecha_nacimiento, sexo, genero,
                     ocupacion, voluntad_anticipada, categoria_discapacidad, pais_residencia,
                     municipio_residencia, etnia, comunidad_etnica, zona_residencia)
VALUES
(1001001001,'CO','Carlos Arias','1990-05-12','M','Masculino','Ingeniero',FALSE,NULL,'CO','Sincelejo',NULL,NULL,'Urbana'),
(1002002002,'CO','María Gómez','1985-08-30','F','Femenino','Docente',TRUE,'Visual','CO','Medellín','Indígena','Zenú','Urbana');

-- Atenciones
INSERT INTO atencion (documento_id, entidad_salud, fecha_ingreso, modalidad_entrega, entorno_atencion,
                      via_ingreso, causa_atencion, fecha_triage, clasificacion_triage)
VALUES
(1001001001,'IPS Centro Salud Sucre','2025-10-01 08:30:00','Presencial','Urgencias','Espontáneo','Dolor torácico','2025-10-01 08:45:00','2'),
(1002002002,'Clínica UPB','2025-10-02 09:00:00','Telemedicina','Consulta',NULL,'Control de diabetes',NULL,NULL);

-- HC ejemplo
INSERT INTO hc (documento_id, atencion_id, motivo_consulta, sintomas_principales, antecedentes_personales,
                antecedentes_familiares, alergias, medicamentos_actuales, habitos)
VALUES
(1001001001, 1, 'Dolor torácico', 'Dolor opresivo', 'Hipertensión', 'Padre con HTA', 'Penicilina', 'Aspirina', 'Sedentario'),
(1002002002, 2, 'Control diabetes', 'Ninguno', 'Diabetes tipo 2', 'Madre con DM2', NULL, 'Metformina', 'Camina 30 min diarios');

-- Diagnósticos
INSERT INTO diagnostico (atencion_id, tipo_diagnostico_ingreso, diagnostico_ingreso, tipo_diagnostico_egreso, diagnostico_egreso)
VALUES
(1,'Presuntivo','I10','Definitivo','I10'),
(2,'Confirmado','E11','Confirmado','E11');

-- Egresos
INSERT INTO egreso (atencion_id, fecha_salida, condicion_salida, diagnostico_muerte, codigo_prestador,
                    tipo_incapacidad, dias_incapacidad, dias_lic_maternidad, alergias, antecedente_familiar,
                    riesgos_ocupacionales, responsable_egreso)
VALUES
(1,'2025-10-01 12:30:00','Mejorado',NULL,'12345','Temporal',5,NULL,'Alergia a penicilina','HTA familiar','Exposición a ruido','Dr. Luis Pineda'),
(2,'2025-10-02 09:40:00','Vivo',NULL,'98765',NULL,NULL,NULL,NULL,'Madre con DM2',NULL,'Dra. Andrea Borrero');

