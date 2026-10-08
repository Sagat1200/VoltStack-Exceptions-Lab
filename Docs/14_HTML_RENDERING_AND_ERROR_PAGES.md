# 14 — Renderizado HTML y páginas de error

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## HTML público

HtmlExceptionRenderer usa PublicError y TransportPlan; no recibe Request entero, Throwable, Container ni ORM. Sus plantillas son pequeñas y no consultan Database, sesión remota, menú dinámico ni servicios que pudieron causar el fallo. Las vistas por categoría/status se precompilan al desplegar y cuentan con una página estática de reserva.

La página muestra título traducido aprobado, mensaje seguro, occurrenceId y una acción razonable: volver a un destino interno, recargar una lectura o consultar estado. No muestra clase PHP, archivo, SQL, tenant interno ni raw message. Una falla con efecto Unknown no ofrece “reintentar compra”; ofrece consultar resultado o una explicación de incertidumbre.

```html
<!doctype html>
<html lang="es">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>No pudimos completar la solicitud</title>
<main>
  <h1>No pudimos completar la solicitud</h1>
  <p>Consulta el estado de la operación antes de intentarlo de nuevo.</p>
  <p>Referencia: <code>occ_example</code></p>
</main>
</html>
```

El ejemplo representa texto fijo. Valores dinámicos se escapan en contexto HTML; nunca se concatenan como markup confiable. Traducciones y parámetros también se escapan. Scripts inline se evitan; si existe interacción, usar recursos del despliegue y CSP compatible sin ampliar la política global por un error.

## Formularios y navegación

La política browser puede usar Post/Redirect/Get para validaciones previstas, fuera del renderer genérico, si Session y Validation ofrecen transporte de errores seguro. Solo campos permitidos se conservan; passwords, tokens y archivos no se reflashean. El redirect utiliza ruta interna predefinida y limita bucles; no copia Referer sin validar.

La página de login es responsabilidad de Authentication. ExceptionRenderer puede solicitar navegación aprobada, no crear una sesión autenticada ni renovar credenciales. Para SPA se usa su envelope; no se devuelve HTML de login donde el runtime espera JSON.

## Disponibilidad y accesibilidad

La página base debe funcionar sin JavaScript, fuentes externas ni imágenes remotas. Usar lang, encabezado único, contraste, foco en resumen si navegación dinámica y referencia copiable. No depender de CDN para comunicar un incidente. HEAD suprime el body y conserva status.

## Pruebas

Renderizar cada status en español y locale desconocido; comprobar fallback de traducción. Inyectar tags, comillas y direcciones javascript: en parámetros y destinos. Fallar deliberadamente el motor de vistas y verificar la página mínima con status original o 500 si el fallo impide mantener semántica segura. Confirmar no-store y ausencia de recursos privados en el HTML final.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](13_HTTP_EXCEPTIONS_AND_STATUS_SEMANTICS.md) · [Siguiente](15_JSON_PROBLEM_DETAILS_AND_API_ERRORS.md)
