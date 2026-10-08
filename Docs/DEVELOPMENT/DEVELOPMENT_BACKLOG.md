# EXCEPTION-LAB · DEVELOPMENT BACKLOG

## Backlog inmediato

| ID | Prioridad | Item | Estado |
|---|---|---|---|
| EXC-BL-001 | Alta | Abrir `EXC-001` con estructura base del modulo `Quantum\\Exceptions` en el framework | Completado |
| EXC-BL-002 | Alta | Definir layout inicial de `Contracts`, `Core`, `Model` y `Context` | Completado |
| EXC-BL-003 | Alta | Traducir contratos del doc 02 a interfaces PHP reales | Completado |
| EXC-BL-004 | Alta | Definir objetos de valor minimos para `HandlingResult`, `SemanticError`, `ExceptionDescriptor` y `PublicError` | Completado |
| EXC-BL-005 | Media | Diseñar occurrence registry scoped con presupuesto y deduplicacion local | Completado |
| EXC-BL-006 | Media | Diseñar limites y constantes basales alineados con doc 31 | Completado |
| EXC-BL-007 | Media | Preparar suite inicial de pruebas contractuales | Completado |
| EXC-BL-013 | Media | Reconciliar a futuro el legacy `ExceptionMapperInterface` con el mapper semantico del nuevo pipeline sin romper BC | Pendiente |
| EXC-BL-014 | Media | Conectar el nuevo `ExceptionScope` y `OccurrenceRegistry` al manager/pipeline futuro y a la ruta legacy del framework | Pendiente |
| EXC-BL-015 | Media | Implementar normalizacion acotada de `Throwable` con limites de causas, frames y bytes | Completado |
| EXC-BL-016 | Media | Introducir base de captura y clasificacion de errores PHP para el nuevo pipeline | Completado |
| EXC-BL-017 | Media | Integrar `PhpErrorCapturePolicy` y `ThrowableNormalizer` con el bridge de bootstrap/runtime sin romper la ruta global existente | Pendiente |
| EXC-BL-018 | Alta | Implementar catalogo semantico base con codigos publicos, severidad, retry y effect | Completado |
| EXC-BL-019 | Alta | Implementar mapping determinista por clase/jerarquia sin sustituir aun el mapper legacy | Completado |
| EXC-BL-020 | Media | Conectar el mapper semantico nuevo con el manager/pipeline y definir la convivencia final con mappers legacy de transporte | Completado |
| EXC-BL-021 | Alta | Implementar `ExceptionManager` con pipeline determinista de normalize -> map -> describe -> report -> decide -> output | Completado |
| EXC-BL-022 | Media | Introducir proyeccion publica y politica basal de recovery del nuevo pipeline | Completado |
| EXC-BL-023 | Media | Conectar `ExceptionManager` a bridges HTTP/CLI base y resolver convivencia inicial con transport mappers legacy | Completado |
| EXC-BL-024 | Alta | Implementar policy de reporting por codigo semantico y severidad | Completado |
| EXC-BL-025 | Alta | Implementar reporters reales basales y receipts enriquecidos | Completado |
| EXC-BL-026 | Media | Integrar eventos observacionales del pipeline de reporting sin mutar el nucleo | Completado |
| EXC-BL-027 | Media | Integrar el bridge SPA/protocolo nativo v1 sobre el pipeline nuevo sin mezclarlo con el fallback HTTP basal | Completado |

## Backlog de integracion posterior

| ID | Prioridad | Item | Estado |
|---|---|---|---|
| EXC-BL-008 | Media | Integracion HTTP Problem Details | Completado |
| EXC-BL-009 | Media | Integracion SPA error protocol v1 | Completado |
| EXC-BL-010 | Media | Integracion Runtime/FrankenPHP | Completado |
| EXC-BL-028 | Media | Conectar finalizacion del bridge de excepciones con confirmacion real de emision/stream del adapter persistente | Pendiente |
| EXC-BL-011 | Media | Integracion de delivery duradero/exportadores externos y emergency sink acotado | Pendiente |
| EXC-BL-012 | Baja | Herramientas DX y comandos de apoyo | Pendiente |

## Deudas y precauciones

- Confirmar convenciones internas del framework antes de fijar estructura fina de subnamespaces
- Evitar helpers ergonomicos antes de estabilizar contratos
- No asumir APIs existentes para HTTPKernel, RuntimeManagerServer o SPA si no estan certificadas en el repo
