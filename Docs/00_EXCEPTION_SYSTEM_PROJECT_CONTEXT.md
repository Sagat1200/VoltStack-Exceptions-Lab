# 00 — Contexto del proyecto e índice cerrado

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Alcance y condición de esta entrega

Esta colección especifica una implementación propia de `VoltStack\Quantum\Exceptions`: experiencia de aplicación sencilla, núcleo desacoplado, SPA reactiva nativa y operación segura en procesos persistentes. Entrega arquitectura, contratos, algoritmos, protocolos, configuración y criterios de verificación. No afirma que exista todavía una implementación PHP ni que las suites propuestas hayan sido ejecutadas.

El listado siguiente queda cerrado antes de escribir los capítulos: **41 documentos Markdown, del 00 al 40**, sin anexos pendientes ni entregas posteriores. `manifest.json` registra el inventario y `validation.json` registra verificaciones documentales; ambos son auxiliares, no documentos adicionales. El ZIP contiene la colección íntegra.

## Índice completo y cerrado

1. [00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md — Contexto del proyecto e índice cerrado](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md)
2. [01_EXCEPTION_SYSTEM_ARCHITECTURE.md — Arquitectura del sistema de excepciones](01_EXCEPTION_SYSTEM_ARCHITECTURE.md)
3. [02_PUBLIC_CONTRACTS_AND_DOMAIN_MODEL.md — Contratos públicos y modelo de dominio](02_PUBLIC_CONTRACTS_AND_DOMAIN_MODEL.md)
4. [03_EXCEPTION_LIFECYCLE_AND_PIPELINE.md — Ciclo de vida y pipeline](03_EXCEPTION_LIFECYCLE_AND_PIPELINE.md)
5. [04_EXCEPTION_MANAGER_AND_ORCHESTRATION.md — Exception Manager y orquestación](04_EXCEPTION_MANAGER_AND_ORCHESTRATION.md)
6. [05_EXCEPTION_CONTEXT_AND_SCOPE_SYSTEM.md — Contexto, scopes y aislamiento](05_EXCEPTION_CONTEXT_AND_SCOPE_SYSTEM.md)
7. [06_EXCEPTION_DESCRIPTOR_AND_ERROR_CATALOG.md — Descriptor y catálogo de errores](06_EXCEPTION_DESCRIPTOR_AND_ERROR_CATALOG.md)
8. [07_NORMALIZATION_AND_PHP_ERROR_CAPTURE.md — Normalización y captura de errores PHP](07_NORMALIZATION_AND_PHP_ERROR_CAPTURE.md)
9. [08_EXCEPTION_MAPPING_ENGINE.md — Motor de mapping de excepciones](08_EXCEPTION_MAPPING_ENGINE.md)
10. [09_DOMAIN_TO_TRANSPORT_MAPPING.md — Mapping de dominio a transporte](09_DOMAIN_TO_TRANSPORT_MAPPING.md)
11. [10_REPORTING_POLICY_AND_DEDUPLICATION.md — Política de reporting y deduplicación](10_REPORTING_POLICY_AND_DEDUPLICATION.md)
12. [11_REPORTER_PIPELINE_AND_DELIVERY.md — Pipeline de reporters y entrega](11_REPORTER_PIPELINE_AND_DELIVERY.md)
13. [12_RENDERING_NEGOTIATION_AND_RESPONSE_RESOLUTION.md — Negociación y resolución de presentación](12_RENDERING_NEGOTIATION_AND_RESPONSE_RESOLUTION.md)
14. [13_HTTP_EXCEPTIONS_AND_STATUS_SEMANTICS.md — Excepciones HTTP y semántica de estados](13_HTTP_EXCEPTIONS_AND_STATUS_SEMANTICS.md)
15. [14_HTML_RENDERING_AND_ERROR_PAGES.md — Renderizado HTML y páginas de error](14_HTML_RENDERING_AND_ERROR_PAGES.md)
16. [15_JSON_PROBLEM_DETAILS_AND_API_ERRORS.md — JSON, Problem Details y errores de API](15_JSON_PROBLEM_DETAILS_AND_API_ERRORS.md)
17. [16_NATIVE_SPA_EXCEPTION_PROTOCOL.md — Protocolo de excepciones de la SPA nativa](16_NATIVE_SPA_EXCEPTION_PROTOCOL.md)
18. [17_COMPONENT_EXCEPTION_BOUNDARIES.md — Boundaries de excepción en componentes](17_COMPONENT_EXCEPTION_BOUNDARIES.md)
19. [18_CLI_EXCEPTION_RENDERING_AND_EXIT_CODES.md — CLI, salida estructurada y códigos de salida](18_CLI_EXCEPTION_RENDERING_AND_EXIT_CODES.md)
20. [19_EXCEPTION_EVENTS_AND_EVENT_SYSTEM_INTEGRATION.md — Eventos e integración con Event System](19_EXCEPTION_EVENTS_AND_EVENT_SYSTEM_INTEGRATION.md)
21. [20_RECOVERY_RETRY_AND_EFFECT_SAFETY.md — Recovery, retries y seguridad de efectos](20_RECOVERY_RETRY_AND_EFFECT_SAFETY.md)
22. [21_SECURITY_AND_THREAT_MODEL.md — Seguridad y modelo de amenazas](21_SECURITY_AND_THREAT_MODEL.md)
23. [22_PRIVACY_REDACTION_AND_DATA_GOVERNANCE.md — Privacidad, redacción y gobierno de datos](22_PRIVACY_REDACTION_AND_DATA_GOVERNANCE.md)
24. [23_TELEMETRY_TRACING_METRICS_AND_CORRELATION.md — Telemetry, trazas, métricas y correlación](23_TELEMETRY_TRACING_METRICS_AND_CORRELATION.md)
25. [24_ASYNC_JOBS_QUEUES_AND_CANCELLATION.md — Async, Jobs, Queues y cancelación](24_ASYNC_JOBS_QUEUES_AND_CANCELLATION.md)
26. [25_PERSISTENT_RUNTIME_LIFECYCLE_AND_ISOLATION.md — Ciclo de vida en runtimes persistentes](25_PERSISTENT_RUNTIME_LIFECYCLE_AND_ISOLATION.md)
27. [26_FRANKENPHP_DEFAULT_RUNTIME.md — FrankenPHP como runtime predeterminado](26_FRANKENPHP_DEFAULT_RUNTIME.md)
28. [27_ROADRUNNER_OPENSWOOLE_AND_RUNTIME_ADAPTERS.md — Adaptadores RoadRunner y OpenSwoole](27_ROADRUNNER_OPENSWOOLE_AND_RUNTIME_ADAPTERS.md)
29. [28_HTTPKERNEL_ROUTING_AND_CONTROLLERS_INTEGRATION.md — Integración con HTTPKernel, Routing y Controllers](28_HTTPKERNEL_ROUTING_AND_CONTROLLERS_INTEGRATION.md)
30. [29_AUTHENTICATION_AUTHORIZATION_AND_VALIDATION_INTEGRATION.md — Authentication, Authorization y Validation](29_AUTHENTICATION_AUTHORIZATION_AND_VALIDATION_INTEGRATION.md)
31. [30_DATABASE_TRANSACTIONS_AND_INFRASTRUCTURE_INTEGRATION.md — Database, transacciones e infraestructura](30_DATABASE_TRANSACTIONS_AND_INFRASTRUCTURE_INTEGRATION.md)
32. [31_CONFIGURATION_REFERENCE_AND_VALIDATION.md — Referencia de configuración y validación](31_CONFIGURATION_REFERENCE_AND_VALIDATION.md)
33. [32_CONTAINER_BOOTSTRAP_AND_PLATFORM_COMPOSITION.md — Container, bootstrap y composición Platform](32_CONTAINER_BOOTSTRAP_AND_PLATFORM_COMPOSITION.md)
34. [33_DEVELOPER_EXPERIENCE_AND_DEBUGGING.md — Experiencia de desarrollo y debugging](33_DEVELOPER_EXPERIENCE_AND_DEBUGGING.md)
35. [34_PRODUCTION_BEHAVIOR_AND_OPERATIONAL_RUNBOOKS.md — Producción y procedimientos operativos](34_PRODUCTION_BEHAVIOR_AND_OPERATIONAL_RUNBOOKS.md)
36. [35_PERFORMANCE_AND_RESOURCE_GOVERNANCE.md — Performance y gobierno de recursos](35_PERFORMANCE_AND_RESOURCE_GOVERNANCE.md)
37. [36_CACHE_COMPILATION_AND_DEPLOYMENT.md — Cache, compilación y despliegue](36_CACHE_COMPILATION_AND_DEPLOYMENT.md)
38. [37_EXTENSIBILITY_VERSIONING_AND_INTEROPERABILITY.md — Extensibilidad, versionado e interoperabilidad](37_EXTENSIBILITY_VERSIONING_AND_INTEROPERABILITY.md)
39. [38_TESTING_CONTRACTS_AND_ACCEPTANCE_MATRIX.md — Testing, contratos y matriz de aceptación](38_TESTING_CONTRACTS_AND_ACCEPTANCE_MATRIX.md)
40. [39_REFERENCE_SCENARIOS_DECISIONS_AND_SOURCES.md — Escenarios de referencia, decisiones y fuentes](39_REFERENCE_SCENARIOS_DECISIONS_AND_SOURCES.md)
41. [40_EXCEPTION_SYSTEM_INTEGRATION_AND_FINAL_ARCHITECTURE.md — Integración del sistema y arquitectura final](40_EXCEPTION_SYSTEM_INTEGRATION_AND_FINAL_ARCHITECTURE.md)

