# EXCEPTION-LAB · DEVELOPMENT LOG

## 2026-10-06 · Apertura documental operativa

### Contexto

Se formaliza la carpeta `vendor/voltstack/exception-lab/Docs/DEVELOPMENT` para gobernar la ejecucion incremental del subsistema `VoltStack\Quantum\Exceptions`.

### Decisiones registradas

- La implementacion real se desarrollara en `vendor/voltstack/framework/src/Quantum/Exceptions`.
- `exception-lab/Docs` conserva la autoridad normativa del sistema.
- La carpeta `DEVELOPMENT` sera la autoridad operativa de trabajo incremental.
- Se adopta una secuencia inicial de bloques `EXC-001` a `EXC-012`.
- El cierre de cada ciclo exigira sincronizacion de checklist, log, backlog, matrix y versions.

### Estado

- Documentacion operativa base: creada
- Codigo del modulo: pendiente
- Primer bloque de implementacion: pendiente de apertura

### Observaciones

- El corpus normativo existente define claramente contratos, pipeline, seguridad, runtime persistente y gates de aceptacion.
- Las integraciones con sistemas no certificados aun deben entrar por puertos/bridges propuestos.

## 2026-10-06 · EXC-001 en progreso

### Objetivo del ciclo

Abrir la estructura base del modulo `VoltStack\Quantum\Exceptions` dentro de `vendor/voltstack/framework/src/Quantum/Exceptions`, alineada con la arquitectura modular definida por `exception-lab`.

### Alcance inicial

- crear layout basal del modulo
- fijar convenciones internas de carpetas y namespaces
- preparar puntos de entrada para `Contracts`, `Core`, `Model` y `Context`
- dejar el bloque listo para abrir `EXC-002`

### Estado

- bloque `EXC-001`: en progreso
- sincronizacion documental previa: completada

## 2026-10-06 · Cierre de EXC-001

### Resultado

Se abrio la estructura base del modulo en `vendor/voltstack/framework/src/Quantum/Exceptions` sin destruir la implementacion previa existente.

### Estructura creada

- `Core`
- `Context`
- `Model`
- `Catalog`
- `Mapping`
- `Reporting`
- `Recovery`
- `Privacy`
- `Events`
- `Compilation`
- `Bridges/Http`
- `Bridges/Console`
- `Bridges/Spa`
- `Bridges/Runtime`

### Decision relevante

Se detecto que el framework actual publica `Quantum\\` como namespace PSR-4 para `src/Quantum`. Por tanto, la implementacion PHP real del modulo queda abierta bajo `Quantum\Exceptions`, mientras que `VoltStack\Quantum\Exceptions` se conserva como referencia normativa del lab.

### Estado

- bloque `EXC-001`: completado
- codigo existente del modulo: preservado
- siguiente bloque recomendado: `EXC-002`

## 2026-10-06 · EXC-002 en progreso

### Objetivo del ciclo

Traducir el contrato base del documento 02 a interfaces y objetos de valor PHP reales dentro de `Quantum\Exceptions`, sin romper los contratos legacy que todavia usa la implementacion previa del modulo.

### Alcance inicial

- inspeccionar compatibilidad con contratos existentes
- introducir contratos publicos nuevos donde no exista colision
- introducir modelos inmutables base para el dominio del sistema
- dejar preparado el terreno para `EXC-003` y `EXC-006`

## 2026-10-06 · Cierre de EXC-002

### Resultado

Se introdujo el primer lote de contratos y DTOs del nuevo sistema de excepciones dentro de `Quantum\Exceptions`, manteniendo compatibilidad con la implementacion legacy ya presente en el framework.

### Contratos agregados

- `ExceptionManagerInterface`
- `ExceptionNormalizerInterface`
- `TransportMapperInterface`
- `ExceptionReporterInterface`
- `ExceptionRendererInterface`
- `RecoveryPolicyInterface`
- `SemanticExceptionMapperInterface`

### Modelos y contextos agregados

