# 10 — Política de reporting y deduplicación

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Decidir qué reportar

ReportPolicy separa ocurrencia, registro diagnóstico, métricas y auditoría. El conteo agregado de errores elegibles sucede antes de sampling. Las violaciones de negocio esperadas pueden omitir logs ordinarios; las obligaciones de auditoría pertenecen a Authentication/Authorization y no se anulan mediante dontReport.

Orden: clasificar → aplicar obligaciones de seguridad → exclusiones ordinarias por código/tipo → seleccionar severidad → deduplicar ocurrencia por reporter → aplicar sampling/throttle → despachar. El receipt conserva la razón `policy_ignored`, `duplicate`, `sampled_out`, `budget_exhausted` o `queue_full`, sin afirmar entrega.

## Deduplicación local

El ledger scoped identifica el objeto original mediante WeakMap o mecanismo equivalente sin prolongar su vida. La clave lógica de entrega es `(occurrenceId, reporterId)`. `report($e)` seguido de `throw $e` conserva identidad; dos instancias iguales se reportan por separado. Un wrapper puede asociarse explícitamente a la ocurrencia de su causa; tener previous por sí solo no prueba que sea el mismo incidente.

Un reporter se marca como intentado antes de invocarlo, evitando reentrada. Si falla, se registra failed y no se vuelve a intentar síncronamente en la misma ocurrencia. La cola de exportación puede reintentar el DTO con deliveryId estable y semántica al menos una vez. Deduplicar dentro del request no promete deduplicación entre procesos.

## Sampling y tormentas

La política permite ratio por código/grupo, token bucket por fingerprint y presupuesto total de proceso. Fingerprints no incluyen usuarios, emails ni URLs arbitrarias. El token bucket compartido es opcional; al fallar aplica un límite local conservador en lugar de bloquear el request.

Errores críticos de arranque conservan una ruta mínima no muestreada pero acotada para evitar llenar disco. Los contadores de supresión se reportan agregados. Dedup no se utiliza como cache de “este tipo ya ocurrió”: eso ocultaría todos los incidentes futuros.

| Categoría | Default | Justificación |
|---|---|---|
| Validation / not found esperados | no log ordinario | alto volumen y bajo valor diagnóstico |
| Authorization denied | canal de auditoría propietario; log ordinario off | preservar seguridad sin duplicación |
| Dependencia no disponible | warning/error con límites | incidente operativo |
| Internal/configuration | error/critical | investigación necesaria |
| Cancelación de cliente | agregado, detalle muestreado | no tratarla como bug siempre |

## Verificación

Probar rethrow, dos objetos equivalentes, dos reporters, failure de un reporter, TTL de buffers y saturación. Una ráfaga debe conservar contadores de ocurrencias y descartes con memoria limitada. El scope siguiente empieza sin dedup residual. Cambiar dontReport nunca suprime el audit record que el motor de Authorization exige antes de considerar completada una acción protegida.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](09_DOMAIN_TO_TRANSPORT_MAPPING.md) · [Siguiente](11_REPORTER_PIPELINE_AND_DELIVERY.md)