## Evidencia y coherencia con el proyecto

Se recuperó la conversación “VoltStack-Exception” completa disponible. Se revisaron las colecciones locales Database, Authorization, Cache y Filesystem, especialmente transacciones, estados de autorización, fallos con efectos inciertos, contexto persistente y SPA/cache. `sources/` no contenía archivos visibles en esta revisión y permanece intacto. Las referencias previas no se modifican.

Platform compone módulos; Quantum contiene capacidades independientes. El namespace PHP adoptado es `VoltStack\Quantum\Exceptions`; el nombre lógico es `VoltStack/Quantum/Exceptions`. Los nombres de paquetes Composer de este diseño son propuestas. El núcleo no depende de HTTP, SPA, Database, Jobs, Telemetry ni de un contenedor concreto. FrankenPHP es el primer objetivo de certificación; RoadRunner y OpenSwoole requieren adaptadores y certificación posterior.

No estuvieron disponibles contratos completos de SPA, Telemetry, Event System, HTTPKernel, Routing, Controllers, Authentication, Validation, Jobs ni RuntimeManagerServer. Los puertos hacia ellos se especifican aquí como **propuestos**, sujetos a adaptación por contrato; no se inventa compatibilidad binaria. La alineación verificable con Database, Authorization, Cache y Filesystem se detalla en 39 y 40.

