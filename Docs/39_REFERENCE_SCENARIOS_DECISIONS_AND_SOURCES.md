# 39 — Escenarios de referencia, decisiones y fuentes

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Escenarios de referencia completos

### A. Formulario SPA inválido

Authentication y Authorization permiten la operación; Validation produce violaciones sin ejecutar mutación. El bridge declara effect=None. Mapper selecciona validation.failed, policy omite log ordinario, métrica agrega ocurrencia, TransportMapper elige 422 y show_fields. El cliente verifica operación, navigation y component revision, muestra mensajes accesibles y conserva valores locales seguros. No hay retry automático ni datos privados en el envelope. Si el cliente ya navegó, descarta actualización visual obsoleta.

### B. Compra confirmada con respuesta perdida

Database confirma commit y outbox. Se pierde la conexión antes de entregar respuesta; el servidor conserva efecto Committed si lo conoce, mientras el cliente solo sabe que el resultado es incierto. La siguiente consulta autorizada al ledger por operationRef/idempotencyKey devuelve estado confirmado. El cliente reconcilia UI sin duplicar compra. Si el servidor también perdió confirmación del commit, conserva Unknown hasta conciliar. Un 503 por sí solo no permite reenvío.

### C. Widget opcional indisponible

Consulta de recomendaciones falla antes de producir efectos. La boundary más cercana permite fallback de lectura, reporta según policy y devuelve fragmento accesible. SSR puede conservar 200 cuando la ruta declara contenido opcional; una acción SPA fallida conserva su status 503. La falla del fallback asciende sin volver a invocar el mismo árbol. Permisos nunca se degradan a contenido privado cacheado.

### D. Job reentregado

El broker entrega un job cuyo ACK anterior se perdió. Jobs abre scope nuevo y verifica ledger/idempotencia. Si el efecto ya se confirmó, recupera resultado y ACK sin ejecutar mutación otra vez; si no puede probarlo, reconcilia. Un fallo técnico nuevo tiene occurrenceId propio ligado al mismo jobRef. El worker se reutiliza únicamente si reset pasa.

### E. Falta de permisos frente a motor de autorización caído

DENY válido puede dar 403 o 404 por política uniforme y mantiene auditoría. FAILURE por provider caído da fallo técnico seguro, fail-closed. El sistema no informa “usuario no tiene rol” cuando no pudo evaluar y no concede acceso por fallback. Debug público permanece deshabilitado.

## Registro de decisiones arquitectónicas

| ADR | Decisión | Motivo y coste |
|---|---|---|
| EX-ADR-01 | núcleo Quantum independiente | más bridges, menos acoplamiento |
| EX-ADR-02 | snapshot y proyección pública separados | más DTOs, menor exposición accidental |
| EX-ADR-03 | mapping de semántica antes de transporte | dominio reutilizable en Jobs/CLI |
| EX-ADR-04 | eventos observacionales | evita mutaciones ocultas de resultado |
| EX-ADR-05 | recovery solo consejo | dueño conserva responsabilidad de efectos |
| EX-ADR-06 | effect explícito en SPA | exige cliente consciente de incertidumbre |
| EX-ADR-07 | scopes y dedup locales | requiere integración real con runtimes |
| EX-ADR-08 | planes compilados por release | cambios requieren despliegue/drain |
| EX-ADR-09 | emergencia sin dependencias | menos detalle cuando el sistema está dañado |
| EX-ADR-10 | FrankenPHP primero, otros por suite | no prometer compatibilidad no probada |

## Fuentes del proyecto revisadas

Las rutas siguientes son referencias de procedencia, externas a este ZIP; no se incluyen ni modifican:

- Conversación `VoltStack-Exception`, id `6a963424-8ce4-83e8-8881-5080bd97f3ae`, recuperada completa disponible: intención Laravel/Symfony, descriptor y SPA.
- `VoltStack/16_AUTHORIZATION_FAILURE_ERROR_DENIAL_AND_EXCEPTION_HANDLING_SYSTEM.md`: GRANT/DENY/CHALLENGE/FAILURE, tipos y fail-closed.
- `VoltStack-Database-Documentation/28_TRANSACTION_AND_CONCURRENCY_SYSTEM.md`: ownership transaccional, unidad completa, rollback y outbox.
- `VoltStack-Database-Documentation/50_DATABASE_SYSTEM_INTEGRATION_AND_FINAL_ARCHITECTURE.md`: contexto inmutable, integración por contratos y coherencia de capas.
- `VoltStack-Cache-Documentation/00_CACHE_PROJECT_CONTEXT.md` y `40_CACHE_SYSTEM_INTEGRATION_AND_FINAL_ARCHITECTURE.md`: Platform, Quantum, módulos opcionales y arquitectura de referencia.
- `VoltStack-Cache-Documentation/25_CACHE_PERSISTENT_WORKERS_AND_RUNTIME_MANAGER_SERVER.md`: scopes y reset en procesos persistentes.
- `VoltStack-Cache-Documentation/31_CACHE_NATIVE_REACTIVE_SPA_AND_HTTP_INTEGRATION.md`: revisiones, reconexión, datos autorizados y stores privados.
- `VoltStack-Filesystem-Documentation/16_FAILURES_EXCEPTIONS_AND_RETRY_SYSTEM.md`: None/Committed/Partial/Unknown e idempotencia.
- `VoltStack-Cache-Documentation/appendices/E_SOURCES_DECISIONS_AND_GLOSSARY.md`: límites de evidencia sobre módulos no presentes.

## Fuentes técnicas externas

Consultadas el 4 de octubre de 2026. Se usan para inspiración o semántica externa; el resto de las decisiones es diseño propio. Referencias current pueden cambiar, por lo que las implementaciones deberán fijar versiones.

| Fuente oficial | Uso acotado |
|---|---|
| [Laravel 12 Error Handling](https://laravel.com/framework/docs/12.x/errors) | ergonomía de configuración y separación report/render; versión elegida por la conversación, no presentada como la más reciente |
| [Symfony HttpKernel](https://symfony.com/doc/current/components/http_kernel.html) | separación de kernel y manejo extensible de excepciones |
| [FrankenPHP worker mode](https://frankenphp.dev/docs/worker/) | persistencia de aplicación en memoria |
| [PHP set_error_handler](https://www.php.net/manual/en/function.set-error-handler.php) | límites de captura del handler de errores |
| [RFC 9457](https://www.rfc-editor.org/rfc/rfc9457.html) | Problem Details como representación API |
| [OpenTelemetry exception span conventions](https://opentelemetry.io/docs/specs/semconv/exceptions/exceptions-spans/) | bridge de observabilidad versionado |

## Glosario

**Ocurrencia:** hecho de fallo identificable, no grupo de errores similares. **Descriptor:** diagnóstico y semántica internos. **Proyección pública:** campos permitidos para un consumidor. **Boundary:** frontera que puede contener un fallo local. **Scope:** vida de una operación y sus recursos. **Efecto:** conocimiento sobre mutación realizada. **Receipt:** resultado de intento de reporte, no confirmación universal de entrega. **Plan:** configuración validada e inmutable. **Bridge:** adaptación hacia un sistema externo al núcleo. **Reconciliación:** consulta al dueño del estado para resolver incertidumbre. **Reciclado:** retirada controlada del worker, no reparación de la operación ya ejecutada.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](38_TESTING_CONTRACTS_AND_ACCEPTANCE_MATRIX.md) · [Siguiente](40_EXCEPTION_SYSTEM_INTEGRATION_AND_FINAL_ARCHITECTURE.md)
