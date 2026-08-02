# 11_EXCEPTION_AND_ERROR_HANDLING_SYSTEM.md

## Sistema de excepciones y manejo de errores de VoltStack

**Versión:** 1.0
**Estado:** Draft arquitectónico
**Módulo:** `VoltStack\Quantum\Exceptions`
**Ámbito:** Framework completo
**Integraciones principales:** Controllers, HttpKernel, Result Transformation, Response Transport, Logging, Observability, FrankenPHP

---

# 1. Introducción

El **Exception and Error Handling System** es el subsistema transversal responsable de recibir cualquier fallo ocurrido durante la ejecución de VoltStack y convertirlo en una decisión controlada del framework.

El sistema deberá distinguir entre:

* errores de programación;
* excepciones de infraestructura;
* excepciones HTTP;
* errores de validación;
* errores de autorización;
* errores de dominio;
* fallos temporales;
* errores recuperables;
* errores fatales;
* errores ocurridos durante streaming;
* errores ocurridos después de iniciar la emisión.

Su responsabilidad no se limita a mostrar una página de error.

También deberá:

* clasificar el fallo;
* resolver su significado;
* determinar si debe reportarse;
* determinar si puede recuperarse;
* sanitizar información sensible;
* construir una representación segura;
* seleccionar el formato de salida;
* generar una respuesta;
* registrar telemetría;
* proteger Workers persistentes;
* decidir si el proceso puede seguir atendiendo peticiones.

---

# 2. Objetivo principal

Transformar cualquier `Throwable` en una decisión consistente, observable y segura.

```text
Throwable
    │
    ▼
Throwable Resolver
    │
    ▼
Exception Definition
    │
    ▼
Classification
    │
    ▼
Handling Plan
    │
    ├── Report
    ├── Recover
    ├── Map
    ├── Render
    └── Terminate
    │
    ▼
Error Representation
    │
    ▼
Response / Abort / Rethrow
```

---

# 3. Posición dentro del framework

El sistema deberá poder interceptar fallos en cualquier etapa.

```text
Bootstrap
    │
Routing
    │
Controller Resolution
    │
Parameter Resolution
    │
Interceptors
    │
Invocation
    │
Result Transformation
    │
Response Transport
    │
Runtime Shutdown
```

Por tanto, no deberá pertenecer exclusivamente al módulo Controllers ni al módulo HTTP.

Será un sistema transversal del framework.

---

# 4. Principios arquitectónicos

El sistema seguirá estos principios:

* Toda excepción debe tener una clasificación.
* Toda clasificación debe producir una política de manejo.
* Reportar y renderizar son responsabilidades distintas.
* El modo Debug nunca debe alterar la lógica funcional.
* La salida pública debe estar sanitizada.
* Los errores de dominio no deben depender de HTTP.
* La respuesta HTTP será una adaptación posterior.
* Los fallos después de iniciar la emisión requieren tratamiento especial.
* Los Workers persistentes deben quedar en un estado válido.
* La infraestructura debe ser extensible mediante registries.
* En producción se preferirán planes compilados.
* Las excepciones originales deberán preservarse.

---

# 5. No responsabilidades

El sistema no deberá:

* corregir automáticamente errores de programación;
* reintentar operaciones sin una política explícita;
* ocultar fallos críticos;
* convertir toda excepción en `500`;
* escribir directamente una respuesta HTTP;
* decidir autorización;
* validar datos;
* implementar logging directamente;
* renderizar vistas sin delegar al Render Engine;
* construir manualmente el Volt Protocol;
* emitir contenido después de una respuesta parcialmente enviada.

---

# 6. Flujo general

```text
Throwable

↓

ExceptionHandlingContext

↓

ThrowableResolver

↓

ExceptionDefinition

↓

ExceptionClassifier

↓

ExceptionHandlingPlanResolver

↓

ExceptionHandlingPipeline

↓

Reporter / Recovery / Mapper / Renderer

↓

ExceptionHandlingResult
```

---

# 7. Componentes principales

```text
ExceptionHandler
ThrowableResolver
ExceptionDefinition
ExceptionClassifier
ExceptionCategory
ExceptionSeverity
ExceptionHandlingContext
ExceptionHandlingPlan
ExceptionHandlingPlanResolver
ExceptionHandlingPipeline
ExceptionMapperRegistry
ExceptionReporterRegistry
ExceptionRecoveryRegistry
ExceptionRendererRegistry
ExceptionSanitizer
ExceptionFingerprint
ExceptionRecorder
ExceptionCompiler
CompiledExceptionHandlingPlan
ExceptionCache
```

---

# 8. ExceptionHandler

Punto de entrada principal.

```php
interface ExceptionHandlerInterface
{
    public function handle(
        Throwable $throwable,
        ExceptionHandlingContext $context
    ): ExceptionHandlingResult;
}
```

Implementación oficial:

```php
final class ExceptionHandler
    implements ExceptionHandlerInterface
{
}
```

---

# 9. Responsabilidades del Handler

El Handler deberá:

1. validar que el fallo no haya sido manejado anteriormente;
2. crear o completar el contexto;
3. resolver la definición de excepción;
4. clasificar el fallo;
5. resolver el plan de manejo;
6. reportar cuando corresponda;
7. ejecutar recuperación cuando esté permitida;
8. mapear la excepción;
9. producir una representación;
10. devolver el resultado de manejo;
11. registrar métricas y tracing.

---

# 10. ExceptionHandlingContext

Representa el contexto de ejecución del fallo.

```php
final readonly class ExceptionHandlingContext
{
    public function __construct(
        public Throwable $throwable,
        public ExceptionOrigin $origin,
        public RuntimeContext $runtime,
        public ?RequestInterface $request,
        public ?ControllerExecution $controllerExecution,
        public ?TransportExecution $transportExecution,
        public MetadataBag $metadata,
        public ExceptionHandlingState $state,
        public bool $debug,
    ) {
    }
}
```

---

# 11. ExceptionOrigin

