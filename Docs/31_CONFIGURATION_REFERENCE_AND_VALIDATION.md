# 31 — Referencia de configuración y validación

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Configuración canónica v1

El provider consume un único árbol `exceptions`. Configuración desconocida falla durante compilación; no se ignora una clave mal escrita. Los valores siguientes son defaults propuestos y forman parte del contrato documental. Un deployment profile puede reducir límites; elevarlos exige revisar memoria, privacidad y tiempos.

```php
return [
    'schema_version' => 1,
    'environment' => 'production',
    'debug' => false,
    'default_locale' => 'es',
    'fallback_locale' => 'en',
    'runtime' => 'frankenphp',
    'limits' => [
        'handling_depth' => 2,
        'causes' => 8,
        'frames_per_cause' => 32,
        'message_bytes' => 2048,
        'snapshot_bytes' => 32768,
        'attributes' => 32,
        'attribute_depth' => 4,
        'occurrences_per_scope' => 128,
        'mapping_candidates' => 64,
        'field_errors' => 100,
        'output_bytes' => 65536,
        'header_bytes' => 8192,
    ],
    'reporting' => [
        'enabled' => true,
        'sync_budget_ms' => 50,
        'sample_rate' => 1.0,
        'buffer_records' => 256,
        'buffer_bytes' => 2097152,
        'buffer_ttl_seconds' => 60,
        'reporters' => ['exceptions.log'],
        'ignore_codes' => ['validation.failed', 'resource.not_found',
            'authentication.required', 'authorization.denied',
            'authorization.challenge'],
    ],
    'rendering' => [
        'api_format' => 'problem_json',
        'browser_format' => 'html',
        'spa_versions' => [1],
        'cache_control' => 'no-store',
    ],
    'recovery' => ['automatic_replay' => false],
    'privacy' => [
        'capture_arguments' => false,
        'capture_request_body' => false,
        'public_trace_id' => false,
        'diagnostic_retention_days' => 7,
        'aggregate_retention_days' => 30,
    ],
    'compilation' => ['required_in_production' => true],
    'rules' => [],
];
```

## Validación por grupo

| Claves | Tipo/rango | Regla |
|---|---|---|
| schema_version | entero, exactamente 1 | otra versión exige migración |
| environment | development/test/staging/production | staging usa privacidad de producción por defecto |
| debug | booleano estricto | true prohibido en production; cadenas no se convierten por truthiness |
| locales | strings de catálogo registrado | fallback debe existir |
| runtime | id de adapter instalado | frankenphp por defecto; no auto-instala nada |
| límites numéricos | enteros positivos | techo de seguridad del provider; 0 no significa ilimitado |
| sample_rate | número 0–1 | no altera audit ni conteo agregado |
| budget y TTL | enteros positivos | no pueden superar presupuesto operativo padre |
| reporters | ids únicos registrados | red síncrona exige timeout real |
| ignore_codes | códigos del catálogo | no suprime audit requerido |
| spa_versions | subconjunto no vacío de versiones instaladas | v1 disponible en bridge v1 |
| cache_control | `no-store` en perfil base | no configurable por request |
| automatic_replay | false en v1 | true se rechaza; propietario decide retries |
| captura sensible | false en perfil base | no hay opt-in general para secretos |
| retention_days | entero positivo | política específica de destino puede reducirla |
| rules | lista de RuleDefinition | esquema descrito debajo |

Techos del provider v1: handling_depth≤4, causes≤16, frames_per_cause≤128, message_bytes≤8192, snapshot_bytes≤131072, attributes≤128, attribute_depth≤8, occurrences_per_scope≤1024, mapping_candidates≤256, field_errors≤100 y output_bytes≤65536 para SPA v1. Otros renderers pueden tener presupuesto separado en una versión futura; v1 usa el mismo máximo. header_bytes≤16384. El servidor HTTP puede aplicar un máximo menor.

## Reglas y builder

RuleDefinition contiene id único, exceptionType cargable, priority entero -1000..1000, serviceId registrado y exclusive booleano. Predicados opcionales son serviceIds de implementaciones puras, no texto PHP de usuario. `rules->map(id, exceptionType, serviceId, priority=0)` de 08 construye esa definición con exclusive=false. Builders adicionales de DX se compilan al mismo árbol; no añaden configuración de runtime por request.

Orden de composición: defaults del paquete → providers instalados → configuración de aplicación → perfil de despliegue. El compilador rechaza duplicados ambiguos y muestra sobrescrituras explícitas. Variables de entorno se parsean con tipos y defaults definidos por Config; no se consultan cada vez que se lanza una excepción.

## Secretos y despliegue

Tokens y DSNs pertenecen a servicios del exporter y no al árbol compilado. El hash del plan incorpora referencias/versiones de política, no secretos. Cambios de privacidad, reglas y locale requieren nueva revisión y drain de workers. Un tenant solo puede seleccionar perfiles aprobados; no inyectar reglas PHP o activar debug.

## Pruebas de configuración

Validar árbol anterior, clave desconocida, booleano string incorrecto, versión SPA no instalada, reporter faltante, duplicados y production debug=true. Verificar que la salida de comandos de diagnóstico oculta credenciales de servicios enlazados y que un fallo de configuración se presenta mediante bootstrap mínimo sin intentar cargar el plan inválido.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](30_DATABASE_TRANSACTIONS_AND_INFRASTRUCTURE_INTEGRATION.md) · [Siguiente](32_CONTAINER_BOOTSTRAP_AND_PLATFORM_COMPOSITION.md)
