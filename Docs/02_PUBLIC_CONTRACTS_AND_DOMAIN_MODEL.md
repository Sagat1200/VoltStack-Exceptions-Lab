# 02 — Contratos públicos y modelo de dominio

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Contratos autoritativos

Todas las interfaces de este capítulo son propuestas bajo `VoltStack\Quantum\Exceptions\Contracts`. Los objetos de valor descritos son inmutables; sus constructores validan rangos y tipos. Las firmas siguientes son el contrato mínimo de implementación, no una implementación PHP completa.

```php
interface ExceptionManagerInterface
{
    public function handle(\Throwable $error, ExceptionContext $context): HandlingResult;
    public function report(\Throwable $error, ExceptionContext $context): ReportReceipt;
}
interface ExceptionNormalizerInterface
{
    public function normalize(\Throwable $error, ExceptionContext $context): FailureSnapshot;
}
interface ExceptionMapperInterface
{
    public function map(FailureSnapshot $failure, ExceptionContext $context): ?SemanticError;
}
interface TransportMapperInterface
{
    public function map(ExceptionDescriptor $descriptor, TransportContext $context): TransportPlan;
}
interface ExceptionReporterInterface
{
    public function report(ReportRecord $record, ReportBudget $budget): ReporterReceipt;
}
interface ExceptionRendererInterface
{
    public function render(PublicError $error, TransportPlan $plan): RenderedOutput;
}
interface RecoveryPolicyInterface
{
    public function decide(ExceptionDescriptor $error, RecoveryContext $context): RecoveryDecision;
}
interface ExceptionEventPort
{
    public function publish(ExceptionEventRecord $event): void;
}
interface EmergencySinkInterface
{
    public function write(EmergencyRecord $record): void;
}
```

Los contratos referencian los siguientes tipos con forma cerrada. `list<T>` significa lista secuencial; mapas de atributos solo admiten escalares, null y listas/mapas acotados de esos valores. No se admite `mixed` arbitrario proveniente de objetos.

| Tipo | Campos requeridos y semántica |
|---|---|
| `FailureSnapshot` | className, internalMessage redactado, phpCode, frames, causes, origin, safeMetadata, truncation; sin Throwable serializable |
| `SafeErrorMetadata` | mapa de atributos de esquema registrado, valores escalares o estructuras acotadas; clasificados por destino y redactados antes de uso |
| `SemanticError` | code, category, messageKey, safeParameters, severity, effect, retryAdvice; valores definidos en 06 |
| `ExceptionDescriptor` | occurrenceId, contextSummary, failure, semantic, policyRevision; uso interno |
| `PublicError` | code, message, occurrenceId, fields opcional; proyección por allowlist |
| `TransportContext` | kind, committed, routeProfile, accept, locale, spaVersion; datos de bridge verificados |
| `TransportPlan` | target, status o exitCode, headers, spaAction, retryAfterSeconds opcional |
| `RenderedOutput` | target, bodyBytes, mediaType, status/exitCode, safeHeaders; sin envío implícito |
| `ReportRecord` | occurrenceId, parentOccurrenceId opcional, fingerprint, semantic, diagnóstico redactado, correlación |
| `ReportBudget` | deadlineMonotonic, maxBytes, maxAttempts=1 en camino síncrono |
| `ReporterReceipt` | reporterId, state, duration, errorCode opcional; state=accepted/dropped/failed/skipped |
| `ReportReceipt` | occurrenceId, list<ReporterReceipt>, deduplicated; accepted no significa entrega remota |
| `RecoveryContext` | owner, effect, idempotencyVerified, attempt, deadline, cancellation, capabilities |
| `RecoveryDecision` | action=none/retry_advice/fallback_advice/reconcile/abort, reasonCode, delayMs opcional |
| `ExceptionEventRecord` | eventId, occurrenceId, phase, sanitizedMetadata, timestamp |
| `EmergencyRecord` | occurrenceId opcional, fixedCode, phase; nunca Throwable ni mensaje arbitrario |

## Resultado discriminado

`HandlingResult` tiene `kind`, `occurrenceId`, `reportReceipt`, `workerDisposition` y una única carga según kind:

| kind | Carga | Consumidor |
|---|---|---|
| `rendered` | output: RenderedOutput | HTTPKernel/Console emite una vez |
| `propagate` | descriptor: ExceptionDescriptor | boundary superior usa Throwable original local |
| `job_decision` | decision: RecoveryDecision | Jobs decide retry/fail/reconcile |
| `abort_transport` | reasonCode | emisor cierra stream ya iniciado |
| `emergency` | output mínimo opcional | bridge emite si todavía es posible |

`workerDisposition` es `reusable`, `recycle` o `terminate`; no termina el proceso desde el núcleo. Un resultado `propagate` no contiene un Throwable serializado: el catch propietario mantiene la referencia local y la relanza. Ningún resultado significa que los bytes ya fueron enviados. Una respuesta HTML, JSON o SPA solo procede si `committed=false`.

## Reglas de implementación

`handle()` es terminal para su frontera, no para el proceso. `report()` normaliza, clasifica y reporta sin recovery ni rendering. Ambos usan el mismo registro de ocurrencias del scope. Si un reporter lanza, el coordinador convierte el fallo a receipt; si el propio coordinador falla, usa emergencia. Las firmas no prometen que todo Throwable sea recuperable ni que PHP pueda atrapar todos los fallos fatales.

El contexto 05 y el catálogo 06 completan las formas de datos. Los puertos de runtime 25 y configuración 31 son contratos adicionales de integración. Implementaciones pueden añadir métodos internos; no cambian las formas públicas sin versionado de 37.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](01_EXCEPTION_SYSTEM_ARCHITECTURE.md) · [Siguiente](03_EXCEPTION_LIFECYCLE_AND_PIPELINE.md)