- `Context\ExceptionContext`
- `Context\TransportContext`
- `Context\RecoveryContext`
- `Core\HandlingResult`
- `Model\FailureSnapshot`
- `Model\SemanticError`
- `Model\ExceptionDescriptor`
- `Model\PublicError`
- `Model\TransportPlan`
- `Model\RenderedOutput`
- `Model\ReportRecord`
- `Model\ReportBudget`
- `Model\ReporterReceipt`
- `Model\ReportReceipt`
- `Model\RecoveryDecision`

### Enums agregados

- `Effect`
- `RetryAdvice`
- `SemanticCategory`
- `SemanticSeverity`
- `ReporterReceiptState`
- `HandlingResultKind`
- `RecoveryAction`

### Decision relevante

No se sustituyo el `Contracts\ExceptionMapperInterface` existente porque hoy forma parte del handler legacy y de contratos publicos ya consumidos por tests y mappers del framework. En su lugar se introdujo `SemanticExceptionMapperInterface` como contrato del nuevo pipeline, dejando la reconciliacion BC como trabajo posterior.

### Verificacion ejecutada

- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionContractsAndModelsTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionHandlerTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/PublicContractSignatureTest.php`

### Estado

- bloque `EXC-002`: completado
- suite inicial de contratos/modelos: abierta y pasando
- siguiente bloque recomendado: `EXC-003`

## 2026-10-06 · EXC-003 en progreso

### Objetivo del ciclo

Introducir el lifecycle base del scope de excepciones y un `occurrence registry` scoped para identidad, deduplicacion local y cierre seguro de referencias.

### Alcance inicial

- fijar un handle de scope explicito
- agregar registro scoped de ocurrencias y receipts
- introducir limites basales de profundidad y capacidad
- cubrir el comportamiento con pruebas unitarias

## 2026-10-06 · Cierre de EXC-003

### Resultado

Se implemento el lifecycle base del scope de excepciones y un `occurrence registry` scoped, manteniendo el nuevo pipeline desacoplado de la ruta legacy actual del handler.

### Componentes agregados

- `Runtime\ExceptionRuntimeLimits`
- `Runtime\ExceptionScope`
- `Runtime\ExceptionScopeFactory`
- `Runtime\ExceptionScopeLifecycleManager`
- `Runtime\OccurrenceRegistry`
- `Runtime\OccurrenceRecord`

### Ajustes realizados

- `Context\ExceptionContext` ahora puede llevar una referencia opcional al `ExceptionScope`
- el registro de ocurrencias usa `WeakMap` para conservar identidad por `Throwable` sin prolongar artificialmente la vida de los objetos
- el ledger retenido se satura con contador de descartes, pero mantiene identidad local para el mismo `Throwable` aun cuando el ledger ya no retenga nuevas entradas

### Politicas implementadas

- mismo `Throwable` dentro del mismo scope => misma ocurrencia
- deduplicacion local por reporter dentro de la misma ocurrencia
- limite de profundidad de manejo por scope
- invalidacion explicita del scope y cierre del registry al finalizar

### Verificacion ejecutada

- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionScopeTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionContractsAndModelsTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionHandlerTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/PublicContractSignatureTest.php`

### Estado

- bloque `EXC-003`: completado
- contexto/scope/registry: disponibles y cubiertos por pruebas
- siguiente bloque recomendado: `EXC-004`

## 2026-10-06 · EXC-004 en progreso

### Objetivo del ciclo

Introducir la normalizacion acotada de `Throwable` y la base de captura/clasificacion de errores PHP para el nuevo pipeline de excepciones.

### Alcance inicial

- normalizar diagnostico a `FailureSnapshot`
- limitar profundidad de causas, cantidad de frames y tamano de mensajes
- evitar serializacion del `Throwable`
- preparar base reutilizable para captura de errores PHP

## 2026-10-06 · Cierre de EXC-004

### Resultado

