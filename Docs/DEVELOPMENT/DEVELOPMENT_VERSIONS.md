# EXCEPTION-LAB · DEVELOPMENT VERSIONS

## Version documental operativa

| Version | Estado | Fecha | Descripcion |
|---|---|---|---|
| 0.1.0 | Inicial | 2026-10-06 | Apertura formal de `Docs/DEVELOPMENT` para `exception-lab`, fijando alcance, bloques, trazabilidad y ruta real de implementacion en `vendor/voltstack/framework/src/Quantum/Exceptions`. |
| 0.1.1 | Completada | 2026-10-06 | Cierre del bloque `EXC-001` con apertura de la estructura base del modulo en `vendor/voltstack/framework/src/Quantum/Exceptions` y registro de la condicion real del namespace `Quantum\\Exceptions` en el framework. |
| 0.2.0 | Completada | 2026-10-06 | Cierre del bloque `EXC-002` con introduccion de contratos publicos, DTOs inmutables, contextos base y pruebas iniciales del nuevo pipeline de excepciones. |
| 0.3.0 | Completada | 2026-10-06 | Cierre del bloque `EXC-003` con lifecycle base de scope, occurrence registry scoped, limites de profundidad/capacidad y pruebas unitarias de identidad y cierre. |
| 0.4.0 | Completada | 2026-10-06 | Cierre del bloque `EXC-004` con normalizacion acotada de `Throwable`, politica de captura PHP y pruebas de redaccion, truncamiento y clasificacion de severidades. |
| 0.5.0 | Completada | 2026-10-06 | Cierre del bloque `EXC-005` con catalogo semantico base, mapping determinista, enriquecimiento seguro de metadata y pruebas de precedencia/fallback. |
| 0.6.0 | Completada | 2026-10-07 | Cierre del bloque `EXC-006` con `ExceptionManager`, proyeccion publica, recovery basal, reporter pipeline y pruebas del flujo determinista estructurado. |
| 0.7.0 | Completada | 2026-10-07 | Cierre del bloque `EXC-007` con policy de reporting, receipts enriquecidos, reporters basales, bridge de telemetria, evento observacional y pruebas del pipeline. |
| 0.8.0 | Completada | 2026-10-07 | Cierre del bloque `EXC-008` con transport mappers HTTP/CLI, renderer basal negociado, status/exit codes seguros y pruebas de bridges del manager. |
| 0.9.0 | Completada | 2026-10-07 | Cierre del bloque `EXC-009` con `application/problem+json` como perfil API por defecto, renderer dedicado, extensiones allowlisted y pruebas del bridge HTTP. |
| 0.10.0 | Completada | 2026-10-07 | Cierre del bloque `EXC-010` con envelope SPA nativo v1, negociacion por version, fallback `406` a Problem Details y pruebas del bridge SPA. |
| 0.11.0 | Completada | 2026-10-07 | Cierre del bloque `EXC-011` con bridge runtime `begin/context/finalize/close`, integracion en `RequestRunner`, cierre idempotente y pruebas de aislamiento/reset en perfil persistente. |

## Politica de versionado documental

- Incremento PATCH: sincronizacion documental, correcciones, aclaraciones menores
- Incremento MINOR: nuevo bloque, expansion de matriz, nuevas reglas operativas
- Incremento MAJOR: cambio de estrategia, cambio de alcance o ruptura del plan ejecutivo

## Estado actual del programa

- Corpus normativo: disponible en `vendor/voltstack/exception-lab/Docs`
- Desarrollo operativo: iniciado en `vendor/voltstack/exception-lab/Docs/DEVELOPMENT`
- Implementacion framework: bloques `EXC-001` a `EXC-011` completados

## Proxima version esperada

- `0.12.0`: apertura documental del bloque `EXC-012`
