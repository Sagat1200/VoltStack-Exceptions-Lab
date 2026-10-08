# 37 — Extensibilidad, versionado e interoperabilidad

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Puntos de extensión

Un provider puede registrar normalización de metadata, mapper semántico, política de reporting, reporter, perfil de transporte, renderer, recovery policy o bridge. Cada extensión declara id, versión, contratos, capacidades, dependencias y lifetime. Las reglas de registro pertenecen al plan compilado y no cambian por petición.

| Extensión | Puede hacer | No puede hacer |
|---|---|---|
| Extractor confiable | leer metadata allowlisted del Throwable | serializar grafo arbitrario |
| Mapper | seleccionar SemanticError válido | emitir bytes o ejecutar retry |
| Reporter | enviar DTO saneado con budget | acceder al request por singleton |
| Renderer | producir output del formato declarado | decidir permisos o saltar guard |
| Recovery policy | aconsejar acción | repetir una mutación desde el manager |
| Event listener | observar hecho saneado | detener redacción o cleanup |
| Runtime bridge | scopes y finalización | redefinir código semántico de dominio |

Registrar una extensión exige pruebas contractuales y validación de lifecycle. Declararse “trusted” permite acceso técnico necesario al extractor, pero no elimina la obligación de sanear antes de distribuir datos. Un plugin PHP instalado puede ejecutar código: los contratos son gobernanza y arquitectura, no aislamiento de seguridad del lenguaje.

## Versionado

El paquete usa versionado semántico; se versionan por separado catálogo, plan compilado, failure envelope de Jobs y protocolo SPA. Añadir campo opcional compatible no cambia versión mayor del protocolo; retirar/renombrar campos requeridos o cambiar significado de action sí. Un código de error no se reutiliza con otro significado.

Interfaces PHP nuevas pueden ser compatibles si son opt-in; añadir métodos obligatorios a una interfaz implementada por terceros es cambio mayor. Una extensión que no cumple schema/capabilities se rechaza al arrancar, con identificación precisa. La configuración estricta ayuda a detectar migraciones incompletas.

## Interoperabilidad

Bridge PSR-3 opcional traduce severity y ReportRecord al logger, pero no pasa Throwable bruto en context por defecto. HTTP puede adaptarse a modelos de Response del proyecto o PSR mediante bridge, sin forzar PSR en núcleo. Adaptadores de excepciones Laravel/Symfony pueden extraer status/headers con guards; no se necesita herencia de esas bibliotecas.

Integrar un exporter externo no convierte sus IDs en occurrenceId canónico. El mapeo interno mantiene identidad independiente del proveedor y facilita reemplazarlo sin romper soporte al cliente.

## Migración y deprecación

Publicar tabla de códigos/formatos antiguos a nuevos, periodo de coexistencia y fixtures de compatibilidad. Diagnósticos de deprecación se muestrean y no interrumpen producción. Rollout canario compara status, códigos públicos, privacy y métricas, sin duplicar efectos de negocio. Shadow evaluation de mapping solo usa snapshots, nunca ejecuta dos controllers.

## Pruebas de terceros

ExtensionTestKit comprueba determinismo, ausencia de mutaciones en DTO, límites, errores propios y scopes. Reporters demuestran timeout real y no filtración. Renderers cumplen esquema y headers. Cada runtime adicional ejecuta 25–27. Un plugin no certificado puede ser experimental, pero no debe publicarse como implementación compatible completa.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](36_CACHE_COMPILATION_AND_DEPLOYMENT.md) · [Siguiente](38_TESTING_CONTRACTS_AND_ACCEPTANCE_MATRIX.md)