```php
enum ExceptionOrigin: string
{
    case Bootstrap = 'bootstrap';
    case Routing = 'routing';
    case ControllerResolution = 'controller_resolution';
    case ParameterResolution = 'parameter_resolution';
    case Interceptor = 'interceptor';
    case Invocation = 'invocation';
    case Transformation = 'transformation';
    case Rendering = 'rendering';
    case Hydration = 'hydration';
    case TransportPreparation = 'transport_preparation';
    case TransportEmission = 'transport_emission';
    case Streaming = 'streaming';
    case Shutdown = 'shutdown';
    case Worker = 'worker';
    case Unknown = 'unknown';
}
```

---

# 12. ExceptionHandlingState

Objeto mutable específico de la ejecución.

Contendrá:

* status;
* attempts;
* reportState;
* recoveryState;
* renderState;
* timestamps;
* fingerprint;
* mappedException;
* representation;
* response;
* terminalDecision.

---

# 13. Handling status

```php
enum ExceptionHandlingStatus: string
{
    case Pending = 'pending';
    case Resolving = 'resolving';
    case Classified = 'classified';
    case Reporting = 'reporting';
    case Recovering = 'recovering';
    case Mapping = 'mapping';
    case Rendering = 'rendering';
    case Handled = 'handled';
    case Rethrown = 'rethrown';
    case Aborted = 'aborted';
    case Failed = 'failed';
}
```

---

# 14. ThrowableResolver

Convierte un `Throwable` en una definición estructurada.

```php
interface ThrowableResolverInterface
{
    public function resolve(
        Throwable $throwable,
        ExceptionHandlingContext $context
    ): ExceptionDefinition;
}
```

---

# 15. ExceptionDefinition

Descripción inmutable del fallo.

```php
final readonly class ExceptionDefinition
{
    public function __construct(
        public string $class,
        public string $message,
        public int|string $code,
        public ExceptionCategory $category,
        public ExceptionSeverity $severity,
        public bool $reportable,
        public bool $recoverable,
        public bool $retryable,
        public bool $public,
        public array $tags,
        public array $metadata,
    ) {
    }
}
```

---

# 16. ExceptionCategory

```php
enum ExceptionCategory: string
{
    case Framework = 'framework';
    case Programming = 'programming';
    case Domain = 'domain';
    case Validation = 'validation';
    case Authentication = 'authentication';
    case Authorization = 'authorization';
    case NotFound = 'not_found';
    case Conflict = 'conflict';
    case RateLimit = 'rate_limit';
    case Infrastructure = 'infrastructure';
    case Database = 'database';
    case Network = 'network';
    case Timeout = 'timeout';
    case Concurrency = 'concurrency';
    case Serialization = 'serialization';
    case Rendering = 'rendering';
    case Transport = 'transport';
    case Security = 'security';
    case ExternalService = 'external_service';
    case Unknown = 'unknown';
}
```

---

# 17. ExceptionSeverity

```php
enum ExceptionSeverity: string
{
    case Debug = 'debug';
    case Info = 'info';
    case Notice = 'notice';
    case Warning = 'warning';
    case Error = 'error';
    case Critical = 'critical';
    case Alert = 'alert';
    case Emergency = 'emergency';
}
```

---

# 18. Clasificación jerárquica

La clasificación deberá considerar:

1. definición explícita;
2. interfaces implementadas;
3. metadata;
4. mapeos registrados;
5. clase base;
6. convenciones;
7. fallback.

---

# 19. Interfaces semánticas

VoltStack podrá definir contratos como:

```php
interface ReportableExceptionInterface
{
}

interface NonReportableExceptionInterface
{
}

interface PublicExceptionInterface
{
    public function publicMessage(): string;
}

interface RecoverableExceptionInterface
{
}

interface RetryableExceptionInterface
{
}

interface DomainExceptionInterface
{
}

interface HttpAwareExceptionInterface
{
}
```

Estos contratos no sustituirán al sistema de metadata, pero podrán actuar como señales explícitas.

---

# 20. Excepciones de dominio

Las excepciones de dominio no deberán conocer HTTP.

Ejemplo:

```php
final class InsufficientCreditException
    extends DomainException
{
}
```

Posteriormente un mapper podrá convertirla en:

* error JSON;
* error SPA;
* página HTML;
* status HTTP;
* mensaje CLI.

---

# 21. ExceptionClassifier

```php
interface ExceptionClassifierInterface
{
    public function classify(
        ExceptionDefinition $definition,
        ExceptionHandlingContext $context
    ): ClassifiedException;
}
```

---

# 22. ClassifiedException

Contendrá:

* categoría definitiva;
* severidad;
* tags;
* report policy;
* recovery policy;
* exposure policy;
* response hints;
* transport hints.

---

# 23. ExceptionMapper

Transforma una excepción en otra representación semántica.

```php
interface ExceptionMapperInterface
{
    public function supports(
        Throwable $throwable,
        ExceptionHandlingContext $context
    ): bool;

    public function map(
        Throwable $throwable,
        ExceptionHandlingContext $context
    ): MappedException;
}
```

---

# 24. MappedException

```php
final readonly class MappedException
{
    public function __construct(
        public string $type,
        public string $title,
        public string $detail,
        public int|string|null $code,
        public ?int $status,
        public array $errors,
        public array $metadata,
        public bool $public,
    ) {
    }
}
```

---

# 25. ExceptionMapperRegistry

```php
interface ExceptionMapperRegistryInterface
{
    public function register(
        ExceptionMapperInterface $mapper,
        int $priority = 0
    ): void;

    public function resolve(
        Throwable $throwable,
        ExceptionHandlingContext $context
    ): ExceptionMapperInterface;

    public function freeze(): void;
}
```

---

# 26. Mappers iniciales

```text
ValidationExceptionMapper
AuthenticationExceptionMapper
AuthorizationExceptionMapper
NotFoundExceptionMapper
ConflictExceptionMapper
RateLimitExceptionMapper
DomainExceptionMapper
DatabaseExceptionMapper
TimeoutExceptionMapper
TransportExceptionMapper
ThrowableFallbackMapper
```

