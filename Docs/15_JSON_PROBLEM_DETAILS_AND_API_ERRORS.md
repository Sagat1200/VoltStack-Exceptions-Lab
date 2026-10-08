# 15 — JSON, Problem Details y errores de API

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Perfil JSON

La representación API predeterminada usa `application/problem+json`. Se toma [RFC 9457](https://www.rfc-editor.org/rfc/rfc9457.html) como referencia de interoperabilidad para Problem Details. El perfil propio de VoltStack fija extensiones y reglas adicionales; los clientes no deben interpretar textos traducidos como códigos.

```json
{
  "type": "https://api.example.test/problems/validation-failed",
  "title": "Datos inválidos",
  "status": 422,
  "detail": "Revisa los campos indicados.",
  "instance": "urn:voltstack:occurrence:01EXAMPLE",
  "code": "validation.failed",
  "request_id": "req_example",
  "errors": [
    {"pointer": "/email", "code": "format.invalid", "message": "Introduce un correo válido."}
  ]
}
```

`example.test` es un placeholder de documentación, no un endpoint de VoltStack. Cada aplicación configura URIs estables de tipos; no se construyen desde Host no confiable. `instance` identifica la ocurrencia y no implica que pueda consultarse públicamente. `request_id` es opcional y validado; el identificador de ocurrencia es suficiente para soporte.

## Esquema del perfil

type es URI absoluta configurada o `about:blank`; title es resumen aprobado; status coincide con el status HTTP; detail es mensaje seguro; instance es referencia opaca. code es obligatorio en el perfil VoltStack. errors es opcional, máximo 100 elementos, cada uno con pointer JSON Pointer válido, código y mensaje de catálogo. Parámetros de validación no incluyen el valor enviado.

Para `about:blank`, title corresponde al motivo HTTP traducido. Un code desconocido por el cliente se muestra con mensaje seguro genérico sin romper parseo. Campos adicionales futuros pueden ignorarse, pero el servidor nunca serializa automáticamente todo el descriptor como extensiones.

## Encoding y compatibilidad

JsonExceptionRenderer construye un mapa allowlisted y codifica UTF-8 con política explícita de reemplazo de secuencias inválidas. Si no puede codificarlo o excede el máximo, usa un objeto mínimo constante para internal.error. No se usa `json_encode($throwable)` ni serializers que inspeccionen propiedades privadas.

Endpoints heredados pueden instalar un perfil `application/json` con envelope propio versionado; ese perfil debe declararse en la ruta, no cambiar en función de la excepción. La respuesta SPA utiliza otro media type y no se anuncia como Problem Details. Error HTTP sigue siendo 4xx/5xx, nunca 200 solo para facilitar parsing.

## Privacidad y pruebas

No se incluyen trace, class, file, bindings SQL, stack ni previous en este perfil, tampoco en debug mediante query string. Diagnostics locales utilizan canal separado. Probar correspondencia status/body, caracteres inválidos, fields sensibles, límite de errores, cliente que desconoce code y petición HEAD. Contract tests validan las extensiones y comprobación negativa de claves prohibidas en cada nivel.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](14_HTML_RENDERING_AND_ERROR_PAGES.md) · [Siguiente](16_NATIVE_SPA_EXCEPTION_PROTOCOL.md)
