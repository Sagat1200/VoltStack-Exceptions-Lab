# 27 — Adaptadores RoadRunner y OpenSwoole

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Compatibilidad por capacidades

RoadRunner y OpenSwoole son objetivos futuros adaptables; no se declaran soportados sin suite. El contrato 25 evita acoplar el núcleo a globals del servidor. Un adapter declara capabilities: requestScope, concurrentScopes, streamingFinalization, cancellation, gracefulDrain, recycleSignal y perOperationContext.

| Runtime objetivo | Adaptación a demostrar | Riesgo principal |
|---|---|---|
| FrankenPHP worker | callback y lifecycle de respuesta | referencias persistentes entre requests |
| RoadRunner | recepción/respuesta del worker y señales del supervisor | respuesta de error duplicada o worker desincronizado |
| OpenSwoole | scope por coroutine y clientes compatibles | mezcla de contexto y bloqueos globales |
| PHP tradicional | entrada/salida por proceso/request | depender de cleanup global en lugar de contrato |

La tabla es requisito de diseño, no afirmación sobre versiones actuales de esas APIs. Cada release del adapter fijará documentación y dependencias oficiales concretas.

## RoadRunner

El bridge traduce entrada a RuntimeOperation, preserva una única autoridad de envío y finaliza el scope incluso si el canal hacia supervisor falla. Error de protocolo o transporte del worker provoca reciclado, no envío de una Response HTTP a través de un canal roto. Jobs y HTTP abren scopes independientes y comparten solo catálogos inmutables.

Si el supervisor reentrega un trabajo después de terminar un worker, la idempotencia pertenece al Job. Un receipt accepted de Telemetry en memoria puede perderse al reiniciar. La implementación debe declarar exactamente qué drena y qué se descarta al shutdown.

## OpenSwoole

Contexto, depth guard y dedup se asocian a coroutine/Fiber, no al proceso. Un Throwable retenido en un singleton puede contaminar scopes concurrentes. Reporters síncronos bloqueantes solo se permiten cuando el adapter puede garantizar timeout y compatibilidad con el scheduler; en caso contrario usan exportación desacoplada.

Clientes de Database/logging deben ser coroutine-safe o exclusivos por operación. Un mutex no convierte una conexión transaccional compartida en una buena política de ownership. Reset global mientras otras coroutines siguen activas está prohibido; recursos de scope se limpian individualmente, y reciclado se coordina a nivel de worker.

## Contract suite común

AdapterTestKit debe verificar begin/context/finalize/close, doble finalización, aislamiento, streaming, cancelación, report failure y recycle. concurrentScopes=true añade interleavings deterministas y pruebas de carga; una implementación secuencial no puede anunciar esa capacidad por omisión.

## Política de adopción

Núcleo y protocolo permanecen iguales. Agregar runtime no cambia códigos, privacy ni mapping. La matriz pública incluye versiones, modo, extensiones, limitaciones y resultados reales. Hasta certificar, el adapter es experimental opt-in y FrankenPHP permanece predeterminado. La aplicación puede usar núcleo sin instalar ninguno de estos servidores.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](26_FRANKENPHP_DEFAULT_RUNTIME.md) · [Siguiente](28_HTTPKERNEL_ROUTING_AND_CONTROLLERS_INTEGRATION.md)
