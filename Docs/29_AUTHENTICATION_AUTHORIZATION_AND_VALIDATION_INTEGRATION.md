# 29 — Authentication, Authorization y Validation

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Authentication

Falta de credenciales válidas produce `authentication.required`; credenciales rechazadas usan mensaje público genérico y no revelan si existe la cuenta. API 401 requiere challenge configurado; browser puede solicitar login por una ruta registrada; SPA devuelve action=authenticate con status del perfil. Un provider caído es dependency/internal failure, no “contraseña incorrecta”.

La excepción no conserva contraseña, cookie ni token para depurar. Una sesión expirada no autoriza repetir la mutación automáticamente después de login. Authentication reconstruye el contexto y la aplicación verifica idempotencia antes de una acción nueva.

## Authorization: preservar cuatro outcomes

| Outcome del sistema existente | Semántica en Exceptions |
|---|---|
| GRANT | continúa; no entra al pipeline |
| DENY | enforcement puede lanzar AuthorizationDeniedException; código privado preciso, público aprobado |
| CHALLENGE | `authorization.challenge`; requiere paso adicional autorizado |
| FAILURE | fallo técnico/configuración/contexto; fail-closed, nunca se transforma en GRANT |

Las referencias locales distinguen AuthorizationConfigurationException, ContextException, ExecutionException, InfrastructureException, ExternalEvaluatorException y AuditException. El bridge adapta esos tipos por regla explícita. Los códigos internos `rbac.permission_missing`, `tenant.mismatch` y `authorization.failure.*` pueden conservarse en diagnóstico aprobado; no todos se exponen al usuario.

Tenant ausente cuando es obligatorio es fallo de contexto; tenant incompatible es denial. MFA insuficiente puede ser challenge; proveedor MFA caído es failure. Una Ability desconocida en strict mode es configuración inválida. La presentación no aplana todos esos casos a 403. Política de ocultación puede usar 404 para denials sin alterar auditoría real.

## Validation

Validation aporta lista estructurada de violaciones con path, ruleCode, messageKey y parámetros públicos allowlisted. Exceptions no vuelve a validar entrada ni infiere fields a partir de texto. Máximo 100 errores; rutas de campos permitidas; truncamiento explícito y resumen. Password/token y archivos nunca se reflejan como valor fallido.

API usa 422; SPA show_fields; CLI 2. Browser puede usar 422 o PRG gestionado por bridge de formularios, con almacenamiento temporal autorizado de datos seguros. Error en definición de regla o validator que lanza inesperadamente es internal/configuration, no error corregible del usuario.

## Auditoría y privacidad

Audit pertenece a Authorization/Authentication. dontReport no lo desactiva. Si una operación requiere evidencia durable y el canal de auditoría falla, el módulo propietario determina el bloqueo antes de continuar; el listener de Exceptions no sustituye esa decisión. El error final evita exponer políticas, roles internos o proveedor caído a un atacante.

## Casos de aceptación

Probar DENY frente a FAILURE, MFA challenge frente a outage, tenant ausente frente a ajeno, validator defectuoso y exceso de fields. Comparar salidas de HTML/JSON/SPA: ninguna concede acceso ni filtra recursos. Verificar redacción en reporting y que logout invalida el estado privado del cliente antes de mostrar errores nuevos.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](28_HTTPKERNEL_ROUTING_AND_CONTROLLERS_INTEGRATION.md) · [Siguiente](30_DATABASE_TRANSACTIONS_AND_INFRASTRUCTURE_INTEGRATION.md)
