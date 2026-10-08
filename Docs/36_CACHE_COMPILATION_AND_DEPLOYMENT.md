# 36 — Cache, compilación y despliegue

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Qué se compila

El compilador produce catálogo, índice de tipos→candidatos, orden de servicios, perfiles de transporte, traducciones de fallback, esquema SPA y referencias a plantillas. El artefacto es inmutable por release y no contiene usuarios, requests, secretos, Throwables ni closures serializadas.

Fingerprint del plan incluye schemaVersion, release de paquetes, versión de PHP relevante, configuración normalizada, catálogo, reglas, templates, traducciones y políticas de privacidad. El hash identifica coherencia, no prueba por sí solo autenticidad de un archivo que un atacante pudiera reemplazar junto con el hash.

## Proceso de compilación

Descubrir providers explícitos → validar contratos/lifetimes → resolver reglas y prioridades → detectar shadowing/ciclos → validar catálogo y transportes → validar traducciones/plantillas → generar artefacto temporal → verificarlo → publicar atómicamente dentro del release. Fallo en cualquier etapa deja el release anterior intacto y aborta activación nueva.

Production requiere plan compatible. Si falta o está corrupto, arranque falla con emergency handler; no recompila silenciosamente desde archivos escribibles ni opera con la mitad de las reglas. Desarrollo puede compilar al arrancar y reportar diagnósticos detallados locales.

## Formato y seguridad

Puede usarse PHP generado por el compilador confiable o formato de datos validado. Si es PHP, se carga solo desde release de solo lectura; no desde uploads ni cache pública. Si es JSON, se valida schema/version y límites antes de construir objetos. No usar unserialize genérico. Secretos se resuelven por servicios en runtime.

Los permisos y procedencia del release, firma de distribución cuando aplique y publicación controlada protegen el artefacto. Un checksum almacenado junto a un archivo en directorio comprometido solo detecta corrupción accidental.

## Caches de runtime

Se permite cache de candidatos por clase y plan hash con límite. No se cachea PublicError contextual, descriptor de usuario, resultado de predicados ni decisión de autorización. Dedup de ocurrencias es scoped y se borra al final, no es cache persistente.

Cambiar configuración crea nueva policyRevision. Workers existentes conservan plan antiguo hasta terminar sus operaciones; nuevos workers usan release nuevo. Drain evita mutación del plan en vuelo. El protocolo SPA mantiene compatibilidad con clientes abiertos de la versión anterior o responde incompatibilidad de forma explícita; deploy no fuerza repetir una mutación.

## Verificación

Compilar dos veces los mismos inputs produce mismo contenido lógico/hash, excluyendo timestamps no funcionales. Cambiar regla, traducción o privacy invalida hash; cambiar identidad de request no. Probar artefacto truncado, versión errónea, serviceId ausente, rollback de release y worker antiguo concurrente con nuevo. La suite confirma que nunca aparece una combinación de catálogo nuevo con mapping viejo.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](35_PERFORMANCE_AND_RESOURCE_GOVERNANCE.md) · [Siguiente](37_EXTENSIBILITY_VERSIONING_AND_INTEROPERABILITY.md)
