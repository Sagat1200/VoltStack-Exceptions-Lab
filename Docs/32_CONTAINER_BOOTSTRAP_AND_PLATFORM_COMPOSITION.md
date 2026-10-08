# 32 — Container, bootstrap y composición Platform

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Ensamblado por Platform

Platform registra ExceptionServiceProvider propuesto. El provider añade contratos y factories al Container del proyecto mediante adapter; no requiere una implementación específica del contenedor dentro del núcleo. El bootstrap debe poder fallar antes de completar Container: por eso EmergencyHandler se construye primero sin servicios de aplicación.

Orden obligatorio: autoload mínimo → emergency sink → captura PHP inicial → Config validada → catálogo y reglas → plan compilado → bindings y scopes → bridges de transportes/runtime → readiness. Un error de compilación aborta arranque con diagnóstico seguro. Nunca se anuncia ready con un plan parcialmente cargado.

## Tabla de bindings

| Contrato/servicio | Lifetime | Implementación base |
|---|---|---|
| ExceptionManagerInterface | scope | ExceptionManager |
| ExceptionContext | scope/inmutable | factory del runtime |
| OccurrenceRegistry | scope | registro acotado |
| ExceptionNormalizerInterface | singleton sin estado | BoundedNormalizer |
| MappingPlan / ErrorCatalog | singleton inmutable | plan compilado |
| TransportMapperInterface | por bridge, sin estado | ProfileTransportMapper |
| ExceptionRendererInterface | por target, sin estado | HTML/JSON/SPA/CLI |
| ReportCoordinator | scope | políticas y receipts locales |
| ExceptionReporterInterface | singleton solo si sin identidad retenida | logger/telemetry adapters |
| ExceptionEventPort | singleton seguro | null o EventSystemBridge |
| EmergencySinkInterface | proceso | salida mínima acotada |

Una factory singleton no captura Container scoped al construirse. Los servicios externos que requieren contexto lo reciben como DTO por llamada. Compartir logger no significa compartir un array mutable de “contexto actual”.

## Dependencias opcionales

Sin Telemetry se instala null bridge y se conserva reporting configurado. Sin Events se omiten notificaciones. Sin HTTP, el núcleo sigue disponible para CLI/Jobs. Si una ruta declara SPA pero no está instalado su renderer, la compilación falla en lugar de degradar silenciosamente a HTML.

Las interfaces de extensiones se resuelven por serviceId en bootstrap/factory registrada, no a partir de nombres enviados por cliente. Autowiring valida dependencias y ciclos. Una dependencia a Request dentro de un singleton reporter se rechaza en validación de scopes.

## Fallos de bootstrap y shutdown

Si Container no inicia, handler mínimo devuelve 500 sin detalle o salida CLI 78. El supervisor conoce estado not-ready. Si falla shutdown/flush, se registra código fijo y se termina dentro del deadline; no se vuelve a arrancar el Container para reportar su propio fallo.

Para pruebas unitarias se construye manager directamente con doubles; para integración se usa el provider real. Ambas rutas deben producir las mismas políticas, sin defaults ocultos en la fachada.

## Aceptación

Arrancar núcleo aislado, HTTP sin SPA, SPA completa y Jobs sin HTTP. Probar dependencia faltante, ciclo, binding de scope incorrecto y logger que falla en construcción. El contenedor debe limpiar scope en fallo antes y después de resolver controller. Readiness solo cambia a true cuando el plan y todos los bridges obligatorios son válidos.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](31_CONFIGURATION_REFERENCE_AND_VALIDATION.md) · [Siguiente](33_DEVELOPER_EXPERIENCE_AND_DEBUGGING.md)