Se implemento la normalizacion acotada de `Throwable` y una politica base de captura/clasificacion de errores PHP para el nuevo pipeline, sin sustituir todavia el bootstrap global ni la ruta legacy del framework.

### Componentes agregados

- `Normalization\ThrowableNormalizer`
- `Runtime\PhpErrorCapturePolicy`

### Politicas implementadas

- snapshot sin referencias al `Throwable`
- captura de frames sin args ni objetos
- normalizacion de rutas y soporte para paths Windows/Linux
- redaccion basica de secretos obvios en mensajes
- limites de causas, frames, bytes de mensaje y bytes de snapshot
- respeto de `error_reporting()` para conversion y captura de errores PHP
- clasificacion separada para warnings, deprecations y severidades fatales

### Decision relevante

El bloque se cerro dejando la base reutilizable lista, pero sin conectar aun `set_error_handler` ni `register_shutdown_function` al nuevo pipeline. Esa integracion queda diferida para un bridge/runtime posterior, evitando romper el comportamiento actual del framework mientras el manager nuevo aun no esta operativo.

### Verificacion ejecutada

- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumThrowableNormalizerTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumPhpErrorCapturePolicyTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionScopeTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/PublicContractSignatureTest.php`

### Estado

- bloque `EXC-004`: completado
- normalizacion/captura base: disponible y probada
- siguiente bloque recomendado: `EXC-005`

## 2026-10-06 · EXC-005 en progreso

### Objetivo del ciclo

Introducir el catalogo semantico base y el motor de mapping determinista del nuevo pipeline, traduciendo el diagnostico normalizado a `SemanticError` sin depender todavia del handler legacy.

### Alcance inicial

- definir entradas de catalogo reutilizables
- resolver mapping por clase y jerarquia
- establecer fallback deterministico para errores no catalogados
- cubrir consistencia de `code`, `category`, `severity`, `effect` y `retryAdvice`

## 2026-10-06 · Cierre de EXC-005

### Resultado

Se implemento el catalogo semantico base y el motor de mapping determinista del nuevo pipeline, permitiendo traducir `FailureSnapshot` a `SemanticError` estable sin depender aun del mapper legacy de transporte.

### Componentes agregados

- `Catalog\SemanticCatalogEntry`
- `Catalog\SemanticErrorCatalog`
- `Mapping\MappingRule`
- `Mapping\MappingRuleMatch`
- `Mapping\DeterministicSemanticExceptionMapper`

### Cobertura funcional introducida

- catalogo basal con codigos publicos y metadata operativa
- mapping determinista por clase exacta, ancestro e interfaz
- desempate por prioridad, especificidad e `id`
- fallback a `internal.error` cuando no hay match o una regla falla
- plan estandar inicial para `Validation`, `Auth`, `View` y `Database`

### Ajustes relacionados

- `Normalization\ThrowableNormalizer` ahora enriquece metadata segura conocida para `ValidationException`, `AuthenticationException`, `ThrottleDeniedException` y `ExecutionException`, de modo que el mapper pueda extraer campos permitidos sin reintroducir el `Throwable` original.

### Decision relevante

El nuevo mapper semantico convive por ahora con los mappers legacy orientados a transporte. No se intento sustituir `AuthExceptionMapper` ni otros componentes equivalentes en este bloque; esa reconciliacion queda para la fase del manager/pipeline y bridges finales.

### Verificacion ejecutada

- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumSemanticMappingTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumThrowableNormalizerTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionContractsAndModelsTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/PublicContractSignatureTest.php`

### Estado

- bloque `EXC-005`: completado
- catalogo/mapping base: disponibles y probados
- siguiente bloque recomendado: `EXC-006`

## 2026-10-07 · EXC-006 en progreso

### Objetivo del ciclo

Introducir `ExceptionManager` y la orquestacion determinista base del nuevo pipeline para convertir un `Throwable` en descriptor, reporting, decision de recovery y resultado estructurado.

### Alcance inicial

