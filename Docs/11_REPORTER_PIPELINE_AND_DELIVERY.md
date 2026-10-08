# 11 — Pipeline de reporters y entrega

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Reporters aislados

ReportCoordinator recibe ReportRecord redactado, resuelve una lista ordenada de reporters y aplica un deadline total. Reporters típicos: log estructurado, bridge Telemetry y exportador externo opt-in. Un canal de métricas puede operar fuera del sampling diagnóstico para no sesgar tasas. El exporter de trazas no duplica el logger salvo decisión explícita.

```text
ReportRecord → política por reporter → reserva de presupuesto
            → intento acotado → receipt
            → siguiente reporter si queda presupuesto
```

El contrato `report()` de 02 solo confirma accepted/dropped/failed/skipped. Accepted puede significar encolado en memoria; el reporte no es durable hasta confirmación del sistema de entrega correspondiente. Todos los reporters reciben el mismo occurrenceId y su propio deliveryId. Un tercero que no soporta idempotencia puede recibir duplicados.

## Tiempo y buffers

La ruta síncrona tiene máximo total 50 ms propuesto y ningún retry de red. En PHP una llamada bloqueante arbitraria no se puede interrumpir de manera segura solo midiendo el tiempo después; por ello se exige timeout real del cliente o aislamiento en worker. Un reporter que no lo soporta no puede registrarse como reporter de red síncrono certificado.

El buffer compartido contiene DTOs saneados, límite de bytes, límite de registros y vencimiento. Al llenarse, aplica drop de menor severidad y aumenta contador; no retiene objetos Throwable ni closures. Exportación durable opcional usa una cola específica sin payload del job de negocio. Si esa cola falla, no se envía el fallo de cola a sí misma recursivamente.

## Fallos secundarios

Un reporter fallido no cambia status público, código original ni decisión de rollback. Se registra fixedCode `exceptions.reporter_failed`, reporterId registrado y occurrenceId padre mediante EmergencySink acotado. La ruta de emergencia no usa el mismo logger de aplicación. Un sink también fallido provoca descarte contado cuando sea posible; no un bucle de reportes.

El redactor se aplica antes del buffer y nuevamente según política del destino externo. Los secrets del exporter se resuelven desde el contenedor seguro y no forman parte del DTO ni del plan compilado. Los endpoints externos no pueden derivarse de entradas del request.

## Contrato de aceptación

Desconectar red, llenar disco y bloquear exporter deben dejar el transporte principal funcional dentro de límites reales. Verificar datos enviados byte por byte y ausencia de raw SQL/cookies. Matar proceso después de accepted en memoria puede perder reporte: esa limitación es parte del contrato. Con cola durable, verificar duplicados tolerados, reintentos acotados y eliminación por retención.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](10_REPORTING_POLICY_AND_DEDUPLICATION.md) · [Siguiente](12_RENDERING_NEGOTIATION_AND_RESPONSE_RESOLUTION.md)
