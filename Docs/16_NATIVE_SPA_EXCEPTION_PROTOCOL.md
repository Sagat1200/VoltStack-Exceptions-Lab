# 16 — Protocolo de excepciones de la SPA nativa

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Protocolo SPA v1

Este protocolo es una propuesta de integración para la SPA reactiva nativa, no una API encontrada en las referencias. Endpoint registrado anuncia soporte de `application/vnd.voltstack.spa-error+json;v=1`. El request usa Accept compatible y `X-VoltStack-SPA-Version: 1`; los headers solo negocian representación. Authentication, CSRF y Authorization se evalúan por sus mecanismos habituales.

```json
{
  "protocol": "voltstack.spa.error",
  "version": 1,
  "kind": "exception",
  "occurrence_id": "occ_example",
  "request_id": "req_example",
  "operation_id": "op_example",
  "navigation_id": "nav_example",
  "status": 422,
  "error": {
    "code": "validation.failed",
    "message": "Revisa los campos indicados.",
    "fields": [{"path": "/email", "code": "format.invalid", "message": "Correo inválido."}]
  },
  "target": {"scope": "component", "id": "cmp_opaque", "revision": 7},
  "action": "show_fields",
  "effect": "None",
  "retry": {"allowed": false},
  "reconcile": null
}
```

## Campos y límites normativos

Todos los campos del ejemplo excepto fields, request_id, navigation_id y reconcile son obligatorios; navigation_id es obligatorio para operaciones de navegación. Identificadores son strings opacos de 1–128 caracteres seguros, ligados al scope; revision es entero no negativo en rango seguro de JavaScript. status debe coincidir con HTTP y ser 400–599. effect toma los cuatro valores de 06. fields máximo 100, paths permitidos por formulario, sin valores originales.

target.scope es `component` o `page`; id y revision son obligatorios para component y se omiten para page. Las referencias se verifican contra el árbol autorizado del servidor. action pertenece a `show_fields`, `show_boundary`, `show_page`, `authenticate`, `challenge`, `reconcile`, `retry_read` o `reload`. No contiene JavaScript, HTML ejecutable ni nombres de métodos arbitrarios. Campos desconocidos se ignoran si la versión mayor es compatible; una action desconocida muestra error genérico seguro.

retry.allowed solo puede ser true cuando el servidor certifica que la operación es lectura/idempotente y la política permite repetición; puede incluir after_ms y max_attempts limitados. El default es false. reconcile puede contener `operation_ref` y `route_key` registrados, nunca URL externa arbitraria. No se publican traceIds por defecto.

## Algoritmo cliente

Validar content type, tamaño máximo 64 KiB, esquema y versión. Asociar operation_id al comando pendiente del cliente. Si navigation_id ya no es activa, no alterar pantalla nueva; conservar señal para reconciliar esa operación. Para componente, verificar id y revision del árbol actualmente montado; ignorar resultados obsoletos y solicitar snapshot si la identidad cambió. Una respuesta retrasada de usuario previo se descarta tras cambio de sesión.

En effect=None se puede revertir actualización optimista de esa operación, sin borrar cambios posteriores. Committed obliga a reconciliar resultado y no repetir comando. Partial/Unknown mantienen estado pendiente y consultan endpoint autorizado si existe; no simulan rollback. fields actualiza errores y foco accesible. authenticate/challenge solicita un flujo registrado; nunca convierte respuesta en autorización ni reenvía automáticamente una mutación después de login.

## Negociación, fallos y seguridad

Versión solicitada incompatible devuelve 406 Problem Details mínimo con code `spa.protocol_unsupported` registrado por bridge y supported_versions=[1]; el cliente tiene fallback de navegación completa explícita. No se confunde con fallo de aplicación. Si la respuesta se pierde, el cliente considera el efecto desconocido y consulta idempotency/operation status antes de repetir mutaciones. Un 5xx por proxy puede ser HTML: el cliente detecta content type y muestra error de conectividad sin insertar ese HTML.

Respuesta de error usa no-store y mantiene el status real. Un batch mixto solo puede usar 200 con resultados parciales cuando el protocolo de batch de éxito lo define expresamente; no usa este envelope de excepción raíz para ocultar fallos. CSRF inválido se representa como 403 con código propio seguro y sin reload que reenvíe la operación. El runtime limpia stores privados al logout y reautoriza después de reconectar.

## Conformidad

Probar validación local, boundary desmontada, navegación A que responde después de B, cambio de usuario, doble clic, commit con pérdida de respuesta, versión incompatible y cuerpo de proxy no JSON. Una suite compartida servidor/cliente debe usar fixtures idénticos y verificar que ningún consejo retry habilita duplicar una mutación incierta.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](15_JSON_PROBLEM_DETAILS_AND_API_ERRORS.md) · [Siguiente](17_COMPONENT_EXCEPTION_BOUNDARIES.md)
