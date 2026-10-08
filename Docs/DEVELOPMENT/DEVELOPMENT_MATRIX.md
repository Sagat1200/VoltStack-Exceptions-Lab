# EXCEPTION-LAB · DEVELOPMENT MATRIX

## 1. Contexto

- Modulo documental: `vendor/voltstack/exception-lab`
- Modulo de implementacion: `vendor/voltstack/framework/src/Quantum/Exceptions`
- Namespace normativo objetivo: `VoltStack\Quantum\Exceptions`
- Namespace PHP actual del framework: `Quantum\Exceptions`

## 2. Trazabilidad bloque -> codigo -> norma

| Bloque | Estado | Objetivo | Ruta principal de codigo | Fuente normativa principal |
|---|---|---|---|---|
| EXC-001 | Completado | Estructura base del modulo, namespaces y bootstrap interno | `vendor/voltstack/framework/src/Quantum/Exceptions` | 01, 40 |
| EXC-002 | Completado | Contratos publicos y modelo de dominio | `.../Contracts`, `.../Model`, `.../Core` | 02, 06 |
| EXC-003 | Completado | Contexto, scope y occurrence registry | `.../Context`, `.../Core`, `.../Runtime` | 03, 05, 25 |
| EXC-004 | Completado | Normalizacion y captura de errores PHP/Throwable | `.../Normalization`, `.../Runtime` | 03, 07 |
| EXC-005 | Completado | Catalogo semantico y mapping engine | `.../Catalog`, `.../Mapping` | 06, 08, 09 |
| EXC-006 | Completado | ExceptionManager y pipeline determinista | `.../Core`, `.../Recovery`, `.../Privacy` | 03, 04 |
| EXC-007 | Completado | Reporting policy, dedup, reporters y observabilidad | `.../Reporting`, `.../Events`, `.../Core` | 10, 11, 19, 23 |
| EXC-008 | Completado | Negociacion y renderizado base HTTP/CLI | `.../Bridges/Http`, `.../Bridges/Console`, `.../Bridges` | 12, 13, 14, 18 |
| EXC-009 | Completado | Problem Details y errores API JSON | `.../Bridges/Http/Json`, `.../Bridges/Http`, `.../Bridges` | 15 |
| EXC-010 | Completado | Protocolo SPA nativo v1 | `.../Bridges/Spa`, `.../Bridges/Http`, `.../Bridges` | 16, 17 |
| EXC-011 | Completado | Runtime persistente, lifecycle y FrankenPHP | `.../Bridges/Runtime`, `src/Runtime` | 25, 26, 27 |
| EXC-012 | Pendiente | Configuracion, compilacion y gates | `.../Compilation`, `.../Tests` | 31, 36, 38, 40 |

## 3. Dependencias por integracion

| Integracion | Estrategia | Nota |
|---|---|---|
| HTTP/HTTPKernel | Bridge | El nucleo no emite bytes |
| CLI | Bridge | Exit codes y stderr sin contaminar el nucleo |
| SPA | Bridge/protocolo | Envelope versionado y validado |
| Jobs | Bridge | Recovery advice, no retry automatico del nucleo |
| Telemetry | Puerto/bridge | Observabilidad saneada |
| Event System | Puerto/bridge | Eventos observacionales, no mutadores |
| Runtime persistente | Bridge | Scope por operacion y reset controlado |
| Authentication/Authorization | Mapping e integracion tipada | Preservar DENY/CHALLENGE/FAILURE |
| Database | Bridge de efecto | Database mantiene autoridad transaccional |

## 4. Riesgos estructurales abiertos

| Riesgo | Impacto | Mitigacion documental |
|---|---|---|
| Contratos reales ausentes de ciertos modulos vecinos | Alto | Implementar puertos propuestos, no acoplamientos duros |
| Fuga entre scopes en runtime persistente | Alto | Prohibir estado scoped en singletons y certificar reset |
| Retry inseguro tras estado incierto | Alto | Mantener `automatic_replay=false` |
| Sobreexposicion de diagnostico | Alto | Redaccion temprana + proyeccion publica cerrada |
