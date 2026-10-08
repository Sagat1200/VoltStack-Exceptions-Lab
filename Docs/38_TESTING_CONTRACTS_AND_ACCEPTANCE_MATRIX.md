# 38 — Testing, contratos y matriz de aceptación

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Estrategia de verificación

La entrega actual ejecuta validación documental de archivos, enlaces, snippets JSON e integridad ZIP. Los casos siguientes son especificación de pruebas para la futura implementación; no son resultados ejecutados de PHP, runtimes, navegador ni brokers.

Unit tests cubren decisiones puras; contract tests fijan formas y semántica; integration tests usan bridges reales; fault injection provoca fallos de cada dependencia; end-to-end valida usuario, transporte y efectos persistidos. Golden fixtures públicas permiten comparar servidor y cliente SPA. Reloj, generador de IDs, random de jitter y reporters son inyectables para pruebas deterministas.

## Matriz contractual

| ID | Caso | Aserción decisiva | Nivel / capítulo |
|---|---|---|---|
| EX-01 | RuntimeException desconocida | internal.error, 500 y mensaje público seguro | unit, 06–09 |
| EX-02 | getCode=404 | no se convierte automáticamente a HTTP 404 | unit, 06/13 |
| EX-03 | reglas reordenadas | mismo mapper y mismo plan | unit, 08/36 |
| EX-04 | mapper que lanza | emergencia/fallback seguro; causa preservada internamente | fault, 03/08 |
| EX-05 | report y rethrow mismo objeto | una ocurrencia y un intento por reporter | contract, 10 |
| EX-06 | dos instancias equivalentes | dos ocurrencias | unit, 10 |
| EX-07 | reporter/redactor/renderer fallido | no recursión; sin raw data | fault, 07/11/22 |
| EX-08 | HTML inyectado y ANSI | escaping correcto; sin ejecución | security, 14/18 |
| EX-09 | secretos en todas las capas | cero coincidencias en outputs y buffers | security, 21/22 |
| EX-10 | Accept q=0 y formato incompatible | formato permitido o 406 controlado | contract, 12 |
| EX-11 | HEAD | status/headers válidos y cero body | HTTP, 12/13 |
| EX-12 | 401/405 | challenge/Allow válidos | HTTP, 13 |
| EX-13 | JSON grande o UTF-8 roto | objeto seguro parseable bajo límite | contract, 15 |
| EX-14 | SPA versión inválida | 406 y cliente seguro sin insertar HTML | e2e, 16 |
| EX-15 | navegación obsoleta y componente desmontado | no modifica UI actual | browser, 16/17 |
| EX-16 | commit con respuesta perdida | no duplicate mutation; reconciliación | DB+browser, 20/30 |
| EX-17 | boundary fallback falla | asciende una vez; no bucle | integration, 17 |
| EX-18 | stdout parcial y CLI error | stderr válido y exit no cero | process, 18 |
| EX-19 | listener de eventos lanza | salida original preservada | integration, 19 |
| EX-20 | Unknown/Partial | retry bloqueado hasta garantía/conciliación | contract, 20 |
| EX-21 | budget agotado/cancelación | no nuevo intento | clock/fault, 20/24 |
| EX-22 | ACK perdido | redelivery sin duplicar efecto con idempotencia | broker, 24 |
| EX-23 | 10.000 scopes A/B | cero identidad/locale/errores cruzados | runtime, 25/26 |
| EX-24 | coroutines entrelazadas | contexto y depth guard independientes | runtime, 27 |
| EX-25 | reset que falla | worker no acepta siguiente request | runtime, 25/26 |
| EX-26 | headers/body ya enviados | no segunda respuesta; cierre de stream | runtime, 12/28 |
| EX-27 | DENY/CHALLENGE/FAILURE | salidas distintas y nunca GRANT por fallo | auth, 29 |
| EX-28 | rollback/commit incierto | effect correcto y conexión retirada | real DB, 30 |
| EX-29 | bootstrap sin Container | error mínimo y not-ready | process, 32 |
| EX-30 | debug=true production | configuración rechazada | config, 31 |
| EX-31 | plan corrupto o incompatible | arranque falla; no mezcla de planes | deployment, 36 |
| EX-32 | sampling y cardinalidad | métricas no sesgadas y series acotadas | telemetry, 23 |
| EX-33 | plugin violando presupuesto | rechazo de certificación/aislamiento | contract, 37 |
| EX-34 | fatal/OOM/proceso terminado | supervisor repone; no garantía falsa de reporte | isolated process, 07/26 |

## Doubles y límites

ExceptionManagerFake registra llamadas sin reemplazar las pruebas del manager real. ReportRecorder guarda DTOs y receipts saneados; EventRecorder registra orden; FrozenClock y ScriptedRandom verifican backoff. FakeTransport rastrea committed y rechaza envío doble. MemoryQueue no demuestra ACK/redelivery y FakeDatabase no demuestra commit/rollback del motor.

Golden fixtures deben usar IDs y timestamps fijos, español e inglés de fallback, no datos reales. Property tests generan cadenas de causas y headers; fuzzing comprueba límites de parser SPA/Accept. Mutations de seguridad intentan quitar redacción, invertir DENY y permitir retries Unknown: las pruebas deben fallar.

## Gates de release

G1 contratos y catálogo; G2 privacidad y seguridad negativa; G3 HTTP/CLI/SPA con fixtures compartidas; G4 efectos e idempotencia con recursos reales; G5 aislamiento FrankenPHP; G6 performance bajo presupuesto declarado; G7 fallos de reporting/rendering/bootstrap y runbooks. RoadRunner/OpenSwoole tienen gates separados antes de anunciar soporte. No aprobar un gate por tener documentación: se exige evidencia reproducible de ejecución.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](37_EXTENSIBILITY_VERSIONING_AND_INTEROPERABILITY.md) · [Siguiente](39_REFERENCE_SCENARIOS_DECISIONS_AND_SOURCES.md)