---

# 27. Prioridades de mappers

```text
10000 Security
9500 Validation
9000 Authentication
8500 Authorization
8000 Domain-specific
7000 Infrastructure
1000 Generic framework
0 Fallback
```

---

# 28. ValidationExceptionMapper

Deberá producir:

* status sugerido;
* colección de errores;
* paths de campos;
* códigos de validación;
* metadata segura.

No deberá exponer valores sensibles automáticamente.

---

# 29. AuthenticationExceptionMapper

Podrá mapear a:

* `401`;
* redirect de login;
* mensaje SPA de autenticación requerida;
* error CLI.

La salida final dependerá del renderer y del transporte.

---

# 30. AuthorizationExceptionMapper

Podrá producir:

* `403`;
* representación segura;
* código de autorización;
* metadata de política cuando esté permitido.

No deberá revelar detalles internos de políticas en producción.

---

# 31. NotFoundExceptionMapper

Permitirá distinguir:

* ruta no encontrada;
* recurso no encontrado;
* archivo no encontrado;
* entidad no encontrada;
* componente no encontrado.

---

# 32. DatabaseExceptionMapper

Deberá sanitizar:

* queries;
* bindings;
* credenciales;
* nombres sensibles;
* rutas de conexión.

En modo Debug podrá mostrar información adicional controlada.

---

# 33. ThrowableFallbackMapper

Será el último recurso.

Mapeará cualquier excepción desconocida a una representación interna segura.

---

# 34. ExceptionHandlingPlan

Describe cómo manejar una excepción.

```php
final readonly class ExceptionHandlingPlan
{
    public function __construct(
        public bool $report,
        public bool $recover,
        public bool $map,
        public bool $render,
        public bool $rethrow,
        public bool $terminateWorker,
        public array $reporters,
        public array $recoveryStrategies,
        public string $mapper,
        public string $renderer,
        public array $sanitizers,
        public bool $compiled,
        public string $signature,
    ) {
    }
}
```

---

# 35. Plan Resolver

```php
interface ExceptionHandlingPlanResolverInterface
{
    public function resolve(
        ClassifiedException $exception,
        ExceptionHandlingContext $context
    ): ExceptionHandlingPlan;
}
```

---

# 36. Modos de resolución

```php
enum ExceptionHandlingMode: string
{
    case Auto = 'auto';
    case Dynamic = 'dynamic';
    case Compiled = 'compiled';
    case CompiledStrict = 'compiled_strict';
    case Debug = 'debug';
}
```

---

# 37. ExceptionHandlingPipeline

```text
Initialize
    │
Resolve
    │
Classify
    │
Sanitize
    │
Report
    │
Recover
    │
Map
    │
Render
    │
Finalize
```

---

# 38. Stages

```text
InitializeExceptionHandlingStage
ResolveThrowableStage
ClassifyExceptionStage
ResolveHandlingPlanStage
CreateFingerprintStage
SanitizeExceptionStage
ReportExceptionStage
RecoverExceptionStage
MapExceptionStage
RenderExceptionStage
NormalizeErrorResponseStage
FinalizeExceptionHandlingStage
CompleteExceptionHandlingStage
```

---

# 39. InitializeExceptionHandlingStage

Inicializa:

* estado;
* trace;
* timestamps;
* contexto;
* origen;
* runtime mode.

---

# 40. ResolveThrowableStage

Produce `ExceptionDefinition`.

---

# 41. ClassifyExceptionStage

Produce `ClassifiedException`.

---

# 42. ResolveHandlingPlanStage

Selecciona el plan compilado o dinámico.

---

# 43. CreateFingerprintStage

Genera una huella estable para agrupar errores similares.

---

# 44. ExceptionFingerprint

Podrá incluir:

* clase;
* top frames normalizados;
* categoría;
* origen;
* código;
* módulo.

No deberá incluir datos variables como IDs de usuario o payloads completos.

---

# 45. SanitizeExceptionStage

Elimina o enmascara información sensible.

---

# 46. ReportExceptionStage

Envía el fallo a los reporters configurados.

---

# 47. RecoverExceptionStage

Ejecuta estrategias de recuperación explícitas.

---

# 48. MapExceptionStage

Produce `MappedException`.

---

# 49. RenderExceptionStage

Produce una representación de error.

---

# 50. NormalizeErrorResponseStage

Delega al `ResultTransformationEngine` cuando la representación todavía no sea una `ResponseInterface`.

---

# 51. FinalizeExceptionHandlingStage

Finaliza:

* métricas;
* tracing;
* cleanup;
* estado del Worker;
* decisiones terminales.

---

# 52. ExceptionReporter

```php
interface ExceptionReporterInterface
{
    public function supports(
        ClassifiedException $exception,
        ExceptionHandlingContext $context
    ): bool;

    public function report(
        ClassifiedException $exception,
        ExceptionHandlingContext $context
    ): void;
}
```

---

# 53. Reporters iniciales

```text
LoggerExceptionReporter
TelemetryExceptionReporter
ErrorTrackingReporter
AuditExceptionReporter
SecurityIncidentReporter
NullExceptionReporter
```

---

# 54. Reporting desacoplado

Reportar no deberá cambiar la respuesta.

Un fallo del reporter tampoco deberá ocultar la excepción original.

---

# 55. Reporter failure policy

Cuando un reporter falle:

* se registrará el fallo secundario;
* se preservará la excepción original;
* se podrá intentar el siguiente reporter;
* nunca se reemplazará el error principal.

---

# 56. ReportPolicy

```php
final readonly class ReportPolicy
{
    public function __construct(
        public bool $enabled,
        public ExceptionSeverity $minimumSeverity,
        public float $sampleRate,
        public bool $deduplicate,
        public ?int $throttleWindowSeconds,
    ) {
    }
}
```

---

# 57. Deduplicación

Errores repetidos podrán agruparse mediante fingerprint.

Esto evita saturar:

* logs;
* sistemas de tracking;
* alertas;
* telemetría.

---

# 58. Sampling

Errores de alta frecuencia y baja severidad podrán muestrearse.

