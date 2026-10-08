# 09 — Mapping de dominio a transporte

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Semántica antes que transporte

Una excepción de dominio expresa una condición de negocio. `InsufficientBalance` no contiene Response, status ni plantilla. Un mapper la convierte a `billing.insufficient_balance`; el catálogo de transporte define cómo presentarla en cada entrada. El mismo código puede requerir una decisión de job terminal y una respuesta HTTP corregible por el usuario.

| Dominio/condición | HTTP estándar | SPA | CLI | Job |
|---|---|---|---|---|
| Saldo insuficiente | 409 | error local, sin replay automático | 5 | terminal de negocio |
| Datos inválidos | 422 | fields y foco accesible | 2 | terminal salvo política explícita |
| Registro ausente | 404 | boundary/página según operación | 4 | terminal o condición esperada |
| Conflicto de versión | 409/412 según precondición | reconciliar snapshot | 5 | releer antes de nueva decisión |
| Dependencia caída sin efecto | 503 | aviso temporal | 6 | consejo retry condicional |
| Resultado de mutación incierto | 503 | estado pendiente de conciliación | 6 | reconciliar, no replay ciego |

Las decisiones de negocio que no son excepcionales pueden devolverse como outcomes ordinarios; Exceptions no obliga a convertir todo resultado negativo en throw. Authorization conserva GRANT/DENY/CHALLENGE/FAILURE y su adaptador solo ingresa al pipeline cuando la frontera necesita manejar ese resultado como error.

## Política contextual

El bridge selecciona un perfil por ruta/comando/job registrado. Un endpoint que aplica If-Match puede mapear conflicto a 412; un conflicto de edición general usa 409. Falta de autenticación browser puede requerir navegación a login, mientras API devuelve challenge. El dominio permanece inalterado.

Solo el perfil de transporte puede elegir status o acción SPA, y siempre dentro de restricciones de seguridad. Ocultar una denegación con 404 cambia PublicError a `resource.not_found`, no el diagnóstico interno ni el evento de auditoría. El código público evita revelar que existe un recurso prohibido.

## Efectos y retry

Ni 503 ni retryAdvice=conditional autorizan repetir una operación. Deben coincidir efecto None, idempotencia verificada, presupuesto y política del dueño. Committed con fallo de render requiere consultar resultado, no repetir. Partial exige compensación o reconciliación de la unidad correspondiente; Exceptions no implementa compensaciones de negocio.

La respuesta puede dar un operationRef opaco para consultar estado si la aplicación ofrece endpoint autorizado. No se publica el identificador de una transacción o job de otro tenant. Un registro de idempotencia debe comparar fingerprint del comando para impedir reutilizar una clave con parámetros diferentes.

## Aceptación

Ejecutar el mismo escenario de saldo insuficiente en cuatro transportes: código semántico interno idéntico, salida adecuada y ningún import HTTP en dominio. Probar denial oculto, precondición fallida y timeout después de commit. La documentación del endpoint debe declarar si permite reconciliación y qué semántica de idempotencia garantiza.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](08_EXCEPTION_MAPPING_ENGINE.md) · [Siguiente](10_REPORTING_POLICY_AND_DEDUPLICATION.md)
