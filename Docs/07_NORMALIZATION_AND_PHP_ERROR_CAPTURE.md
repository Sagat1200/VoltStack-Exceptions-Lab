# 07 — Normalización y captura de errores PHP

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Captura PHP

El bridge instala handlers una vez durante bootstrap y conserva la política de composición con handlers previos. `catch (Throwable)` en las fronteras es el camino habitual para Exceptions y Errors. El handler global es una última red para excepciones no capturadas; no sustituye el ciclo de cada request de un worker.

Los warnings seleccionados pueden convertirse a ErrorException, respetando `error_reporting() & $severity`. Deprecations se envían a un canal de diagnóstico limitado y no interrumpen requests por defecto. La conversión global de todos los warnings puede romper librerías: el perfil estricto se prueba y activa explícitamente.

El [manual PHP de set_error_handler](https://www.php.net/manual/en/function.set-error-handler.php) establece que ciertos errores fatales y errores previos al registro no son manejables por esa función. El diseño no promete recuperar OOM, parse errors de arranque, fallos del motor o SIGKILL. El shutdown hook intenta diagnóstico mínimo si hay error fatal relevante y evita duplicarlo mediante marcador del bridge.

## Algoritmo de normalización

1. Capturar clase, código y mensaje bajo límites de bytes.
2. Redactar el mensaje antes de distribuirlo; para clases no confiables usar mensaje diagnóstico fijo si no se puede garantizar saneamiento.
3. Extraer frames sin args ni objetos; normalizar rutas a identificadores del proyecto.
4. Recorrer previous hasta `max_causes`, con conjunto de identidades para prevenir ciclos.
5. Aplicar límites de profundidad, frames y bytes totales, marcando truncamiento.
6. Extraer solo metadata de interfaces registradas y puras; jamás invocar `__toString()` de objetos arbitrarios.
7. Producir FailureSnapshot sin referencias al Throwable ni conexiones.

El objeto original queda en la frontera para propagación, no en el snapshot. La normalización no consulta código fuente, Database ni red. Source frames pertenecen al diagnóstico local autorizado y se cargan de forma diferida, fuera del camino de producción.

## Fallos durante normalización

Si una implementación de extractor lanza, se omite su metadata y se registra `normalization.extension_failed` sin reingresar. Si no puede producirse un snapshot seguro, el manager usa emergencia con código fijo. El límite de profundidad de manejo no es el de causas: son presupuestos independientes.

Una reserva de memoria del bridge puede ayudar al reporte fatal mínimo, pero no garantiza que se ejecute ni que alcance memoria. Al detectar corrupción de estado o fallo fatal, se solicita terminación/reciclado al runtime. Nunca se sirve el siguiente request suponiendo que el proceso quedó sano.

## Pruebas

Inyectar mensajes con secretos, UTF-8 inválido, rutas de Windows/Linux, cadenas extensas y args con objetos que lanzan al serializar. Confirmar que no se ejecuta su serialización. Probar error reporting deshabilitado, warning convertido, deprecation limitada, Throwable en bootstrap y doble notificación handler/shutdown. Los fatales y OOM se prueban en procesos aislados, no en la misma suite que debe continuar.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](06_EXCEPTION_DESCRIPTOR_AND_ERROR_CATALOG.md) · [Siguiente](08_EXCEPTION_MAPPING_ENGINE.md)