Los errores críticos no deberán muestrearse salvo configuración explícita.

---

# 59. ExceptionRecoveryStrategy

```php
interface ExceptionRecoveryStrategyInterface
{
    public function supports(
        ClassifiedException $exception,
        ExceptionHandlingContext $context
    ): bool;

    public function recover(
        ClassifiedException $exception,
        ExceptionHandlingContext $context
    ): RecoveryResult;
}
```

---

# 60. RecoveryResult

```php
final readonly class RecoveryResult
{
    public function __construct(
        public RecoveryStatus $status,
        public mixed $replacementResult = null,
        public ?Throwable $replacementThrowable = null,
        public array $metadata = [],
    ) {
    }
}
```

---

# 61. RecoveryStatus

```php
enum RecoveryStatus: string
{
    case NotAttempted = 'not_attempted';
    case Recovered = 'recovered';
    case RetryRequested = 'retry_requested';
    case FallbackProvided = 'fallback_provided';
    case Failed = 'failed';
    case Unsafe = 'unsafe';
}
```

---

# 62. Recuperación explícita

No toda excepción recuperable deberá recuperarse automáticamente.

La recuperación requerirá:

* estrategia registrada;
* política explícita;
* contexto seguro;
* límite de intentos;
* idempotencia cuando aplique.

---

# 63. Estrategias de recuperación iniciales

```text
FallbackValueRecovery
CachedValueRecovery
RetryRecovery
CircuitBreakerRecovery
GracefulDegradationRecovery
WorkerResetRecovery
TransportAbortRecovery
```

---

# 64. RetryRecovery

Solo podrá ejecutarse cuando:

* la operación sea retryable;
* exista permiso explícito;
* no se haya iniciado emisión irreversible;
* no se excedan intentos;
* la operación sea segura o idempotente.

---

# 65. GracefulDegradationRecovery

Podrá sustituir:

* servicio externo por cache;
* componente fallido por placeholder;
* feature opcional por contenido degradado;
* recurso remoto por fallback local.

---

# 66. ExceptionRenderer

Convierte `MappedException` en una representación consumible.

```php
interface ExceptionRendererInterface
{
    public function supports(
        MappedException $exception,
        ExceptionHandlingContext $context
    ): bool;

    public function render(
        MappedException $exception,
        ExceptionHandlingContext $context
    ): ErrorRepresentation;
}
```

---

# 67. ErrorRepresentation

```php
final readonly class ErrorRepresentation
{
    public function __construct(
        public ErrorRepresentationType $type,
        public mixed $payload,
        public ?int $status,
        public array $headers,
        public array $metadata,
    ) {
    }
}
```

---

# 68. ErrorRepresentationType

```php
enum ErrorRepresentationType: string
{
    case Html = 'html';
    case Json = 'json';
    case ProblemDetails = 'problem_details';
    case Spa = 'spa';
    case Component = 'component';
    case Cli = 'cli';
    case Text = 'text';
    case Abort = 'abort';
}
```

---

# 69. Renderers iniciales

```text
HtmlExceptionRenderer
JsonExceptionRenderer
ProblemDetailsRenderer
SpaExceptionRenderer
ComponentExceptionRenderer
CliExceptionRenderer
PlainTextExceptionRenderer
TransportAbortRenderer
```

---

# 70. Renderer resolution

El renderer se resolverá según:

1. representación requerida;
2. transporte;
3. negociación;
4. request;
5. metadata de ruta;
6. modo runtime;
7. fallback.

---

# 71. Problem Details

VoltStack soportará una representación inspirada en Problem Details.

Objeto:

```php
final readonly class ProblemDetails
{
    public function __construct(
        public string $type,
        public string $title,
        public int $status,
        public string $detail,
        public ?string $instance,
        public array $extensions = [],
    ) {
    }
}
```

---

# 72. Campos públicos

En producción:

* `title` deberá ser seguro;
* `detail` deberá estar sanitizado;
* `instance` no deberá exponer rutas internas;
* `extensions` no deberá incluir secretos.

---

# 73. Validation Problem Details

Las validaciones podrán incluir:

```json
{
  "type": "validation_error",
  "title": "The provided data is invalid.",
  "status": 422,
  "errors": {
    "email": [
      "The email field is invalid."
    ]
  }
}
```

---

# 74. HTML Error Renderer

En producción producirá una página segura y mínima.

En Debug podrá delegar al `DebugPageRenderer`.

---

# 75. DebugPageRenderer

Deberá mostrar:

* clase;
* mensaje;
* stack trace;
* código relevante;
* request;
* route;
* controller;
* metadata;
* logs cercanos;
* timeline;
* previous exceptions.

Solo estará disponible en entornos autorizados.

---

# 76. Seguridad de debug

El modo Debug no deberá habilitarse únicamente mediante un header público.

La autorización para mostrar información sensible deberá depender de:

* configuración;
* entorno;
* origen permitido;
* token seguro opcional;
* política interna.

---

# 77. SPA Exception Renderer

Para navegación SPA podrá producir:

* tipo de error;
* status;
* mensaje público;
* errores de validación;
* instrucciones de navegación;
* información de componente;
* fallback UI;
* correlation ID.

---

# 78. Component Exception Renderer

Permitirá aislar errores de componentes cuando la política lo permita.

En lugar de fallar toda la página:

```text
Component failure
    │
    ├── Error boundary disponible
    │       ▼
    │   Render fallback component
    │
    └── Sin boundary
            ▼
        Escalar error
```

---

# 79. Error boundaries

VoltStack podrá definir:

```php
interface ComponentErrorBoundaryInterface
{
    public function handleComponentError(
        Throwable $throwable,
        ComponentContext $context
    ): mixed;
}
```

---

# 80. CLI Exception Renderer

Deberá producir:

* mensaje;
* exit code;
* nivel;
* stack trace opcional;
* sugerencias;
* formato de terminal.

---

# 81. ExceptionSanitizer

```php
interface ExceptionSanitizerInterface
{
    public function sanitize(
        MappedException $exception,
        ExceptionHandlingContext $context
    ): MappedException;
}
```

