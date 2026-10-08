# EXCEPTION-LAB · DEVELOPMENT EXECUTIVE PLAN

## 1. Objetivo ejecutivo

Formalizar e implementar el subsistema normativo `VoltStack\Quantum\Exceptions` sobre el framework real en:

- `vendor/voltstack/framework/src/Quantum/Exceptions`

La carpeta `vendor/voltstack/exception-lab/Docs` conserva la autoridad normativa del diseño. La carpeta `vendor/voltstack/exception-lab/Docs/DEVELOPMENT` gobierna la ejecucion incremental del trabajo, su trazabilidad y sus criterios de cierre.

En el framework actual, la implementacion PHP sigue el namespace PSR-4 `Quantum\Exceptions` porque `vendor/voltstack/framework/composer.json` publica `Quantum\\` => `src/Quantum`. Esa diferencia debe tratarse como una condicion actual del repositorio y no como una reescritura silenciosa de la norma.

## 2. Principios de ejecucion

1. El desarrollo real vive en el framework, no en `exception-lab`.
2. Cada bloque de trabajo actualiza esta carpeta `DEVELOPMENT` antes y despues del cambio de codigo.
3. Ningun bloque se considera cerrado sin trazabilidad minima en:
   - `DEVELOPMENT_CHECKLIST.md`
   - `DEVELOPMENT_LOG.md`
   - `DEVELOPMENT_BACKLOG.md`
   - `DEVELOPMENT_MATRIX.md`
   - `DEVELOPMENT_VERSIONS.md`
4. La implementacion debe respetar como autoridad minima:
   - contratos de `02_PUBLIC_CONTRACTS_AND_DOMAIN_MODEL.md`
   - catalogo de `06_EXCEPTION_DESCRIPTOR_AND_ERROR_CATALOG.md`
   - protocolo SPA de `16_NATIVE_SPA_EXCEPTION_PROTOCOL.md`
   - configuracion de `31_CONFIGURATION_REFERENCE_AND_VALIDATION.md`
5. El sistema debe preservar las invariantes operativas del corpus normativo:
   - no doble emision
   - no retry automatico de mutaciones inciertas
   - no falsa promesa de rollback tras commit
   - aislamiento estricto por scope en runtimes persistentes

## 3. Alcance inicial de implementacion

El desarrollo se organizara en bloques incrementales `EXC-001+`.

### Fase A. Nucleo contractual y modelo

- contratos publicos
- objetos de valor inmutables
- `HandlingResult`
- `ExceptionDescriptor`
- `SemanticError`
- `PublicError`
- `RecoveryDecision`
- contexto y scope basicos

### Fase B. Pipeline y manager

- `ExceptionManager`
- normalizacion acotada
- mapping semantico
- politica de privacidad/proyeccion publica
- coordinacion de reporting
- resolucion de transporte
- emergencia minima

### Fase C. Integracion HTTP/JSON/HTML/SPA

- status semantics HTTP
- Problem Details JSON
- envelope SPA v1
- negotiation y renderer seguro
- proteccion ante transporte ya comprometido

### Fase D. Reporting, eventos y telemetria

- politica de deduplicacion local
- reporters tipados
- recibos de reporte
- eventos observacionales saneados
- integracion tipada de telemetria

### Fase E. Runtime persistente y certificacion

- lifecycle bridge
- aislamiento por scope
- soporte inicial FrankenPHP
- gates contractuales y pruebas de no fuga

## 4. Orden recomendado de bloques

1. `EXC-001` estructura base del modulo y namespaces
2. `EXC-002` contratos publicos y modelos cerrados
3. `EXC-003` contexto, scope y occurrence registry
4. `EXC-004` normalizacion y captura diagnostica acotada
5. `EXC-005` mapping engine y catalogo semantico base
6. `EXC-006` manager y pipeline determinista
7. `EXC-007` reporting policy, dedup y receipts
8. `EXC-008` transport mapping y renderers HTTP/CLI base
9. `EXC-009` Problem Details JSON
10. `EXC-010` SPA error protocol v1
11. `EXC-011` runtime bridge y aislamiento persistente
12. `EXC-012` pruebas contractuales y hardening operativo

## 5. Definicion de terminado

Un bloque se considera terminado cuando:

- su alcance esta reflejado en `DEVELOPMENT_CHECKLIST.md`
- existe registro resumido en `DEVELOPMENT_LOG.md`
- la trazabilidad normativa/codigo esta actualizada en `DEVELOPMENT_MATRIX.md`
- la version documental/progreso se refleja en `DEVELOPMENT_VERSIONS.md`
- backlog y siguientes pasos quedaron sincronizados
- las pruebas relevantes del bloque fueron ejecutadas o su ausencia quedo declarada

## 6. Restricciones duras

- Namespace normativo objetivo: `VoltStack\Quantum\Exceptions`
- Namespace PHP actual del framework: `Quantum\Exceptions`
- Ruta de implementacion: `vendor/voltstack/framework/src/Quantum/Exceptions`
- La documentacion normativa en `exception-lab/Docs` no se reemplaza con decisiones ad hoc de codigo
- Cualquier integracion no disponible hoy debe entrar como puerto/bridge, no como acoplamiento fuerte
- La seguridad y privacidad son obligatorias desde el primer bloque, no una fase posterior
