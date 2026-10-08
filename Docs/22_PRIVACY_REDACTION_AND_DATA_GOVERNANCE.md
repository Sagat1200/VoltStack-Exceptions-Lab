# 22 — Privacidad, redacción y gobierno de datos

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Clasificación y destinos

Los datos se clasifican en público aprobado, diagnóstico restringido y secreto prohibido. PublicError solo usa la primera clase. ReportRecord puede incluir diagnóstico restringido bajo política del destino; ningún destino recibe secretos por default. El Throwable bruto no se persiste como atajo de debugging.

| Dato | Política predeterminada |
|---|---|
| password, tokens, cookies, authorization headers, claves | eliminar, sin hash reutilizable |
| request body y query string | no capturar; allowlist de metadata necesaria |
| SQL y bindings | query fingerprint/operación; sin bindings ni SQL literal |
| email, IP, user id, tenant id | omitir o pseudonimizar según necesidad aprobada |
| stack args y propiedades de entidades | nunca capturar automáticamente |
| rutas locales | ruta lógica relativa; sin directorio personal |
| código semántico y severidad | conservar |
| occurrenceId | conservar con acceso a búsqueda restringido |

## Redacción por etapas

Normalización redacta antes de crear snapshot para extensiones. El mapper aporta parámetros públicos solo según esquema del catálogo. Un PublicProjector construye un objeto nuevo; no elimina unas pocas claves de un mapa privado esperando que lo restante sea seguro. Cada reporter aplica además política del destino, por ejemplo no enviar identifiers pseudónimos a un proveedor externo.

Los redactors operan con límite de bytes, profundidad y tiempo. Expresiones regulares deben evitar backtracking no acotado. Una cadena imposible de sanear se reemplaza completa. Keys anidadas, variantes de capitalización y objetos serializables no evaden la política. La marca `[REDACTED]` no incorpora prefijos o longitudes que faciliten reconstruir un secreto.

Pseudonimización con HMAC usa clave y dominio de uso separados; no se usa hash simple para emails predecibles. Rotar claves reduce correlación histórica según política. Pseudonimizar no convierte automáticamente un dato en anónimo ni elimina obligaciones organizacionales.

## Retención y acceso

El perfil operativo propuesto retiene reportes diagnósticos 7 días, agregados 30 y buffers en memoria como máximo 60 segundos; son defaults técnicos ajustables, no asesoría ni requisitos legales. Auditoría tiene política independiente del módulo propietario. Registrar destino, finalidad, permisos de consulta y borrado también para backups/exportaciones.

Consultas por occurrenceId requieren autenticación y scope de tenant cuando corresponda. No devolver “incidente encontrado” públicamente si eso permite enumeración. Descargar un informe de soporte exige redacción adicional y registro de acceso; no adjuntar `.env`, dumps completos ni payloads de jobs.

## Verificación

Inyectar un mismo secreto en mensaje, causa, header, field, SQL y metadata; buscarlo en bytes de logs, eventos, trazas, métricas, buffers y respuestas. Verificar borrado por retención y que el siguiente request no conserva datos. Probar redactor que lanza: se conserva solo diagnóstico fijo y no raw input. Las pruebas usan datos sintéticos, no datos reales de clientes.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](21_SECURITY_AND_THREAT_MODEL.md) · [Siguiente](23_TELEMETRY_TRACING_METRICS_AND_CORRELATION.md)