## Autoridad normativa

DEBE y NO DEBE expresan obligaciones; DEBERÍA exige justificación para desviarse; PUEDE es opcional. El documento 02 fija contratos, 06 el modelo semántico, 16 el protocolo SPA y 31 la configuración. Los restantes desarrollan comportamiento sin redefinirlos. Un conflicto debe corregirse antes de publicar una implementación. PHP 8.2 es la base sintáctica de los ejemplos, no una matriz de soporte certificada. Los ejemplos son especificación de APIs propuestas, no código de una librería instalada.

## Decisiones fundacionales

1. Un Throwable conserva su causa; el descriptor separa diagnóstico interno y datos públicos.
2. Reporting y rendering son independientes, con una sola autoridad que finaliza el transporte.
3. Mapping semántico precede a mapping de transporte; dominio no conoce HTTP.
4. Un fallo no demuestra ausencia de efectos; None, Committed, Partial y Unknown son distintos.
5. Recuperación nunca concede permisos, revierte commits ni repite automáticamente mutaciones.
6. Scopes y deduplicación son locales a una operación; ningún singleton retiene request o identidad.
7. Eventos observan el pipeline; las decisiones críticas usan puertos tipados y orden determinista.
8. Producción expone códigos estables y mensajes aprobados; debug no se activa desde la petición.
9. Toda colección, extensión, exportación y espera tiene límites explícitos.
10. La ruta de emergencia funciona sin Container, plantillas, Database ni red.

## Orden recomendado de implementación

Construir 02–09 y las invariantes de 03; añadir reporting y transportes 10–18; integrar seguridad y aislamiento 19–32; certificar operación y pruebas 33–38. El 40 fija los criterios para declarar terminada la implementación. La documentación está completa independientemente de ese trabajo futuro.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Siguiente](01_EXCEPTION_SYSTEM_ARCHITECTURE.md)
