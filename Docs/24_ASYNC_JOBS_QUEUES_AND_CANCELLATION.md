# 24 — Async, Jobs, Queues y cancelación

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Jobs y workers

JobExceptionBridge abre scope por intento, captura Throwable, consulta efecto y pasa contexto al manager. Recibe job_decision; Jobs conserva autoridad sobre ACK, retry, deadline, lease y dead-letter. Un error de negocio terminal puede marcar el job failed sin matar el worker. Un estado de proceso inseguro exige reciclarlo después del protocolo de entrega correspondiente.

```text
dequeue → scope nuevo → autenticar contexto de ejecución → ejecutar unidad
       → éxito confirmado → ACK según contrato de broker
       → error → clasificar → retry/fail/reconcile → persistir decisión → ACK/release
finally → cerrar recursos y scope
```

El orden exacto persistencia/ACK depende del broker y debe tolerar caída entre pasos. No se promete exactly-once. Si el efecto ya se confirmó y se pierde ACK, la reentrega necesita idempotencia del job; Exceptions no resuelve esa brecha.

## Failure envelope durable

Datos permitidos: schemaVersion=1, jobRef opaco, attemptId, attempt, occurrenceId, code, category, effect, retryAdvice, timestamp, policyRevision y diagnóstico redactado opcional. No se serializan Throwable, conexiones, Container, credenciales ni payload completo. Un envelope no autoriza al consumidor a suplantar la identidad original: Jobs resuelve permisos vigentes.

Reintentos mantienen jobRef, crean attemptId/occurrenceId nuevos y consumen un presupuesto total. Poison messages por formato inválido van a terminal/dead-letter según política; no bucle. Un replay manual exige autorización, muestra efecto y clave de idempotencia y registra auditoría. Partial/Unknown se concilian antes de ejecutar otra vez.

## Async local y concurrencia estructurada

Una tarea hija que falla comunica resultado a su scope padre. Al cerrar el padre se espera/cancela según deadline; ninguna tarea conserva request tras el cierre. Cancelación no es confirmación de que la operación remota no sucedió. El dueño determina efecto; desconectar cliente no revierte un commit.

Un AggregateFailure conserva lista acotada de fallos de tareas y causa primaria determinista por orden de tarea, no por orden de llegada variable. Cada fallo hijo tiene ocurrencia propia; el agregado relaciona ids y no repite reportes ya emitidos. Información de otro tenant no entra al agregado compartido.

## Fronteras HTTP/Jobs

Una vez que HTTP confirmó “trabajo aceptado”, el fallo posterior del job no modifica esa respuesta. Estado de operación y notificación SPA se publican por mecanismos autorizados de Jobs/Events, preferiblemente con durabilidad apropiada. El cliente puede consultar estado; un error de job no se inyecta como stack trace en un canal broadcast público.

## Pruebas

Pérdida de ACK, crash antes/después de commit, job expirado, cancelación entre pasos, retry agotado y dead-letter inaccesible. Intercalar 10.000 intentos de diferentes tenants comprueba aislamiento. Un fake de cola valida decisiones, pero solo pruebas con broker real validan redelivery y ACK. Declarar esa diferencia en la matriz de soporte.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](23_TELEMETRY_TRACING_METRICS_AND_CORRELATION.md) · [Siguiente](25_PERSISTENT_RUNTIME_LIFECYCLE_AND_ISOLATION.md)
