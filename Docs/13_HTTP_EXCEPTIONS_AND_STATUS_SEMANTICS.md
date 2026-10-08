# 13 — Excepciones HTTP y semántica de estados

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Familia HTTP propia

`VoltStack\Quantum\Exceptions\Http` pertenece al bridge HTTP. `HttpExceptionInterface` expone `statusCode(): int` y `headers(): array`; las implementaciones extienden RuntimeException y conservan previous. Su mensaje interno no se publica automáticamente. Un constructor valida 400–599 y headers permitidos. El núcleo reconoce esos metadatos mediante extractor instalado por el bridge.

Clases iniciales: BadRequestHttpException, UnauthorizedHttpException, ForbiddenHttpException, NotFoundHttpException, MethodNotAllowedHttpException, ConflictHttpException, PreconditionFailedHttpException, PayloadTooLargeHttpException, UnsupportedMediaTypeHttpException, UnprocessableContentHttpException, TooManyRequestsHttpException, InternalServerErrorHttpException, ServiceUnavailableHttpException y GatewayTimeoutHttpException.

| Estado | Regla de uso |
|---|---|
| 400 | sintaxis de request inválida, no validación de negocio genérica |
| 401 | falta credencial válida; incluir WWW-Authenticate apropiado |
| 403 | identidad sin permiso o challenge de aplicación; no se inventa challenge HTTP |
| 404 | ausencia o ocultación explícita consistente |
| 405 | ruta existe para otros métodos; incluir Allow calculado por Router |
| 409 | conflicto con estado de recurso |
| 412 | precondición HTTP evaluada y fallida |
| 413 / 415 | tamaño o media type de entrada no admitido |
| 422 | entrada sintácticamente válida con fallos de validación |
| 429 | política de rate limit; Retry-After opcional si estimación fiable |
| 500 | defecto interno o configuración inválida |
| 503 | incapacidad temporal del servicio; no prueba ausencia de efectos |
| 504 | gateway que agotó espera a upstream; no todo timeout interno |

## Headers

La allowlist base acepta Allow, WWW-Authenticate y Retry-After mediante parsers especializados, más headers seguros definidos por el bridge. Rechaza CR/LF, bytes de control, nombres inválidos y exceso de longitud. No copia cookies ni headers del Throwable por defecto. El bridge añade `Cache-Control: no-store`, `X-Content-Type-Options: nosniff` y mediaType correcto. Location solo procede de una política de navegación con destinos internos registrados.

## Cancelación y errores tardíos

La desconexión del cliente no genera un status 499 público estándar; se registra como client_cancelled interno y se aborta transporte. Si el servidor cancela antes de emitir y la conexión sigue viva, el perfil puede devolver 503 con código operation.cancelled. CLI SIGINT se trata en 18. Deadline interno no implica automáticamente 408, cuyo significado depende de recepción del request.

## DX y pruebas

`abort(404)` propuesto lanza NotFoundHttpException; abortIf/abortUnless son helpers namespaced y no terminan el proceso. Se prueban status inválidos, headers maliciosos, 401 sin challenge (configuración rechazada), 405 con métodos reales y HEAD sin cuerpo. Tests verifican que una excepción de dominio con getCode()=401 no recibe automáticamente privilegios ni semántica de autenticación.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](12_RENDERING_NEGOTIATION_AND_RESPONSE_RESOLUTION.md) · [Siguiente](14_HTML_RENDERING_AND_ERROR_PAGES.md)