- orquestar normalize -> map -> describe -> report -> decide
- proyectar un `PublicError` cerrado y reusable
- soportar resultado `rendered`, `propagate`, `jobDecision` y `abortTransport`
- mantener compatibilidad con la ruta legacy actual mientras el bridge final aun no existe

## 2026-10-07 · Cierre de EXC-006

### Resultado

Se implemento `ExceptionManager` y la orquestacion determinista base del nuevo pipeline, permitiendo convertir un `Throwable` en descriptor, reporting, decision de recovery y `HandlingResult` estructurado.

### Componentes agregados

- `Core\ExceptionManager`
- `Core\ExceptionDescriptorFactory`
- `Core\PublicErrorProjector`
- `Recovery\DeterministicRecoveryPolicy`
- `Reporting\ExceptionReporterPipeline`

### Cobertura funcional introducida

- pipeline determinista `normalize -> map -> describe -> report -> decide -> output`
- soporte para resultados `rendered`, `propagate`, `jobDecision`, `abortTransport` y `emergency`
- fingerprint basal para reporting
- deduplicacion de reporters por ocurrencia dentro del scope
- proyeccion de `PublicError` a partir del descriptor semantico
- decision basal de recovery usando `effect`, `retryAdvice`, `cancelled` e idempotencia

### Ajustes relacionados

- el manager consume metadata de transporte y de ejecucion desde `ExceptionContext`
- el reporter pipeline respeta el ledger retenido del scope y evita romperse cuando una ocurrencia ya no esta retenida por capacidad

### Decision relevante

El bloque se cerro sin integrar aun el manager nuevo con los bridges HTTP/CLI/SPA finales ni con la ruta global legacy del framework. La orquestacion ya existe y esta probada, pero su adopcion de extremo a extremo queda para los siguientes bloques de reporting/rendering/bridges.

### Verificacion ejecutada

- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionManagerTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumSemanticMappingTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionScopeTest.php`
- `vendor\\bin\\phpunit vendor/voltstack/framework/tests/Unit/PublicContractSignatureTest.php`

### Estado

- bloque `EXC-006`: completado
- manager/pipeline base: disponibles y probados
- siguiente bloque recomendado: `EXC-007`

## 2026-10-07 · EXC-007 en progreso

### Objetivo del ciclo

Introducir la politica basal de reporting, reporters reales y eventos observacionales del pipeline nuevo, manteniendo deduplicacion por ocurrencia y evitando que el reporting mutile el nucleo.

### Alcance inicial

- decidir si un descriptor debe omitirse, muestrearse o reportarse
- agregar reporters basales reutilizables
- enriquecer receipts con identidad y detalle operativo
- emitir eventos observacionales del pipeline de reporting

## 2026-10-07 · Cierre de EXC-007

### Resultado

Se cerro el bloque `EXC-007` introduciendo policy de reporting, receipts enriquecidos con razones de supresion, reporters basales reutilizables y bridges observacionales para telemetria y eventos, sin mutar el nucleo determinista del manager.

### Componentes agregados

- `Reporting\ExceptionReportingPolicy`
- `Reporting\ReportingPolicyDecision`
- `Reporting\StructuredErrorLogReporter`
- `Reporting\TelemetryExceptionReporter`
- `Reporting\EventDispatcherExceptionReporter`
- `Events\ExceptionReportedEvent`
- ajustes en `Reporting\ExceptionReporterPipeline`
- ajustes en `Model\ReporterReceipt`, `Model\ReportReceipt`, `Runtime\OccurrenceRecord`

### Cobertura funcional introducida

- policy basal por codigo, categoria, severidad minima y sampling determinista
- razones estructuradas de supresion en receipts: `policy_ignored`, `sampled_out`, `budget_exhausted`, `duplicate`
- captura controlada de fallo de reporter como `exceptions.reporter_failed` sin romper el resultado publico
- reporter estructurado para log local
- bridge de telemetria con `TelemetryManagerInterface`
- bridge observacional con `ControllerEventDispatcherInterface` y `ExceptionReportedEvent`
- preservacion de deduplicacion por ocurrencia dentro del scope, devolviendo razon `duplicate` al reingreso

### Ajustes relacionados

- `ExceptionManager` ahora propaga correlacion de transporte, `trace_id` y `tenant_id` al `ReportRecord`
- el pipeline normaliza el `reporterId`, calcula `durationMs` cuando el reporter no lo entrega y consolida razones de supresion en el `ReportReceipt`

### Verificacion ejecutada

- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionReportingTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionManagerTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit --filter QuantumException vendor/voltstack/framework/tests/Unit`

