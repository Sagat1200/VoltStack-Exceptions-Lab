# 06 — Descriptor y catálogo de errores

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Descriptor semántico

ExceptionDescriptor reúne un FailureSnapshot interno y un SemanticError estable. No es un modelo de persistencia de entidades ni un Throwable serializado. Se construye una sola vez por clasificación y se sella con policyRevision. Una remap explícita produce un nuevo descriptor con registro de regla aplicada; no muta retroactivamente reportes enviados.

| Campo semántico | Dominio de valores |
|---|---|
| code | ASCII minúsculo `^[a-z][a-z0-9_.]{0,95}$`, estable y registrado |
| category | validation, authentication, authorization, not_found, conflict, throttled, dependency, configuration, cancelled, internal |
| messageKey | clave del catálogo de traducciones aprobado |
| safeParameters | allowlist por código; sin input reflejado por defecto |
| severity | debug, info, notice, warning, error, critical |
| effect | None, Committed, Partial, Unknown |
| retryAdvice | never, conditional, reconcile_first |

El catálogo guarda también `publicDefault`, `reportClass`, `httpDefault`, `cliDefault` y políticas de campos. Los defaults de transporte son metadatos de integración, no obligaciones para la excepción de dominio. `Throwable::getCode()` se conserva como diagnóstico y jamás se interpreta automáticamente como status HTTP.

## Catálogo base

| Código | Categoría | HTTP de perfil estándar | CLI | Reporting ordinario |
|---|---|---:|---:|---|
| `validation.failed` | validation | 422 | 2 | omitido |
| `authentication.required` | authentication | 401 con challenge válido | 3 | omitido |
| `authorization.denied` | authorization | 403, o 404 por política | 3 | omitido |
| `authorization.challenge` | authorization | 403 + acción aprobada | 3 | omitido |
| `resource.not_found` | not_found | 404 | 4 | omitido |
| `resource.conflict` | conflict | 409 | 5 | info |
| `request.throttled` | throttled | 429 | 6 | muestreado |
| `dependency.unavailable` | dependency | 503 | 6 | warning/error |
| `operation.indeterminate` | internal | 503 | 6 | error |
| `operation.cancelled` | cancelled | ver política 13 | 130 si SIGINT | info |
| `configuration.invalid` | configuration | 500 | 78 | critical |
| `internal.error` | internal | 500 | 1 | error |

Los mensajes públicos de internal/configuration/dependency son genéricos. `operation.indeterminate` informa que no se pudo confirmar el resultado y ofrece consulta autorizada cuando exista, nunca “no se guardó”. Código y categoría no dependen del idioma.

## Diagnóstico, cadenas y agrupación

FailureSnapshot contiene clase, mensaje redactado, frames sin argumentos, causas acotadas y marcas de truncamiento. El descriptor puede incluir `originCode` interno diferente al código público cuando se oculta un denial como 404. Cause traversal usa identidad de objetos y límite de profundidad; no se serializan objetos internos ni propiedades privadas por reflexión.

El fingerprint usa versión, código, clase normalizada y ubicación lógica estable; excluye mensajes variables, IDs y números de línea volátiles si la política de despliegue así lo exige. Fingerprint agrupa diagnósticos; occurrenceId distingue hechos. Dos errores iguales en dos operaciones no comparten ocurrencia.

## Reglas de evolución y prueba

Un código publicado no cambia de significado silenciosamente. Se puede añadir traducción o metadata opcional, pero retirar código exige deprecación. Probar desconocidos→internal.error, todos los códigos con traducción fallback, phpCode=404 sin efecto en status, datos sensibles en previous y preservación de Unknown a través de todos los transportes.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](05_EXCEPTION_CONTEXT_AND_SCOPE_SYSTEM.md) · [Siguiente](07_NORMALIZATION_AND_PHP_ERROR_CAPTURE.md)
