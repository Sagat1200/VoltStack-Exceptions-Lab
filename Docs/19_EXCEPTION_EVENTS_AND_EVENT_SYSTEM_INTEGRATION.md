# 19 — Eventos e integración con Event System

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Eventos observacionales

El bridge Event System publica hechos inmutables y saneados. El núcleo depende de ExceptionEventPort, con implementación null cuando Events no está instalado. El pipeline no obtiene decisiones mutando un evento global: mapping, recovery y rendering ya tienen puertos tipados. Esto permite extensibilidad sin hacer que el resultado dependa de listeners accidentales.

| Evento | Momento | Payload adicional permitido |
|---|---|---|
| ExceptionCaught | metadata mínima segura disponible | origin, transport |
| ExceptionClassified | descriptor sellado | code, category, ruleId |
| ExceptionRecoveryDecided | consejo calculado | action, reasonCode |
| ExceptionReportCompleted | intentos concluidos | conteos por receipt state |
| ExceptionResolved | resultado construido | resultKind, status/exitCode |
| ExceptionFinalized | bridge confirma emisión/ACK intentado según caso | outcome, transportCommitted |
| ExceptionHandlingFailed | falla interna aislada | phase, fixedCode |

Todos incluyen eventId, occurrenceId, timestamp y policyRevision. No incluyen Throwable, Request, entidades, raw message ni payload del job. EventId sirve para idempotencia de consumidores; el orden solo está garantizado dentro de una ocurrencia síncrona mientras el proceso sobreviva.

## Listeners y aislamiento

Listeners síncronos tienen prioridad y serviceId declarados, presupuesto común y no pueden resolver otra vez la ocurrencia. Fallar un listener observacional produce diagnóstico secundario y no interrumpe salida. Una dependencia crítica de negocio no debe modelarse como listener de Exceptions; pertenece al flujo de aplicación, donde su fallo puede abortar la operación explícitamente.

Handlers que deban influir en resultado se registran como mapper, recovery policy o renderer, y respetan su orden y validación. No existe `stopPropagation()` capaz de saltarse redacción, cleanup o auditoría de seguridad. Un plugin no instala un segundo emisor de respuesta mediante evento.

## Entrega durable

Los eventos de excepciones son best effort por defecto. Un evento de error ocurrido después de rollback no puede escribirse en una outbox de esa misma transacción abortada. Si se requiere evidencia durable se utiliza canal independiente diseñado por Audit, con sus propias garantías, retención y tratamiento de indisponibilidad. Reporting no declara atomicidad con la mutación fallida.

El consumidor durable tolera duplicados y eventos fuera de orden. No infiere “la operación nunca sucedió” porque solo recibió Caught sin Finalized: el proceso pudo morir o la red perder el segundo evento. Se correlaciona con el estado autoritativo de la operación cuando exista.

## Pruebas

Registrar secuencia con EventRecorder; lanzar desde listeners; deshabilitar Event System y repetir el flujo. Verificar esquema saneado, límites y no reentrada. Para durable, perder ack de evento y reproducirlo: consumidor no ejecuta dos efectos. La ausencia de una notificación observacional no cambia el resultado de negocio confirmado.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](18_CLI_EXCEPTION_RENDERING_AND_EXIT_CODES.md) · [Siguiente](20_RECOVERY_RETRY_AND_EFFECT_SAFETY.md)