### Estado

- bloque `EXC-007`: completado
- reporting/observabilidad base: disponibles y probados
- siguiente bloque recomendado: `EXC-008`

## 2026-10-07 · EXC-008 en progreso

### Objetivo del ciclo

Introducir los transport mappers y renderers basales para HTTP y CLI, manteniendo el nucleo desacoplado y preparando el terreno para Problem Details, protocolo SPA e integracion final de bridges.

### Alcance inicial

- negociar representacion segura HTTP entre HTML y JSON basal
- definir semantica basal de status HTTP por codigo semantico
- definir codigos de salida CLI y salida humana/JSON en stderr
- conectar estas piezas al `ExceptionManager` mediante `TransportMapperInterface` y `ExceptionRendererInterface`

## 2026-10-07 · Cierre de EXC-008

### Resultado

Se cerro el bloque `EXC-008` introduciendo transport mappers basales para HTTP y CLI, junto con un renderer negociado capaz de producir HTML, JSON minimo y salida CLI humana/JSON sin acoplar el nucleo a emision directa de respuesta.

### Componentes agregados

- `Bridges\Http\HttpTransportMapper`
- `Bridges\Console\CliTransportMapper`
- `Bridges\TransportExceptionRenderer`
- `tests/Unit/QuantumExceptionTransportBridgeTest.php`

### Cobertura funcional introducida

- negociacion HTTP basal entre HTML y JSON usando `Accept`, q-values simples, especificidad y fallback por perfil
- semantica basal de status HTTP por codigo semantico (`422`, `401`, `403`, `404`, `409`, `429`, `503`, `500`)
- headers seguros por bridge (`Cache-Control: no-store`, `X-Content-Type-Options: nosniff`, `Content-Language` y `Retry-After` cuando aplica)
- render HTML minimo con escape de contenido dinamico
- render JSON minimo para API sin entrar aun en `Problem Details`
- salida CLI humana en stderr y modo JSON estructurado con `exit_code` estable
- integracion del `ExceptionManager` con estos bridges para superficies HTTP y CLI

### Ajustes relacionados

- el renderer unificado usa `TransportPlan::target` para seleccionar `http.html`, `http.json`, `cli.text` o `cli.json`
- el bloque deja pendiente la especializacion de `Problem Details` y el protocolo SPA, que se moveran a `EXC-009` y `EXC-010`

### Verificacion ejecutada

- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionTransportBridgeTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionManagerTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit --filter QuantumException vendor/voltstack/framework/tests/Unit`

### Estado

- bloque `EXC-008`: completado
- bridges HTTP/CLI base: disponibles y probados
- siguiente bloque recomendado: `EXC-009`

## 2026-10-07 · EXC-009 en progreso

### Objetivo del ciclo

Especializar la salida HTTP JSON del pipeline nuevo hacia `application/problem+json`, manteniendo compatibilidad con el renderer basal y sin mezclar todavia el protocolo SPA.

### Alcance inicial

- introducir Problem Details como perfil JSON API por defecto
- mapear `code`, `instance`, `status` y errores de validacion como extensiones allowlisted
- mantener un fallback JSON minimo cuando no se use el perfil Problem Details
- cubrir correspondencia status/body/media type con pruebas de contrato del bridge HTTP

## 2026-10-07 · Cierre de EXC-009

### Resultado

Se cerro el bloque `EXC-009` especializando la salida HTTP API hacia `application/problem+json`, manteniendo el fallback JSON basal para perfiles legacy y sin contaminar todavia el terreno del protocolo SPA.

### Componentes agregados

- `Bridges\Http\Json\ProblemDetailsExceptionRenderer`
- ajustes en `Bridges\Http\HttpTransportMapper`
- ajustes en `Bridges\TransportExceptionRenderer`
- ampliacion de `tests/Unit/QuantumExceptionTransportBridgeTest.php`

### Cobertura funcional introducida

- `application/problem+json` como representacion por defecto para el perfil `api`
- target dedicado `http.problem_json`
- payload allowlisted con `type`, `title`, `status`, `detail`, `instance`, `code` y `errors`
- `instance` estable usando `urn:voltstack:occurrence:<id>`
- `type` por defecto en `about:blank`, dejando lista la futura configuracion de URIs de problemas por aplicacion
- truncado de errores de validacion a un maximo controlado y fallback constante a `internal.error` si la codificacion JSON deja de ser segura
- preservacion del perfil legacy `http.json` para rutas que declaren `json`

### Ajustes relacionados

- `HttpTransportMapper` ahora distingue `api/problem_json` frente a `json/legacy_json`
- `TransportExceptionRenderer` delega la construccion de Problem Details a un renderer HTTP especializado

### Verificacion ejecutada

- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionTransportBridgeTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionManagerTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit --filter QuantumException vendor/voltstack/framework/tests/Unit`

### Estado

- bloque `EXC-009`: completado
- Problem Details HTTP: disponible y probado
- siguiente bloque recomendado: `EXC-010`

## 2026-10-07 · EXC-010 en progreso

### Objetivo del ciclo

Introducir el protocolo SPA nativo v1 sobre el pipeline nuevo, usando negociacion explicita por perfil/version y manteniendo separados los caminos de SPA, Problem Details y fallback HTTP basal.

### Alcance inicial

- anunciar y renderizar `application/vnd.voltstack.spa-error+json;v=1`
- dirigir envelopes al target autorizado de `page` o `component`
- decidir `action`, `retry` y `reconcile` desde el bridge sin acoplar el nucleo al arbol UI
- devolver `406` con Problem Details para versiones incompatibles del protocolo

## 2026-10-07 · Cierre de EXC-010

### Resultado

Se cerro el bloque `EXC-010` introduciendo el envelope SPA nativo v1, con negociacion por version/perfil, target de componente o pagina, acciones seguras y fallback `406` a Problem Details cuando el cliente solicita una version incompatible.

### Componentes agregados

- `Bridges\Spa\SpaExceptionRenderer`
- ajustes en `Model\TransportPlan`
- ajustes en `Core\ExceptionDescriptorFactory`
- ajustes en `Bridges\Http\HttpTransportMapper`
- ajustes en `Bridges\TransportExceptionRenderer`
- ampliacion de `tests/Unit/QuantumExceptionTransportBridgeTest.php`

### Cobertura funcional introducida

- media type `application/vnd.voltstack.spa-error+json;v=1`
- target dedicado `spa.error.v1`
- envelope con `protocol`, `version`, `kind`, `occurrence_id`, `status`, `error`, `target`, `action`, `effect`, `retry` y `reconcile`
- soporte para `request_id`, `operation_id` y `navigation_id` cuando el contexto los provee
- target `component` con `id` y `revision`, o `page` cuando no existe boundary de componente
- acciones seguras como `show_fields`, `authenticate`, `challenge`, `reconcile`, `retry_read`, `show_boundary` y `show_page`
- respuesta `406` en `application/problem+json` con `code=spa.protocol_unsupported` y `supported_versions=[1]`

### Ajustes relacionados

- `TransportPlan` ahora puede transportar metadata controlada para bridges sin exponer el descriptor completo al renderer
- `ExceptionDescriptorFactory` conserva en `contextSummary` solo las claves SPA necesarias para el bridge
- `HttpTransportMapper` no habilita SPA solo por `Accept`; requiere perfil de ruta `spa` y version soportada