---

# 82. Sanitizers iniciales

```text
MessageSanitizer
StackTraceSanitizer
PathSanitizer
DatabaseSanitizer
RequestDataSanitizer
HeaderSanitizer
CredentialSanitizer
FileSystemSanitizer
```

---

# 83. Datos sensibles

Se deberán ocultar por defecto:

* passwords;
* tokens;
* cookies;
* authorization headers;
* secrets;
* API keys;
* database DSNs;
* session IDs;
* private keys;
* formularios sensibles.

---

# 84. Sanitización de paths

En producción se podrán reemplazar paths absolutos por rutas relativas del proyecto.

---

# 85. Stack traces

El sistema mantendrá dos representaciones:

* stack trace interno completo;
* stack trace público sanitizado.

---

# 86. Previous exceptions

Las excepciones encadenadas se conservarán internamente.

La representación pública decidirá cuánto exponer.

---

# 87. Manejo de PHP errors

VoltStack deberá convertir errores PHP compatibles en `ErrorException`.

```php
set_error_handler(...);
```

La instalación concreta pertenecerá al Bootstrap Runtime.

---

# 88. Manejo de excepciones no capturadas

VoltStack registrará un handler global:

```php
set_exception_handler(...);
```

que delegará en `ExceptionHandlerInterface`.

---

# 89. Shutdown errors

El sistema deberá inspeccionar:

```php
error_get_last();
```

durante shutdown para detectar errores fatales no convertidos previamente.

---

# 90. FatalErrorDefinition

Deberá distinguir:

* parse errors;
* memory exhaustion;
* core errors;
* compile errors;
* unrecoverable engine errors.

---

# 91. Memory exhaustion

El framework podrá reservar un pequeño bloque de memoria de emergencia durante el bootstrap.

Ante agotamiento:

* liberará la reserva;
* registrará información mínima;
* evitará renderizado complejo;
* emitirá una respuesta mínima cuando sea posible;
* marcará el Worker como no reutilizable.

---

# 92. EmergencyExceptionRenderer

Renderer mínimo sin dependencias complejas.

Deberá funcionar cuando:

* el container esté incompleto;
* el Render Engine haya fallado;
* la memoria sea limitada;
* el sistema de metadata no esté disponible.

---

# 93. Bootstrap errors

Los errores durante bootstrap requieren un pipeline reducido.

```text
Bootstrap Throwable

↓

Emergency Throwable Resolver

↓

Emergency Reporter

↓

Emergency Renderer

↓

Minimal Transport
```

---

# 94. Errores durante Result Transformation

Si el propio `ResultTransformationEngine` falla al transformar una representación de error:

* se evitará recursión infinita;
* se activará el Emergency Renderer;
* se registrarán ambas excepciones;
* se preservará la original como principal.

---

# 95. Recursion guard

```php
final class ExceptionRecursionGuard
{
    public function enter(Throwable $throwable): void;

    public function leave(Throwable $throwable): void;

    public function depth(): int;
}
```

El sistema deberá imponer una profundidad máxima.

---

# 96. Error rendering failure

```text
Original exception
    │
Renderer failure
    │
    ▼
Emergency renderer
```

El error del renderer será secundario.

---

# 97. Errores durante preparación de transporte

Si aún no comenzó la emisión, podrá generarse una nueva respuesta de error.

---

# 98. Errores durante emisión

Una vez emitidos headers o bytes del body, el sistema no deberá intentar construir una respuesta alternativa completa.

---

# 99. TransportEmissionFailurePolicy

Podrá decidir:

* abortar stream;
* cerrar conexión;
* emitir trailer, si el protocolo lo permite;
* enviar evento terminal en SSE;
* registrar respuesta incompleta;
* reiniciar Worker.

---

# 100. Errores en streaming

Los streams requieren manejo específico.

```text
Stream producer
    │
    ├── Error before first chunk
    │       ▼
    │   Normal exception response
    │
    └── Error after chunks
            ▼
        Abort / terminal frame / close
```

---

# 101. SSE stream errors

Cuando corresponda, SSE podrá emitir un evento terminal:

```text
event: error
data: {...}
```

Solo si:

* la conexión continúa válida;
* la política lo permite;
* el payload es seguro;
* no se viola el protocolo.

---

# 102. WebSocket errors futuros

La futura integración deberá distinguir:

* error de handshake;
* error de mensaje;
* error de conexión;
* error de protocolo;
* error de aplicación.

---

# 103. Worker health

Después de manejar una excepción, el sistema deberá evaluar el estado del Worker.

---

# 104. WorkerDisposition

```php
enum WorkerDisposition: string
{
    case Reuse = 'reuse';
    case Reset = 'reset';
    case RestartRecommended = 'restart_recommended';
    case Terminate = 'terminate';
}
```

---

# 105. WorkerHealthEvaluator

```php
interface WorkerHealthEvaluatorInterface
{
    public function evaluate(
        ClassifiedException $exception,
        ExceptionHandlingContext $context
    ): WorkerDisposition;
}
```

---

# 106. Casos que pueden requerir terminar Worker

* memory exhaustion;
* estado global corrupto;
* error fatal del engine;
* fuga de recursos crítica;
* container corrupto;
* fallo de reset;
* excepción durante shutdown;
* invariantes internas rotas.

---

# 107. FrankenPHP integration

En modo Worker:

```text
Handle exception
    │
    ▼
Complete response or abort
    │
    ▼
Reset request-scoped services
    │
    ▼
Evaluate worker health
    │
    ├── Reuse
    ├── Reset
    └── Terminate
```

---

# 108. Request state cleanup

Después de cada fallo se limpiarán:

* contexts;
* execution objects;
* buffers;
* streams;
* scoped container;
* temporary files;
* telemetry spans;
* transactions pendientes;
* locks;
* locale;
* tenant context;
* authentication context.

---

# 109. Database transaction safety

El sistema no deberá asumir que toda excepción requiere rollback directo.

La gestión primaria corresponderá al `TransactionInterceptor`.

