# 34 — Producción y procedimientos operativos

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Perfil de producción

debug=false, catálogo compilado, headers seguros, outputs mínimos, no-store, límites activos y reporting acotado. El error público muestra mensaje aprobado y referencia; la causa interna se consulta por canal protegido. Aplicación fallida y sistema de excepciones fallido son indicadores diferentes y deben poder alertarse por separado.

| Situación | Respuesta | Operación |
|---|---|---|
| Error de negocio previsto | 4xx apropiado | métrica y audit según módulo |
| Bug no clasificado | 500 seguro | error report con referencia |
| Reporter caído | respuesta original | buffer/drop contado, alerta exporter |
| Renderer caído | fallback constante | diagnóstico secundario |
| Headers ya enviados | abortar/frame terminal válido | marcar fallo de stream |
| Reset incompleto | respuesta si posible, recycle | retirar worker antes de siguiente scope |
| OOM/fatal | mejor esfuerzo mínimo | supervisor reemplaza proceso |

## Procedimiento: subida de errores internos

Confirmar tasa por código/versión de despliegue y distinguir 4xx esperados. Consultar muestra saneada por occurrenceId y correlacionar con trace protegido. Identificar primer fallo de dependencia, cambio de plan o código. Si comenzó con despliegue, rollback de release y drain; no activar debug público ni aumentar logging sin revisar PII. Verificar recuperación con tráfico sintético y tasa de errores; cerrar incidente con causa y prueba de regresión.

## Procedimiento: resultado incierto de mutación

Conservar operationRef y pedir al dueño de negocio conciliación mediante ledger/proveedor. No solicitar al usuario reenviar la operación hasta conocer efecto o garantía de idempotencia. Si Committed, devolver/mostrar resultado conocido por endpoint autorizado. Si None comprobado, permitir nuevo intento bajo política. Si sigue Unknown, escalar operativamente; el paso del tiempo no lo transforma automáticamente en None.

## Procedimiento: tormenta de reportes

Observar buffers, drops y cardinalidad. Mantener métricas agregadas y audit obligatorio; ajustar sampling diagnóstico de grupo registrado y presupuesto local mediante nuevo perfil. No agrupar por mensaje o tenant arbitrario. Si disco/red fallan, usar emergencia limitada y restaurar exporter fuera del camino de request. Registrar ventana de posible pérdida de diagnóstico.

## Procedimiento: fuga entre scopes

Retirar workers afectados y detener exposición si hay datos cruzados. Identificar servicio singleton con contexto retenido, limpiar caches privadas según módulo y corregir ownership. Reciclar es mitigación inmediata, no solución definitiva. Ejecutar tests de identidades alternadas y concurrencia antes de volver a admitir tráfico.

## Readiness, liveness y presupuesto

Readiness exige plan válido y capacidad del runtime; no depende de cada exporter de diagnóstico para evitar retirar toda la flota por caída de Telemetry. Una dependencia de auditoría obligatoria puede afectar operaciones protegidas según su contrato. Liveness no usa una ruta que invoque toda la aplicación fallida. Flush y drain respetan deadlines publicados.

## Criterios de recuperación

Un incidente termina cuando efectos están conciliados, workers sanos, privacidad verificada y tasas estabilizadas. Registrar datos diagnósticos perdidos, no inventar éxito de entrega. Los umbrales de alertas dependen del SLO del servicio; esta especificación no afirma números de latencia o disponibilidad medidos.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](33_DEVELOPER_EXPERIENCE_AND_DEBUGGING.md) · [Siguiente](35_PERFORMANCE_AND_RESOURCE_GOVERNANCE.md)
