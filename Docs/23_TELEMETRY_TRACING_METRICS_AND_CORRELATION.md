# 23 — Telemetry, trazas, métricas y correlación

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Puerto hacia Telemetry

ExceptionTelemetryBridge es opcional y adapta DTOs del núcleo a Telemetry. No se han verificado firmas existentes de ese módulo; el contrato mínimo es registrar ocurrencia agregada, evento diagnóstico saneado y resultado de operación. El núcleo no depende del SDK OpenTelemetry.

El bridge puede seguir las [convenciones de excepciones en spans de OpenTelemetry](https://opentelemetry.io/docs/specs/semconv/exceptions/exceptions-spans/) en su versión fijada. La versión de convenciones se registra en el despliegue; no se cambian atributos automáticamente por apuntar a documentación current. Mensaje y stack son opcionales y se omiten cuando su saneamiento no sea seguro.

## Propiedad de spans

HTTPKernel/Jobs/Database son dueños de sus spans; Exceptions añade información pero no termina spans ajenos. El estado final depende del contrato de la operación: un fallo no manejado normalmente implica error, mientras una validación esperada o fallback de componente puede no marcar el span raíz como fallo interno. No se marca Error automáticamente para cualquier 4xx.

Una excepción que sube por varias capas conserva occurrenceId. El bridge evita registrar el mismo evento varias veces en el mismo span. Spans distintos pueden registrar impacto propio con vínculo a la misma ocurrencia si aporta información, sin contar ocurrencias nuevas. Si tracing está deshabilitado o no sampleado, reporting y métricas continúan con sus políticas.

## Instrumentos propuestos

| Instrumento | Tipo | Dimensiones permitidas |
|---|---|---|
| `voltstack.exceptions.occurrences` | counter | code registrado, category, transport |
| `voltstack.exceptions.handling.duration` | histogram ms | stage, transport |
| `voltstack.exceptions.reporting` | counter | reporterId registrado, receipt state |
| `voltstack.exceptions.suppressed` | counter | reason, group de catálogo |
| `voltstack.exceptions.emergency` | counter | phase, fixedCode |
| `voltstack.exceptions.buffer.bytes` | gauge | buffer registrado |
| `voltstack.exceptions.worker.recycles` | counter | reason acotado |

No usar occurrenceId, traceId, tenant, user, URL completa, raw message o fingerprint ilimitado como etiquetas. Código no registrado se agrupa como unknown. Un catálogo gigantesco también requiere límites de series; routeName opcional solo si proviene de inventario acotado. Exemplars pueden correlacionar trazas según política sin crear dimensión por ID.

## Correlación y fallos

requestId, operationId, occurrenceId y traceId tienen propósitos distintos. Propagación remota valida formato y confianza de baggage; no copia PII a headers. El public occurrenceId sirve para soporte, y el servidor conserva el vínculo protegido con la traza. No se exige exportación exitosa para finalizar una respuesta.

Durante una caída del exporter, buffer acotado y política de descarte mantienen memoria predecible. Shutdown concede flush con deadline; no espera indefinidamente. Métricas de descarte no se exportan recursivamente a través del mismo pipeline fallido sin límite.

## Aceptación

Comparar conteo con sampling activado: occurrences no debe reducirse por muestreo diagnóstico. Probar doble captura en mismo span, span ausente, inválido trace header, exporter bloqueado y cardinalidad con entradas aleatorias. Separar SLO de fallos de aplicación, fallos de manejo y pérdidas de telemetría; no confundir ausencia de trazas con ausencia de errores.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](22_PRIVACY_REDACTION_AND_DATA_GOVERNANCE.md) · [Siguiente](24_ASYNC_JOBS_QUEUES_AND_CANCELLATION.md)