Sin embargo, durante cleanup deberá verificar que no permanezcan transacciones abiertas.

---

# 110. Lock cleanup

Los locks registrados en el lifecycle deberán liberarse cuando sea seguro.

---

# 111. Error IDs

Cada error manejado podrá recibir un identificador público.

Ejemplo:

```text
ERR-01J4Z8Y1K2M3N4
```

Este ID permitirá correlacionar:

* respuesta;
* logs;
* traces;
* métricas;
* sistemas de soporte.

---

# 112. PublicErrorIdGenerator

```php
interface PublicErrorIdGeneratorInterface
{
    public function generate(
        ClassifiedException $exception
    ): string;
}
```

---

# 113. Observabilidad

Eventos principales:

```text
ExceptionCaptured
ThrowableResolving
ThrowableResolved
ExceptionClassified
HandlingPlanResolving
HandlingPlanResolved
ExceptionReporting
ExceptionReported
RecoveryStarting
RecoverySucceeded
RecoveryFailed
ExceptionMapped
ExceptionSanitized
ExceptionRendering
ExceptionRendered
EmergencyRendering
WorkerHealthEvaluated
ExceptionHandled
ExceptionRethrown
ExceptionHandlingFailed
```

---

# 114. Métricas

```text
exceptions.total
exceptions.reported
exceptions.not_reported
exceptions.recovered
exceptions.failed
exceptions.by_category
exceptions.by_severity
exceptions.by_origin
exceptions.render.duration
exceptions.report.duration
exceptions.recovery.duration
exceptions.recursion
exceptions.emergency_render
workers.terminated_by_exception
```

---

# 115. Tracing

Span principal:

```text
exception.handle
```

Subspans:

```text
exception.resolve
exception.classify
exception.plan
exception.report
exception.recover
exception.map
exception.sanitize
exception.render
exception.worker_health
```

---

# 116. Logging context

Podrá incluir:

* error ID;
* fingerprint;
* category;
* severity;
* origin;
* route;
* controller;
* tenant ID seguro;
* request ID;
* trace ID;
* worker ID.

---

# 117. Información prohibida en logs

No se registrará automáticamente:

* passwords;
* access tokens;
* session cookies;
* private keys;
* request bodies completos;
* archivos subidos;
* secretos de configuración.

---

# 118. Metadata keys

```text
exception.category
exception.severity
exception.report
exception.recover
exception.retry
exception.renderer
exception.mapper
exception.public
exception.status
exception.worker_disposition
exception.sanitize
exception.sample_rate
```

---

# 119. Atributos potenciales

```php
#[Report]
#[DontReport]
#[PublicError]
#[ExceptionStatus(404)]
#[RenderWith(...)]
#[MapExceptionWith(...)]
#[RecoverWith(...)]
#[Retryable]
#[WorkerDisposition(...)]
```

El módulo consumirá metadata resuelta, no atributos directamente.

---

# 120. Compiler

El sistema producirá:

```text
CompiledExceptionHandlingPlan
CompiledExceptionMapperRegistry
CompiledExceptionRendererRegistry
CompiledReportPolicy
CompiledRecoveryPolicy
CompiledSanitizationPolicy
```

---

# 121. CompiledExceptionHandlingPlan

```php
final readonly class CompiledExceptionHandlingPlan
{
    public function __construct(
        public string $exceptionClass,
        public string $category,
        public string $severity,
        public bool $report,
        public bool $recover,
        public string $mapper,
        public string $renderer,
        public array $reporters,
        public array $sanitizers,
        public string $workerDisposition,
        public string $frameworkVersion,
        public string $signature,
    ) {
    }
}
```

---

# 122. Cache

Capas:

```text
L1 Handling execution
L2 Request
L3 Worker
L4 Compiled PHP
```

---

# 123. Cache segura

Solo se podrán almacenar:

* planes;
* mapeos;
* políticas;
* registries congelados;
* fingerprints normalizados;
* metadata compilada.

Nunca:

* excepciones concretas;
* stack traces;
* request;
* response;
* contexto;
* datos de usuario.

---

# 124. Testing

El módulo incluirá:

```text
FakeExceptionReporter
FakeExceptionRenderer
FakeExceptionMapper
FakeRecoveryStrategy
InMemoryErrorTracker
ExceptionHandlingTestHarness
ExceptionAssertions
```

---

# 125. Casos de prueba

* excepción conocida;
* excepción desconocida;
* error de validación;
* error de autorización;
* error de dominio;
* error de base de datos;
* renderer fallido;
* reporter fallido;
* recuperación exitosa;
* recuperación fallida;
* recursión;
* streaming iniciado;
* emisión parcial;
* memory exhaustion simulado;
* Worker reset;
* Worker termination.

---

# 126. Assertions

```php
ExceptionAssert::category(
    $result,
    ExceptionCategory::Validation
);

ExceptionAssert::reported($result);

ExceptionAssert::status($result, 422);

ExceptionAssert::renderer(
    $result,
    ProblemDetailsRenderer::class
);

ExceptionAssert::workerDisposition(
    $result,
    WorkerDisposition::Reuse
);
```

---

# 127. Directorios

