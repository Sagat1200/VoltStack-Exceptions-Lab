# 17 — Boundaries de excepción en componentes

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Boundary como frontera de contención

ComponentExceptionBoundary protege un subtree explícito de la SPA/SSR. Solo captura errores originados en evaluación, carga de datos o renderizado de ese subtree. No captura indistintamente todos los fallos del proceso. Recibe el descriptor saneado y decide `fallback`, `propagate` o `reconcile`; nunca concede acceso ni oculta un fallo transaccional como éxito.

El árbol de boundaries se compila con id de definición y referencias de instancia por sesión de render. Captura la boundary más cercana capaz de manejar la categoría. Un fallo dentro de su propio fallback asciende a la siguiente; cada boundary se visita como máximo una vez por ocurrencia. Si no existe otra, HTTPKernel produce página/envelope de error.

## Política de categorías

| Fallo | Tratamiento predeterminado |
|---|---|
| Consulta opcional de recomendaciones | fallback local autorizado |
| Validation de formulario | fields en componente propietario |
| Autenticación expirada | flujo de autenticación a nivel de aplicación |
| Denial de acceso al componente | ocultación/denial aprobado; sin datos alternativos privados |
| Integrity, configuración, estado de worker corrupto | propagación; no fallback silencioso |
| Mutación con efecto Unknown | reconciliar operación; no UI de éxito |

Una boundary de presentación no inicia retries transaccionales. Al refrescar un widget se ejecuta una nueva consulta autorizada con presupuesto; al repetir un comando se necesita la política de 20.

## SSR y reactividad

Durante SSR, una consulta opcional puede producir un fragmento fallback y mantener HTTP 200 solo si la página completa sigue cumpliendo el contrato funcional de la ruta. Esa degradación se registra como component_degraded y no como fallo raíz oculto. Si el contenido obligatorio falla antes de emitir, la página recibe 5xx/4xx. Después de flush solo se permite el protocolo de stream previsto o abortar.

En SPA, cada acción fallida responde con status real y envelope 16 dirigido a id/revision autorizados. El cliente verifica que el subtree aún exista, no restaura una instancia desmontada y no elimina estado no relacionado. El reset de boundary se activa por cambio de revision, acción explícita de lectura o nueva navegación; no hay loop automático de render→error→retry.

## DX propuesta

Una declaración `boundary('recommendations')->handles('dependency')->fallback('recommendations-unavailable')` registra ids de componentes/fallbacks, no closures que capturen el request. El compilador valida que el fallback no sea el propio componente y que sus dependencias no incluyan el servicio fallido obligatorio. El renderer ejecuta fallback con contexto mínimo y presupuesto separado.

## Pruebas

Anidar tres boundaries y forzar fallo en componente y primer fallback: la segunda válida maneja una sola ocurrencia. Probar invalidación de revision, unmounted component, acceso revocado y SSR después de flush. Métricas distinguen fallo raíz de degradación local. Aceptar fallback no borra el reporte original ni cambia effect=Committed/Unknown.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](16_NATIVE_SPA_EXCEPTION_PROTOCOL.md) · [Siguiente](18_CLI_EXCEPTION_RENDERING_AND_EXIT_CODES.md)
