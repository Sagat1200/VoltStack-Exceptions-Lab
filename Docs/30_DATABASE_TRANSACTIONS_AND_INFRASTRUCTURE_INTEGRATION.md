# 30 — Database, transacciones e infraestructura

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Database como autoridad transaccional

Database determina commit, rollback, savepoints, integridad y estado de conexión. Exceptions recibe `effect` y código semántico; no llama rollback desde el renderer. La referencia local exige repetir la unidad transaccional completa y coordinar efectos externos mediante outbox o after-commit. Se conserva esa regla.

| Condición de Database | Clasificación propuesta | Regla |
|---|---|---|
| constraint de negocio identificada | validation/conflict según mapper explícito | no exponer nombre físico ni SQL |
| optimistic version conflict | resource.conflict | releer o pedir reconciliación |
| deadlock con rollback confirmado | dependency/conflict transitorio interno | retry de unidad por Transaction Manager |
| conectividad antes de ejecutar | dependency.unavailable, None si probado | retry condicional |
| pérdida de conexión en commit | operation.indeterminate, Unknown | consultar estado/idempotencia |
| rollback fallido | internal/dependency, Unknown | descartar conexión y conciliar |
| esquema/mapping inválido | configuration.invalid | no retry de aplicación |

No toda excepción del driver equivale a un conflicto de usuario. La traducción de códigos de motor pertenece al adapter de Database y debe probarse por motor. SQLSTATE o getCode se conservan como diagnóstico saneado, sin deducir seguridad de retry a partir de una cadena aislada.

## Unit of Work y estado posterior

Tras rollback, Unit of Work puede quedar inválida o requerir reset; EntityManager decide. Exceptions no sirve entidades parcialmente hidratadas como fallback. Conexión de estado incierto se retira del pool. Cleanup fallido se vincula como diagnóstico secundario y puede forzar reciclado, aunque el error inicial fuera una validación esperada.

Si commit fue confirmado y luego falla render/Telemetry, effect=Committed permanece. HTTP puede devolver error técnico, pero UI debe reconciliar y no repetir la mutación. La outbox confirmada sigue su ciclo; un fallo de Exceptions no anula eventos de negocio ya durables.

## Cache y Filesystem

La colección Filesystem ya define None/Committed/Partial/Unknown, retry condicionado y conciliación. El bridge conserva esos estados al mapear StorageException. Una escritura de objeto parcial no se representa como “archivo ausente”. Cache distingue datos descartables de coordinación: una caída de caché de lectura puede permitir fallback al origen según Cache; un lock/lease fallido no permite ejecutar libremente la sección protegida.

Integraciones son paquetes opcionales y no generan dependencias Database→Exceptions→Database. Cada módulo publica extractores de metadatos y mappers semánticos. La infraestructura no necesita conocer HTML ni SPA para clasificar un error.

## Pruebas

Inyectar pérdida de respuesta de commit, deadlock, constraint conocida/desconocida, fallo de outbox y fallo de renderer tras commit. Contar mutaciones reales y confirmar que Unknown llega intacto al cliente. Repetir por motores soportados por Database y con recursos de Filesystem de capacidades diferentes; un fake no demuestra rollback real ni consistencia distribuida.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](29_AUTHENTICATION_AUTHORIZATION_AND_VALIDATION_INTEGRATION.md) · [Siguiente](31_CONFIGURATION_REFERENCE_AND_VALIDATION.md)