```text
src/
└── Quantum/
    └── Exceptions/
        ├── Contracts/
        │   ├── ExceptionHandlerInterface.php
        │   ├── ThrowableResolverInterface.php
        │   ├── ExceptionClassifierInterface.php
        │   ├── ExceptionMapperInterface.php
        │   ├── ExceptionRendererInterface.php
        │   ├── ExceptionReporterInterface.php
        │   ├── ExceptionRecoveryStrategyInterface.php
        │   ├── ExceptionSanitizerInterface.php
        │   └── WorkerHealthEvaluatorInterface.php
        │
        ├── Engine/
        │   └── ExceptionHandler.php
        │
        ├── Context/
        │   ├── ExceptionHandlingContext.php
        │   ├── ExceptionHandlingState.php
        │   └── ExceptionOrigin.php
        │
        ├── Definition/
        │   ├── ExceptionDefinition.php
        │   ├── ClassifiedException.php
        │   ├── ExceptionCategory.php
        │   ├── ExceptionSeverity.php
        │   └── MappedException.php
        │
        ├── Pipeline/
        │   ├── ExceptionHandlingPipeline.php
        │   └── Stages/
        │       ├── InitializeExceptionHandlingStage.php
        │       ├── ResolveThrowableStage.php
        │       ├── ClassifyExceptionStage.php
        │       ├── ResolveHandlingPlanStage.php
        │       ├── CreateFingerprintStage.php
        │       ├── SanitizeExceptionStage.php
        │       ├── ReportExceptionStage.php
        │       ├── RecoverExceptionStage.php
        │       ├── MapExceptionStage.php
        │       ├── RenderExceptionStage.php
        │       ├── NormalizeErrorResponseStage.php
        │       ├── FinalizeExceptionHandlingStage.php
        │       └── CompleteExceptionHandlingStage.php
        │
        ├── Mapping/
        │   ├── ExceptionMapperRegistry.php
        │   └── Mappers/
        │       ├── ValidationExceptionMapper.php
        │       ├── AuthenticationExceptionMapper.php
        │       ├── AuthorizationExceptionMapper.php
        │       ├── NotFoundExceptionMapper.php
        │       ├── ConflictExceptionMapper.php
        │       ├── DomainExceptionMapper.php
        │       ├── DatabaseExceptionMapper.php
        │       ├── TimeoutExceptionMapper.php
        │       ├── TransportExceptionMapper.php
        │       └── ThrowableFallbackMapper.php
        │
        ├── Rendering/
        │   ├── ExceptionRendererRegistry.php
        │   ├── ErrorRepresentation.php
        │   ├── ErrorRepresentationType.php
        │   ├── ProblemDetails.php
        │   └── Renderers/
        │       ├── HtmlExceptionRenderer.php
        │       ├── JsonExceptionRenderer.php
        │       ├── ProblemDetailsRenderer.php
        │       ├── SpaExceptionRenderer.php
        │       ├── ComponentExceptionRenderer.php
        │       ├── CliExceptionRenderer.php
        │       ├── PlainTextExceptionRenderer.php
        │       └── EmergencyExceptionRenderer.php
        │
        ├── Reporting/
        │   ├── ExceptionReporterRegistry.php
        │   ├── ReportPolicy.php
        │   └── Reporters/
        │       ├── LoggerExceptionReporter.php
        │       ├── TelemetryExceptionReporter.php
        │       ├── ErrorTrackingReporter.php
        │       ├── AuditExceptionReporter.php
        │       └── SecurityIncidentReporter.php
        │
        ├── Recovery/
        │   ├── ExceptionRecoveryRegistry.php
        │   ├── RecoveryResult.php
        │   ├── RecoveryStatus.php
        │   └── Strategies/
        │       ├── FallbackValueRecovery.php
        │       ├── CachedValueRecovery.php
        │       ├── RetryRecovery.php
        │       ├── CircuitBreakerRecovery.php
        │       ├── GracefulDegradationRecovery.php
        │       ├── WorkerResetRecovery.php
        │       └── TransportAbortRecovery.php
        │
        ├── Sanitization/
        │   ├── ExceptionSanitizerPipeline.php
        │   └── Sanitizers/
        │       ├── MessageSanitizer.php
        │       ├── StackTraceSanitizer.php
        │       ├── PathSanitizer.php
        │       ├── DatabaseSanitizer.php
        │       ├── RequestDataSanitizer.php
        │       ├── HeaderSanitizer.php
        │       └── CredentialSanitizer.php
        │
        ├── Planning/
        │   ├── ExceptionHandlingPlan.php
        │   ├── ExceptionHandlingPlanResolver.php
        │   ├── DynamicExceptionHandlingPlanFactory.php
        │   └── ExceptionHandlingPlanValidator.php
        │
        ├── Fingerprint/
        │   ├── ExceptionFingerprint.php
        │   └── ExceptionFingerprintGenerator.php
        │
        ├── Worker/
        │   ├── WorkerHealthEvaluator.php
        │   ├── WorkerDisposition.php
        │   └── WorkerExceptionResetter.php
        │
        ├── Emergency/
        │   ├── EmergencyThrowableResolver.php
        │   ├── EmergencyReporter.php
        │   ├── EmergencyExceptionRenderer.php
        │   └── EmergencyMemoryReserve.php
        │
        ├── Compiler/
        │   ├── ExceptionHandlingCompiler.php
        │   ├── CompiledExceptionHandlingPlan.php
        │   ├── CompiledExceptionRegistry.php
        │   └── ExceptionArtifactWriter.php
        │
        ├── Cache/
        ├── Metadata/
        ├── Events/
        ├── Metrics/
        ├── Diagnostics/
        ├── Exceptions/
        ├── Testing/
        └── Providers/
            └── ExceptionServiceProvider.php
```

---

# 128. Configuración

```php
// config/exceptions.php

return [
    'mode' => 'auto',

    'debug' => [
        'enabled' => env('APP_DEBUG', false),
        'show_source' => true,
        'show_request' => true,
        'show_environment' => false,
    ],

    'reporting' => [
        'enabled' => true,
        'deduplicate' => true,
        'sample_rate' => 1.0,
    ],

    'rendering' => [
        'default' => 'auto',
        'problem_details' => true,
        'public_error_ids' => true,
    ],

    'recovery' => [
        'enabled' => true,
        'max_attempts' => 1,
    ],

    'sanitization' => [
        'enabled' => true,
        'hide_paths' => true,
        'sensitive_keys' => [
            'password',
            'token',
            'secret',
            'authorization',
            'cookie',
        ],
    ],

    'workers' => [
        'evaluate_health' => true,
        'terminate_on_fatal' => true,
        'reset_after_exception' => true,
    ],

    'compiled' => [
        'enabled' => true,
        'strict' => false,
    ],
];
```

---

# 129. Integración con HttpKernel