### Verificacion ejecutada

- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionTransportBridgeTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionManagerTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit --filter QuantumException vendor/voltstack/framework/tests/Unit`

### Estado

- bloque `EXC-010`: completado
- protocolo SPA v1: disponible y probado
- siguiente bloque recomendado: `EXC-011`

## 2026-10-07 · EXC-011 en progreso

### Objetivo del ciclo

Introducir el bridge base de lifecycle de excepciones para runtimes persistentes, alineando apertura/finalizacion/cierre con el `RequestRunner`, el `ScopeManager`, el `ResetManager` y el perfil FrankenPHP worker ya presente en el framework.

### Alcance inicial

- definir el contrato runtime `begin/context/finalize/close`
- abrir un `ExceptionScope` por operacion persistente usando el `RuntimeContext` activo
- cerrar de forma idempotente y delegar el reset real al ciclo worker existente
- cubrir con pruebas la integracion del runner, el aislamiento y el fallback de cierre

## 2026-10-07 · Cierre de EXC-011

### Resultado

Se cerro el bloque `EXC-011` introduciendo el bridge runtime base del subsistema de excepciones y conectandolo al `RequestRunner` del perfil persistente. Cada operacion worker ahora abre `RuntimeContext`, abre `ExceptionScope`, finaliza el scope antes del cierre del request runtime y delega el reset real al ciclo worker existente mediante un `close()` idempotente.

### Componentes agregados

- `Quantum\Exceptions\Bridges\Runtime\ExceptionRuntimeBridge`
- `Quantum\Exceptions\Bridges\Runtime\RuntimeOperation`
- `Quantum\Exceptions\Bridges\Runtime\FinalizationOutcome`
- `Quantum\Exceptions\Bridges\Runtime\ManagedExceptionRuntimeBridge`
- `tests/Unit/QuantumExceptionRuntimeBridgeTest.php`

### Ajustes de integracion

- `Runtime\RequestRunner` ahora:
  - abre `RuntimeContext` usando `ScopeManager`
  - crea un `RuntimeOperation`
  - abre/finaliza/cierra el `ExceptionScope` por request
  - marca `Terminate` si falla el bridge o el reset
- `Platform\Application` publica `ExceptionScopeLifecycleManager` y `ExceptionRuntimeBridge` como bindings `worker`

### Cobertura funcional introducida

- contrato runtime `begin/context/finalize/close`
- `ExceptionContext` derivado del `RuntimeContext` activo
- cierre idempotente por `scopeId`
- metadatos runtime de excepciones visibles en `RuntimeContext` durante callbacks de scope
- integracion compatible con el ciclo FrankenPHP ya existente, sin duplicar loops ni resetters paralelos

### Limitacion conocida

- la finalizacion del bridge ocurre al terminar `RequestRunner`, antes de la confirmacion real de emision/stream del adapter. Esto deja abierto un backlog especifico para alinear el outcome final con la autoridad real de transporte persistente.

### Verificacion ejecutada

- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/QuantumExceptionRuntimeBridgeTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/BootstrapExecutionLifecycleTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/RuntimeManagerServerTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/RuntimeInMemoryResetTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit --filter QuantumException vendor/voltstack/framework/tests/Unit`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Feature/RuntimeScopeTest.php`
- `php vendor/voltstack/framework/vendor/bin/phpunit vendor/voltstack/framework/tests/Unit/RuntimeWorkerOwnedBindingsTest.php`

### Observacion

- `RuntimeScopeTest` sigue mostrando 2 deprecations preexistentes en `Quantum\Authorization\Authority\RequestScopedAuthorityMemoizationCache` y su contrato `AuthorityMemoizationCacheInterface`; no pertenecen al cambio de `EXC-011`.

### Estado

- bloque `EXC-011`: completado
- bridge runtime persistente: disponible y probado
- siguiente bloque recomendado: `EXC-012`
