# 35 — Performance y gobierno de recursos

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Presupuesto predecible

El camino sin excepción no normaliza stacks ni crea descriptors. Solo aporta un catch de frontera y contexto que ya necesita la operación. El camino de error es acotado en profundidad, datos y tiempo. Optimizar no puede omitir redacción, aislamiento ni validación de headers.

Sea C número de causas, F frames por causa, A atributos acotados y R reglas candidatas. Normalización debe ser O(C·F+A) respecto del input retenido; mapping O(R) después de resolver el índice de clase. No se permite recorrer ilimitadamente el grafo de propiedades del Throwable ni serializar entidades. El límite de bytes corta antes de construir una copia enorme.

## Memoria y tiempo

Defaults de 31: snapshot 32 KiB, salida 64 KiB, 128 ocurrencias por scope, buffer de proceso 2 MiB/256 registros. El máximo teórico no debe reservarse completo por cada operación; se asigna bajo demanda. Saturar el ledger activa emergencia/registro agregado sin retener nuevos objetos completos. Nunca mantener una lista no acotada de errores “para debugging”.

Reporting síncrono comparte 50 ms de presupuesto propuesto; cada cliente debe soportar timeout real. Medir después de una llamada bloqueada no implementa deadline. Source frames, symbolication y upload de reportes pesados se excluyen del camino síncrono. Privacy continúa antes de exportación diferida.

## Método de benchmark

Comparar baseline sin Exceptions con catch instalado, excepción esperada ignorada, internal.error con log local, renderer JSON/HTML/SPA, cadena máxima y exporter caído. Separar cold bootstrap, warm workers y saturación. Medir p50/p95/p99, CPU, asignaciones, memoria retenida tras GC, bytes de logs, drops y throughput útil.

Usar carga representativa y proporciones 0%, 1%, 10% y tormenta de errores. Repetir con identidades alternadas y concurrencia real del adapter. Registrar hardware, PHP, extensiones, runtime, plan hash, tamaño de datos y número de iteraciones. Los resultados se comparan con presupuesto del servicio; esta colección no contiene benchmarks ejecutados.

## Control de cardinalidad y carga

Índices de mapping por clase tienen tamaño máximo y versión; clases dinámicas no pueden producir cache infinito. Fingerprints se agregan bajo límite. Fallbacks de rendering no consultan red. Reporters usan buffers con backpressure/drop explícito en lugar de bloquear toda la aplicación.

Un error por request con cadenas máximas puede ser un ataque; límites del HTTP server, admission control y rate limiting actúan antes cuando sea posible. Exceptions gestiona el residual y no sustituye esos controles.

## Puertas de aceptación

Cero fuga entre scopes y tendencia de memoria retenida estable tras warmup; no crecimiento proporcional al número histórico de requests. El overhead sin error y la latencia de manejo deben quedar dentro del presupuesto acordado antes de release. Un cambio de optimización requiere comparar outputs de privacidad y semántica, no solo tiempo total.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](34_PRODUCTION_BEHAVIOR_AND_OPERATIONAL_RUNBOOKS.md) · [Siguiente](36_CACHE_COMPILATION_AND_DEPLOYMENT.md)
