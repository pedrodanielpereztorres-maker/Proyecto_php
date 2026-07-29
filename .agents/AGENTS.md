# Reglas y Especificaciones del Proyecto de Horarios IUTEPI

## Contexto de Negocio
Sistema de Gestión de Horarios Académicos para IUTEPI estructurado en 5 Fases.

---

## Estructura de Fases del Sistema

### Fase 1: Configuración Global y Catálogos Base (Cimientos)
- **Parámetros de Jornada:** Duración (minutos) de bloques académicos, recesos, horas de inicio (Semana vs. Sabatino). Modelo: `JornadaParametro`.
- **Periodos Académicos:** Ej. PR26-2. Modelo: `PeriodoAcademico`.
- **Turnos:** Matutino, Vespertino, Nocturno. Modelo: `Turno`.
- **Departamentos:** Áreas académicas institucionales. Modelo: `Departamento`.
- **Tipos de Espacio:** Aula Regular, Laboratorio, Taller, Cancha. Modelo: `TipoEspacio`.
- **Niveles Académicos / Profesiones:** TSU, Ingeniero, Licenciado. Modelo: `NivelAcademico`.
- **Especialidades / Menciones:** Informática, Administración, Análisis de Sistemas. Modelo: `Especialidad`.
- **Carreras:** Código (ej: SIS) y nombre. Modelo: `Carrera`.

### Fase 2: Gestión de Infraestructura (Espacios Físicos)
- **Espacios:** Nombre, `tipo_espacio_id`, `capacidad_maxima`, `estatus_operativo`. Modelo: `Espacio` / `Aula`.

### Fase 3: Gestión Académica (Pénsum y Estudiantes)
- **Materias (Pénsum):** Código (ej: AS515), nombre, horas semanales, semestre (1 al 6), `tipo_espacio_id` obligatorio. Importador masivo CSV mediante Filament Actions. Modelo: `Materia`.
- **Secciones:** Periodo, Turno, Carrera, Semestre, Nomenclatura (ej: SM5), `cantidad_alumnos` (para cruzar con capacidad del aula). Modelo: `Seccion`.

### Fase 4: Gestión de Talento Humano (Docentes)
- **Profesores:** Código interno, cédula, nombres, dirección, teléfono, correo, `nivel_academico_id`, `especialidad_id`, foto de perfil (avatar). Modelo: `Profesor`.
- **Restricciones Docentes:** Matriz/días/horas bloqueadas sin disponibilidad. Modelo: `ProfesorRestriccion`.

### Fase 5: Motor Transaccional de Horarios (El Núcleo)
- **Generador Dinámico de Bloques:** Servicio/controlador backend basado en `JornadaParametro` (Semana vs. Sabatino).
- **Panel de Asignación Reactiva (Horario):**
  - Recesos flotantes (pausas de 20-30 min a demanda).
  - Asignación continua (previsualización de horas consecutivas del docente).
  - Validación inteligente y flexible: Filtro de aulas por `cantidad_alumnos` vs `capacidad_maxima` + compatibilidad `tipo_espacio_id`, con Toggle "Ignorar límite" y advertencia visual. Modelo: `Horario`.
