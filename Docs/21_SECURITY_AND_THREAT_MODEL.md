# 21 — Seguridad y modelo de amenazas

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Modelo de amenazas

El atacante puede controlar headers, parámetros, cargas de validación, nombres de archivo y mensajes provenientes de servicios externos. También puede provocar errores repetidos, negociar formatos y explotar respuestas tardías. Los plugins instalados son código confiable con responsabilidad operativa; sus puertos limitan daños accidentales, no constituyen un sandbox contra PHP malicioso.

| Amenaza | Control obligatorio | Prueba negativa |
|---|---|---|
| Filtración por stack/SQL/secretos | proyección pública por allowlist | secretos señuelo ausentes en todos los outputs |
| XSS o inyección de terminal | escaping por contexto y eliminación de controles | payload HTML/ANSI literal sin ejecución |
| Header injection | parsers y rechazo CR/LF | Throwable con header hostil |
| Enumeración de recursos | política uniforme 403/404 y mensajes | objeto ajeno y ausente indistinguibles según perfil |
| Confusión tenant/usuario | contextos confiables y scopes aislados | headers falsos no cambian principal |
| Retry de pago | effect y owner idempotency | timeout tras commit no duplica |
| SSRF por reporter/redirect | endpoints de configuración y rutas allowlisted | URL arbitraria rechazada |
| Denegación de servicio | límites de causas, campos, tiempos y buffers | tormenta con memoria acotada |

## Debug y exposición

Producción deshabilita debug por configuración de despliegue. Query string, cookie, IP enviada por proxy no confiable o header no lo activan. Incluso en desarrollo, el renderer de diagnóstico redacta secretos y args de stack. Un portal de incidentes remoto requiere autenticación/autorización separadas y auditoría; occurrenceId no es credencial de acceso.

Errores de Authentication/Authorization no degradan a éxito. Si el motor de permisos está caído, el bridge conserva fallo técnico fail-closed. Un plugin que solicita fallback no puede sobreescribir esa restricción. Proteger rutas de login y recuperación contra bucles de redirect y reenvío de mutaciones.

## Fronteras de serialización

No usar unserialize de datos no confiables para DTOs de error. El protocolo SPA no contiene código ejecutable ni instrucciones arbitrarias del servidor para invocar métodos del cliente. Los catálogos/planes se producen por el despliegue confiable, se verifican antes de cargar y no están en directorios escribibles por uploads.

Los mensajes de terceros se consideran datos hostiles, incluidos “instrucciones” en contenido remoto. No se ejecutan ni se convierten en plantillas. Un sanitizer fallido elimina datos, no permite pasarlos sin filtrar.

## Revisión de seguridad

La aceptación requiere fuzzing de headers, Accept y envelopes; pruebas de aislamiento en workers; secretos señuelo en causas anidadas; abuso de cardinalidad; y pérdida de respuesta tras efectos. Documentar qué endpoints ocultan existencia y mantener política consistente entre HTML, JSON, SPA y CLI. Los reportes internos también se protegen con permisos y retención: ser internos no hace inocua una filtración.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](20_RECOVERY_RETRY_AND_EFFECT_SAFETY.md) · [Siguiente](22_PRIVACY_REDACTION_AND_DATA_GOVERNANCE.md)
