# 33 — Experiencia de desarrollo y debugging

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## DX de aplicación

La aplicación configura el sistema en un punto y utiliza excepciones de dominio normales. Se propone `withExceptions(callable)` en el builder de Platform, sin afirmar que ese método exista hoy. La configuración recibe ExceptionConfiguration y produce el mismo plan de 31.

```php
// Ejemplo de API propuesta; imports de aplicación omitidos.
$app->withExceptions(function (ExceptionConfiguration $exceptions): void {
    $exceptions->map(
        id: 'billing.balance',
        exceptionType: InsufficientBalance::class,
        serviceId: BalanceErrorMapper::class,
        priority: 100,
    );
    $exceptions->dontReport('validation.failed');
    $exceptions->reporter('exceptions.log', StructuredLogReporter::class);
    $exceptions->renderer('problem_json', JsonExceptionRenderer::class);
});
```

Contratos del builder: map usa RuleDefinition de 31; dontReport(string code) añade exclusión ordinaria; reporter(string id, string serviceId) registra implementación; renderer(string target, string serviceId) vincula formato. report callbacks y render callbacks son azúcar opt-in sobre adaptadores tipados, nunca redefiniciones del pipeline.

## Helpers y excepciones autocontenidas

`report(Throwable)` retorna ReportReceipt; `abort(int status, array headers=[])` lanza excepción HTTP validada; `abortIf(bool, int)` y `abortUnless(bool, int)` son wrappers. La firma propuesta de recovery local es `rescue(callable $operation, array $types, callable $fallback): mixed`, donde types es una lista no vacía de class-string allowlisted. El helper captura y reporta únicamente esos tipos y consulta la política de recuperación antes de llamar al fallback. En v1 no hay rescue genérico que trague cualquier Throwable: los fallos de seguridad/integridad no recuperables se propagan incluso si un tipo genérico aparece en la lista. El valor retornado es un resultado explícito de la operación/fallback y no determina el efecto transaccional.

Las interfaces opcionales `ProvidesErrorMetadata::errorMetadata(): SafeErrorMetadata`, `ReportableException::reportDefinition(): string` y `RenderableException::renderDefinition(): string` pueden devolver metadata o serviceId registrado; no realizan I/O ni generan bytes dentro de la excepción. Esto da conveniencia sin acoplar dominio a HTTP. Métodos arbitrarios llamados report/render no se invocan por duck typing.

Closures de configuración solo se ejecutan en bootstrap. Callbacks dinámicos de manejo deben ser stateless, declarados como servicios para compilación de producción. Capturar `$request` en la closure está prohibido; el diagnóstico del compilador explica la migración a servicio.

## Debug local

Debug inspector muestra código, regla aplicada, transport plan, receipts, frames redactados y límites alcanzados. Argumentos permanecen ocultos. Source frames se leen bajo roots de proyecto allowlisted y con máximo de bytes/líneas; paths externos no abren archivos arbitrarios. La vista nunca incluye `.env`, credentials o payloads de sesión.

CLI propuesto: `exceptions:explain --type=... --transport=json`, `exceptions:catalog`, `exceptions:validate`, `exceptions:compile` y `exceptions:doctor`. explain usa fixtures sintéticos, no ejecuta una operación real. doctor detecta bridge ausente, debug en producción, buffer fuera de presupuesto y plan obsoleto; no imprime secretos.

## Pruebas de DX

Una app mínima debe poder mapear una excepción con una regla, verla como 409 JSON y reportarla con el mismo occurrenceId. Mensajes de configuración identifican archivo/regla/serviceId y acción correctiva. Comparar acceso por helper e inyección: mismo resultado y cleanup. Las herramientas se especifican aquí; no se entregan comandos ejecutables en esta colección documental.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](32_CONTAINER_BOOTSTRAP_AND_PLATFORM_COMPOSITION.md) · [Siguiente](34_PRODUCTION_BEHAVIOR_AND_OPERATIONAL_RUNBOOKS.md)