```php
try {
    $result = $controllerDispatcher->dispatch($request);

    $response = $resultTransformationEngine->transform(
        $result,
        $transformationContext,
    );

    return $responseTransportManager->send(
        $response,
        $transportContext,
    );
} catch (Throwable $throwable) {
    $handlingResult = $exceptionHandler->handle(
        $throwable,
        $exceptionContextFactory->create($throwable),
    );

    return $exceptionResultExecutor->execute(
        $handlingResult,
    );
}
```

---

# 130. ExceptionHandlingResult

```php
final readonly class ExceptionHandlingResult
{
    public function __construct(
        public ExceptionHandlingStatus $status,
        public ?ResponseInterface $response,
        public ?ErrorRepresentation $representation,
        public ?RecoveryResult $recovery,
        public WorkerDisposition $workerDisposition,
        public bool $rethrow,
        public ?Throwable $throwable,
    ) {
    }
}
```

---

# 131. Decisión terminal

El resultado final podrá indicar:

```text
Return response
Recover with replacement
Abort current transport
Rethrow
Terminate worker
Terminate process
```

---

# 132. ADR-001

**Las excepciones de dominio serán independientes del transporte.**

No deberán contener directamente lógica HTTP.

---

# 133. ADR-002

**Reportar, recuperar, mapear y renderizar son etapas separadas.**

---

# 134. ADR-003

**La excepción original será siempre preservada.**

Los errores secundarios no la reemplazarán.

---

# 135. ADR-004

**Toda salida pública será sanitizada.**

---

# 136. ADR-005

**El modo Debug modifica visibilidad, no comportamiento funcional.**

---

# 137. ADR-006

**Los errores después de iniciar la emisión no producirán una segunda respuesta.**

---

# 138. ADR-007

**La recuperación deberá ser explícita y limitada.**

---

# 139. ADR-008

**Los Workers serán evaluados después de errores críticos.**

---

# 140. ADR-009

**El sistema deberá disponer de un modo de emergencia con dependencias mínimas.**

---

# 141. ADR-010

**Los registries serán congelados en producción.**

---

# 142. ADR-011

**Los planes compilados no contendrán excepciones ni datos de petición.**

---

# 143. ADR-012

**El sistema de errores utilizará Result Transformation para construir respuestas ordinarias.**

Solo el modo de emergencia evitará esta integración.

---

# 144. ADR-013

**Problem Details será una representación, no la abstracción central del error.**

---

# 145. ADR-014

**Los errores de componentes podrán aislarse mediante boundaries.**

---

# 146. ADR-015

**La deduplicación utilizará fingerprints estables y sanitizados.**

---

# 147. ADR-016

**Los reporters no podrán alterar la respuesta de error.**

---

# 148. ADR-017

**El sistema no reintentará operaciones después de emisión irreversible.**

---

# 149. ADR-018

**La salud del Worker será una decisión formal del pipeline.**

---

# 150. ADR-019

**Los errores fatales y de bootstrap utilizarán un pipeline reducido.**

---

# 151. ADR-020

**La recursión en el manejo de errores estará protegida por un guard.**

---

# 152. V1

La primera versión deberá implementar:

* ExceptionHandler;
* ThrowableResolver;
* ExceptionClassifier;
* ExceptionMapperRegistry;
* ExceptionRendererRegistry;
* ExceptionReporterRegistry;
* sanitización;
* Problem Details;
* HTML errors;
* JSON errors;
* SPA errors;
* validation errors;
* authorization errors;
* not found;
* domain errors;
* database errors;
* Emergency Renderer;
* error IDs;
* fingerprinting;
* reporting;
* Worker health;
* FrankenPHP reset;
* streaming failure policy;
* compiler;
* cache;
* observabilidad;
* testing utilities.

---

# 153. V2

Podrá incluir:

* component error boundaries avanzados;
* recuperación adaptativa;
* reglas de sampling dinámico;
* integración con circuit breakers;
* agrupación inteligente de errores;
* error budgets;
* fallbacks distribuidos;
* mejores diagnósticos de concurrencia.

---

# 154. V3

Podrá incorporar:

* clasificación asistida;
* detección automática de causa raíz;
* agrupación semántica;
* recomendaciones de corrección;
* recuperación predictiva;
* análisis distribuido entre nodos;
* políticas de resiliencia adaptativas.

---

# 155. Flujo final de éxito

```text
Controller
    │
    ▼
Raw Result
    │
    ▼
Result Transformation
    │
    ▼
Response
    │
    ▼
Transport
    │
    ▼
Client
```

---

# 156. Flujo final de error antes de emisión

```text
Throwable
    │
    ▼
Exception Handler
    │
    ▼
Mapped Exception
    │
    ▼
Error Representation
    │
    ▼
Result Transformation
    │
    ▼
Response
    │
    ▼
Transport
```

---

# 157. Flujo final de error durante emisión

```text
Emission started
    │
Throwable
    │
    ▼
Transport failure policy
    │
    ├── Abort stream
    ├── Emit terminal frame
    ├── Close connection
    └── Mark incomplete
```

---

# 158. Resultado arquitectónico

Con este sistema, VoltStack tendrá una única infraestructura coherente para:

* errores internos;
* excepciones de dominio;
* validación;
* seguridad;
* infraestructura;
* SPA;
* HTML;
* APIs;
* CLI;
* streaming;
* Workers persistentes.

La arquitectura evita mezclar excepciones con HTTP, mantiene la respuesta pública segura y permite que cada módulo participe mediante mappers, renderers, reporters y políticas registrables.

---

1. Próximo documento recomendado

Con Controllers, transformación, transporte y errores definidos, el siguiente documento recomendado es:

CONTROLLER_LIFECYCLE_AND_EXECUTION_STATE.md

Este documento deberá consolidar:

ciclo de vida completo del controlador;
estados de ejecución;
cancelación;
short-circuit;
cleanup;
recursos asociados;
transacciones;
locks;
eventos;
integración entre Dispatcher, Interceptors, Invoker, Transformation, Transport y Exceptions;
seguridad para FrankenPHP;
observabilidad;
compilación;
testing.
