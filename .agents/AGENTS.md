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

---

## 🎨 Reglas Obligatorias de Diseño UI/UX (Filament)
De ahora en adelante, al generar o modificar un Recurso de Filament (Tablas y Formularios), el agente **debe** aplicar las siguientes reglas para mantener una estética premium y consistente:

### 1. Formularios (Forms)
- **Ancho Completo:** Toda `Section` principal debe ocupar el ancho de la pantalla agregando `->columnSpanFull()`.
- **Organización:** Utilizar `->columns(2)` u otra distribución dentro de las secciones para agrupar campos de manera inteligente.
- **Ayudas Visuales:** Todo campo debe llevar un `->placeholder('Ej:...')` y un `->helperText('...')` explicativo. 
- **Iconografía:** Usar `->prefixIcon('heroicon-m-...')` en los inputs relevantes, y `->icon()` en las secciones.
- **Selects Elegantes:** A todo componente `Select` se le debe agregar `->native(false)` para habilitar el buscador moderno estilizado por Filament.

### 2. Tablas (Tables)
- **Destacar lo Principal:** La primera columna de texto (el identificador o nombre principal) debe estar en negrita usando `->weight(\Filament\Support\Enums\FontWeight::Bold)` y llevar un ícono si aplica.
- **Etiquetas Visuales:** Los campos de categoría, relaciones cortas o estados deben mostrarse como etiquetas usando `->badge()` y `->color('info')` (o el color que corresponda).
- **Alineación:** Las columnas booleanas (como Activo) o de acciones cortas deben estar centradas `->alignCenter()`.
- **Traducción Perfecta:** Si el plural de la palabra en español genera un error de ruteo en Filament (ej. "Especialidads"), se debe declarar explícitamente `protected static ?string $slug = 'especialidades';` en el Resource.
