# 28 — Integración con HTTPKernel, Routing y Controllers

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Frontera HTTPKernel

El catch exterior de HTTPKernel rodea routing, middleware, controller y construcción de respuesta. La emisión puede ocurrir después y necesita su propio manejo dentro del mismo scope. El kernel dispone de contexto mínimo aun cuando falla el router o Container. Un catch en controller no sustituye esa frontera.

```text
scope → request normalizado → routing → authn → authz → validation
      → controller → respuesta → emisión → finalize → reset
      cualquier Throwable → ExceptionManager → HandlingResult
```

El orden exacto de middleware del proyecto puede variar, pero identidad y autorización deben resolverse antes de exponer datos. El contexto de excepción se enriquece solo con lo que efectivamente se resolvió; si routing falla, routeName es null y no se intenta volver a resolver la ruta para reportar.

## Routing

Router aporta not_found, method_not_allowed, Allow y perfil de representación. Un error en sintaxis de definición o binding de ruta es configuración interna, no 404. Resource binding que no encuentra entidad puede ser 404 según política de privacidad; binding de tenant debe filtrar antes de comprobar existencia visible.

El compilador de rutas exporta routeName y perfil `browser`, `api` o `spa`, más versiones y formatos permitidos. Exceptions consume ese contrato mediante bridge; no escanea anotaciones del controller en cada error. Parámetros de ruta sensibles no se copian al contexto automáticamente.

## Controllers y middleware

Controllers lanzan excepciones de dominio o devuelven outcomes explícitos. Una excepción HTTP puede usarse en la capa de presentación; no debe propagarse como dependencia del dominio. Middleware de seguridad puede generar denial/challenge; mantiene los códigos semánticos diferenciados de 29.

Un middleware que ya produjo Response no la envía directamente. Si un middleware posterior falla antes de emisión, el kernel la reemplaza por resultado seguro. Si hay bytes enviados, aborta o usa frame terminal del protocolo. After-response failures se reportan y afectan estado operativo, pero no reescriben status que el cliente ya recibió.

## Integración con helpers

`abort`, `report` y `rescue` son namespaced/fachadas sobre servicios scoped. El helper report obtiene el scope explícito mediante bridge de aplicación; invocarlo desde async sin contexto válido falla de forma identificable y no reutiliza el request anterior. El dominio puede usar interfaces inyectadas en lugar de helpers.

## Pruebas de extremo a extremo

Falla antes del router, ruta ausente, método incorrecto, constructor del controller que lanza, validación, error en plantilla y fallo al emitir stream. Verificar una sola respuesta, status y mediaType correctos, Allow/WWW-Authenticate cuando correspondan y cleanup. Probar que un header SPA forjado no cambia permisos ni activa una ruta no declarada.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](27_ROADRUNNER_OPENSWOOLE_AND_RUNTIME_ADAPTERS.md) · [Siguiente](29_AUTHENTICATION_AUTHORIZATION_AND_VALIDATION_INTEGRATION.md)
