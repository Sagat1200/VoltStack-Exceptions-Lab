# 18 — CLI, salida estructurada y códigos de salida

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Separación de canales

CliExceptionRenderer escribe un RenderedOutput dirigido a stderr; ConsoleBridge decide cuándo escribir y qué código devolver al entrypoint. El núcleo no llama exit(). stdout queda disponible para datos del comando, incluidos pipelines. El modo JSON se solicita como opción del comando y produce un único objeto JSON de error en stderr, sin adornos ANSI.

```json
{"version":1,"code":"resource.conflict","message":"El recurso cambió.","occurrence_id":"occ_example","exit_code":5}
```

No se reutiliza Problem Details HTTP como contrato CLI: no hay status HTTP ni headers. Un consumidor debe usar exit_code y code; no parsear texto humano traducido. Si stdout ya contiene salida parcial, el comando documenta esa posibilidad y falla con código no cero; no imprime un marcador que simule salida completa.

## Códigos estables del perfil VoltStack

| Código | Uso |
|---:|---|
| 0 | finalización exitosa; nunca excepción sin resolver |
| 1 | error interno o no clasificado |
| 2 | uso/argumentos/validación inválidos |
| 3 | autenticación o autorización |
| 4 | recurso requerido ausente |
| 5 | conflicto de estado |
| 6 | dependencia temporal o resultado incierto |
| 78 | configuración inválida |
| 130 | SIGINT gestionada por el bridge en plataformas compatibles |

Estos códigos son convención VoltStack, no equivalencia universal de shells. Otros signals y entornos Windows se adaptan en ConsoleBridge con matriz publicada. La cancelación programática no originada en SIGINT usa el código del perfil del comando, por defecto 1, y code=operation.cancelled.

## Presentación humana

Modo normal: mensaje seguro, referencia e indicación de acción. Verbosidad adicional local puede mostrar frames redactados si la política debug lo permite. `--verbose` no anula production.debug=false ni habilita imprimir credenciales. Color depende de TTY y opción de usuario; todo texto dinámico elimina secuencias de control para evitar escapes de terminal maliciosos.

Errores de bootstrap usan una línea fija en stderr y 78/1. Un comando de larga duración que ejecuta jobs no termina todo el worker por un fallo de negocio; Jobs consume HandlingResult y el supervisor recibe workerDisposition. Un fallo de cleanup puede exigir reciclado después de finalizar el intento actual.

## Aceptación

Probar stdout capturado, stderr JSON parseable, terminal sin color, caracteres de control y pipe roto. Simular error después de emitir filas y verificar código no cero. El entrypoint debe preservar el código incluso si reporting falla. Probar SIGINT en proceso aislado y separar cancelación operativa de retry de un trabajo.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](17_COMPONENT_EXCEPTION_BOUNDARIES.md) · [Siguiente](19_EXCEPTION_EVENTS_AND_EVENT_SYSTEM_INTEGRATION.md)
