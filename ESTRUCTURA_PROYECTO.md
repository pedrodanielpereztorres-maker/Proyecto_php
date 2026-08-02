# Especificación General del Sistema de Horarios - IUTEPI

Documento maestro con la hoja de ruta y especificación funcional de las 5 Fases para la construcción del Sistema de Horarios.

---

## 📌 Fase 1: Configuración Global y Catálogos Base (Cimientos)
Tablas de mantenimiento para alimentar los selects del sistema:
- **Módulo de Parámetros de Jornada:** Duración (minutos) de bloques académicos, recesos y horas de inicio, separados por tipo de jornada (Semana vs. Sabatino).
- **Módulo de Catálogos Institucionales:**
  - Periodos Académicos (Ej. PR26-2).
  - Turnos (Matutino, Vespertino, Nocturno).
  - Departamentos (Áreas académicas).
  - Tipos de Espacio (Aula Regular, Laboratorio, Cancha).
- **Módulo de Catálogos Profesionales:**
  - Niveles Académicos / Profesiones (TSU, Ingeniero, Licenciado).
  - Especialidades / Menciones (Informática, Administración, Análisis de Sistemas).
- **Módulo de Carreras:** Especialidades con su código (Ej. SIS para Sistemas).

---

## 📌 Fase 2: Gestión de Infraestructura (Espacios Físicos)
Registro de lugares de clase:
- **Módulo de Espacios:** Nombre, `tipo_espacio_id`, `capacidad_maxima`, `estatus_operativo`.

---

## 📌 Fase 3: Gestión Académica (Pénsum y Estudiantes)
Estructura educativa y grupos:
- **Módulo de Materias (Pénsum):** Código (Ej. AS515), nombre, horas semanales, semestre (1 al 6), `tipo_espacio_id` obligatorio. Integración de importador masivo CSV en Filament.
- **Módulo de Secciones:** Periodo, Turno, Carrera, Semestre, Nomenclatura (Ej. SM5), `cantidad_alumnos` (clave para capacidad del espacio).

---

## 📌 Fase 4: Gestión de Talento Humano (Docentes)
Directorio del personal y disponibilidad:
- **Módulo de Profesores:** Código interno, cédula, nombres, dirección, teléfono, correo, `nivel_academico_id`, `especialidad_id`, foto de perfil (Avatar).
- **Submódulo de Restricciones Docentes:** Días y horas específicas sin disponibilidad para impartir clase.

---

## 📌 Fase 5: Motor Transaccional de Horarios (El Núcleo)
Núcleo del sistema:
- **Generador Dinámico de Bloques:** Creación automática de franjas de tiempo en la base de datos según `JornadaParametro` (Semana vs. Sabatino).
- **Panel de Asignación Reactiva (Horario):**
  - **Recesos Flotantes:** Insertar pausas (20 o 30 min) desplazando el horario dinámicamente.
  - **Asignación Continua:** Previsualización en tiempo real del horario del docente para asignación consecutiva.
  - **Validación Inteligente y Flexible:** Filtro automático de aulas según `cantidad_alumnos` vs `capacidad_maxima` + `tipo_espacio_id`, con Toggle "Ignorar límite" y advertencias visuales.
