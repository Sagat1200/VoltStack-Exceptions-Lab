# 20 — Recovery, retries y seguridad de efectos

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Recovery es una decisión, no una promesa

RecoveryPolicy produce consejo tipado para el propietario de la unidad. No vuelve a invocar un controller o callable arbitrario desde un catch global. El dueño conoce transacción, idempotencia, cancelación y consecuencias externas. Exceptions no implementa rollback, compensación ni exactly-once.

| Efecto observado | Acción permitida por defecto |
|---|---|
| None | retry condicional si error transitorio, unidad idempotente y presupuesto |
| Committed | recuperar/consultar resultado; no repetir comando |
| Partial | reconciliar o compensar mediante workflow explícito |
| Unknown | reconcile_first; nunca replay ciego |

TypeError, error de configuración, validación, permisos e integridad no se vuelven transitorios por configurarlos como retryables. Un propietario puede conocer una excepción de concurrencia recuperable específica y repetir toda la transacción tras rollback confirmado.

## Algoritmo de retry del propietario

Comprobar cancelación y deadline; confirmar efecto None o garantía de idempotencia certificada que cubra la incertidumbre; verificar código allowlisted; calcular backoff con full jitter: `random(0, min(cap, base * 2^(attempt-1)))`; combinar con Retry-After cuando sea válido y no exceda deadline; reservar presupuesto global de intentos; ejecutar nuevamente la unidad completa con contexto de intento nuevo.

Máximo propuesto: tres intentos incluyendo inicial, base 100 ms, cap 2 s, siempre limitado por deadline. Valores pertenecen al dueño Database/Jobs, no a defaults ocultos del manager. No se acumulan tres retries de HTTP × tres de Database × tres de SDK: un RetryBudget compartido o una política que deshabilite capas internas debe acotar amplificación.

## Fallback y circuit breaker

Fallback explícito solo para información degradable. Una consulta de recomendaciones puede omitirse; un resultado de autorización no puede sustituirse por GRANT. Datos cacheados deben cumplir la política del módulo Cache; Exceptions no autoriza stale sensible. Circuit breaker pertenece al cliente de dependencia, distingue rechazo de admisión de fallo ejecutado y aporta efecto correspondiente.

Un fallback fallido produce causa secundaria, asciende o termina; no alterna infinitamente entre proveedores. La respuesta pública indica degradación cuando afecte decisiones del usuario. Las compensaciones son operaciones nuevas, auditables, que también pueden fallar; nunca se presentan como deshacer universal.

## Efecto incierto y reconciliación

El dueño guarda operationRef/idempotencyKey y fingerprint con aislamiento de tenant. Consultar ese ledger o proveedor puede determinar Committed/None; mientras tanto conserva Unknown. Si no existe mecanismo, el resultado requiere intervención o instrucción de soporte, no repetición automática. Un helper `rescue()` solo devuelve fallback explícito; no inventa None ni silencia errores críticos.

## Pruebas

Inyectar pérdida de respuesta después de commit, rollback fallido, agotamiento de budget, Retry-After enorme y cancelación durante espera. Contar efectos reales, no solo invocaciones. Verificar que cada intento tiene identidad propia correlacionada y que no se inicia un intento luego del deadline. Una prueba de retry pasa solo si la garantía del propietario cubre todos los efectos de la unidad.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](19_EXCEPTION_EVENTS_AND_EVENT_SYSTEM_INTEGRATION.md) · [Siguiente](21_SECURITY_AND_THREAT_MODEL.md)
