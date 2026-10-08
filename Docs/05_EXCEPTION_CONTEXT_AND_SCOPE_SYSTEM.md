# 05 — Contexto, scopes y aislamiento

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Contexto explícito

ExceptionContext es un snapshot inmutable construido por una frontera confiable. Contiene `scopeId`, `operationId`, `transport`, `environment`, `locale`, `deadlineMonotonic`, `requestId?`, `traceId?`, `spanId?`, `partitionRef?`, `principalRef?`, `routeName?`, `componentRef?`, `jobRef?`, `attempt`, `responseCommitted`, `effect`, `policyRevision` y `cancellationState`. Las referencias de identidad son internas y opcionales, nunca objetos de sesión, usuario o Request completos.

| Dato | Autoridad | Tratamiento |
|---|---|---|
| scope/operation/occurrence | Runtime y Exceptions | generados localmente, tamaño fijo |
| requestId | bridge HTTP | validar formato o generar otro |
| trace/span | Telemetry | contexto aceptado tras política de propagación |
| tenant/partition/principal | Authentication y resolución de tenancy | nunca header arbitrario como autoridad |
| route/component | router y árbol SPA del servidor | nombres del plan registrado |
| effect | dueño de operación | Unknown si no hay evidencia suficiente |
| deadline | Runtime | reloj monotónico para presupuesto |

Un requestId externo no se usa para autorización, deduplicación durable ni para abrir un expediente por sí solo. Tampoco se expone un traceId si la política de privacidad no permite correlación externa. El identificador público predeterminado es occurrenceId opaco y sin PII.

## Árbol de scopes

Request/Command/Job constituyen scopes raíz. Componentes y suboperaciones crean contextos hijos con parentOperationId, heredan deadline máximo y restricciones, y no amplían permisos. El ledger de ocurrencias se comparte únicamente dentro de esa raíz mediante un servicio scoped explícito. Los contextos hijos no prolongan la vida del request tras finalizarlo.

En async, capturar contexto significa copiar un subconjunto inmutable; no copiar Container ni conexiones. Una tarea durable reconstruye identidad y permisos al ejecutarse y abre otro scope. Una Fiber/coroutine obtiene almacenamiento local a su unidad de ejecución. Un único static “currentContext” está prohibido incluso si los requests parecen secuenciales hoy.

## Enriquecimiento

Enrichers registrados pueden añadir atributos con namespace, esquema y límite. Se ejecutan sin I/O por defecto y antes del saneamiento final. Fallar al enriquecer no cambia el código principal. Los campos reservados no son sobreescribibles. Valores fuera de rango se descartan y se registra un contador, sin incluir el valor descartado.

El cierre cancela tareas locales, libera ledger, traces y callbacks scoped, y borra referencias a objetos Throwable. Una operación que requiere streaming conserva scope hasta el cierre del stream bajo deadline; un callback after-response no puede retenerlo indefinidamente.

## Casos de aceptación

Alternar dos tenants con igual requestId aportado por cliente debe producir scopes y aislamiento distintos. Intercalar dos coroutines con distintos usuarios no mezcla etiquetas ni locale. Un job nacido del request A no reutiliza el principal A sin resolución autorizada. Un contexto expirado impide iniciar nuevos reporters de red, pero permite emitir una respuesta mínima y realizar cleanup acotado.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](04_EXCEPTION_MANAGER_AND_ORCHESTRATION.md) · [Siguiente](06_EXCEPTION_DESCRIPTOR_AND_ERROR_CATALOG.md)
