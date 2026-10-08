# 25 — Ciclo de vida en runtimes persistentes

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Puerto de ciclo de vida

RuntimeManagerServer es el propietario propuesto de admisión, scope, reset y reciclado. El bridge de Exceptions implementa este contrato de integración sin imponer un servidor específico:

```php
interface ExceptionRuntimeBridge
{
    public function begin(RuntimeOperation $operation): ExceptionScope;
    public function context(ExceptionScope $scope): ExceptionContext;
    public function finalize(ExceptionScope $scope, FinalizationOutcome $outcome): void;
    public function close(ExceptionScope $scope): ResetReport;
}
```

RuntimeOperation contiene transport, operationId, deadline y metadata confiable. ExceptionScope es handle opaco, inválido tras close. FinalizationOutcome informa emitted/aborted/job_recorded/failed sin fingir entrega de red al usuario final. ResetReport contiene reusable y lista acotada de códigos de fallo. Todos son puertos propuestos, no firmas certificadas de RuntimeManagerServer existente.

## Propiedad de estado

| Lifetime | Permitido |
|---|---|
| Proceso inmutable | plan, catálogo, factories, esquema de protocolo, templates precompiladas |
| Proceso mutable controlado | buffer saneado limitado, contadores, pool de conexiones con ownership |
| Scope | manager, contexto, ledger de ocurrencias, handlers locales, spans |
| Captura local | Throwable original, resources temporales de normalización |

No guardar último request, user, tenant, ExceptionContext, Throwable o closure con variables scoped en singletons. Un servicio mutable compartido declara mecanismos de concurrencia y reset; declarar singleton no basta para seguridad en coroutines.

## Cierre garantizado y recuperación

Cada entrada abre scope antes de código de aplicación y lo cierra en finally. El orden propuesto: impedir nuevas tareas → cancelar/esperar hijos acotadamente → finalizar reporte de transporte → liberar recursos del módulo propietario → limpiar ledger y referencias → terminar spans propios → emitir ResetReport. Cleanup mantiene la excepción primaria si aparece otra falla; registra secundaria y marca reciclado.

El cierre debe ser idempotente en el handle: segunda llamada no libera recursos de otra operación. Un reset fallido impide reutilizar worker; no basta borrar una variable y continuar. Una conexión con transacción incierta se descarta por Database, no se devuelve sana al pool por optimismo.

## Concurrencia y shutdown

Scopes concurrentes usan almacenamiento explícito o local a coroutine/Fiber. El handler global consulta solo el scope de ejecución válido; no un puntero global mutable. Drain deja de admitir operaciones, permite terminar activas hasta deadline, vacía buffer con límite y recicla. Un fatal puede evitar finally: supervisión externa y recursos con expiración siguen siendo necesarios.

## Aceptación

Probar miles de scopes, streams abortados, doble close, fallos en reset y ejecución tras cierre. Medir referencias retenidas y memoria después de warmup. El test debe provocar errores y alternar identidades, no solo enviar requests exitosos. Reciclar cada N requests reduce impacto de fugas, pero no sustituye demostrar aislamiento.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](24_ASYNC_JOBS_QUEUES_AND_CANCELLATION.md) · [Siguiente](26_FRANKENPHP_DEFAULT_RUNTIME.md)
