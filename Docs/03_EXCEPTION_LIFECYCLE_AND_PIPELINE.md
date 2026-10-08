# 03 — Ciclo de vida y pipeline

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Máquina de estados

La ocurrencia sigue `Captured → Snapshotted → Classified → Sanitized → Decided → ReportAttempted → Resolved → Finalized → Released`. `Resolved` puede ser salida, propagación, decisión de job o cierre de transporte. `ReportAttempted` incluye skipped/dropped: no exige entrega. Una falla interna transita a `Emergency → Released`. El cierre de recursos siempre se intenta desde `finally`, incluso si la emisión falla.

```text
catch propietario
  asignar/reutilizar occurrenceId en scope
  capturar snapshot acotado y redactar diagnóstico
  elegir un mapper semántico
  construir descriptor inmutable
  calcular recuperación permitida y proyección pública
  contar ocurrencia e intentar reporting con presupuesto
  resolver transporte y renderizar, o propagar/devolver decisión
  devolver HandlingResult al propietario
propietario emite/finaliza y notifica resultado real
finally: cerrar scope o frontera hija; liberar referencias
```

## Orden y decisiones

La normalización elimina secretos evidentes antes de construir el snapshot; la proyección posterior aplica reglas específicas del catálogo y del destino. No existe una etapa que distribuya el Throwable sin filtrar a plugins generales. Recovery decide consejo; no ejecuta otra vez el controller. El propietario de la unidad transaccional o job puede consumir el consejo bajo sus propias garantías.

`ExceptionCaught` se emite solo después de disponer de metadata mínima saneada; su nombre describe el hecho, no acceso al objeto original. `ExceptionClassified`, `ExceptionReportCompleted` y `ExceptionResolved` siguen al cambio de estado correspondiente. `ExceptionFinalized` lo emite el bridge con éxito/fallo real de transporte. No se emite “rendered” como si implicara entrega al cliente.

## Fronteras anidadas

Una boundary de componente puede manejar el fallo local antes de que alcance HTTPKernel. Si lo propaga, conserva occurrenceId y causa; no genera un segundo reporte al alcanzar el kernel. Dos throws independientes de la misma clase son dos ocurrencias. El estado de cada frontera está separado del ledger de reporting compartido por el scope.

La marca `handlingDepth` se incrementa en entrada y decrementa en finally. Reingresar al manager mientras maneja una falla activa usa emergencia cuando supera el límite configurado; no bloquea errores independientes de otro scope concurrente. Un renderer fallido crea diagnóstico secundario con `parentOccurrenceId`, pero no vuelve a pasar por el renderer que falló.

## Emisión y cleanup

El emisor valida status y headers antes de enviar. Una vez enviados headers o cuerpo, falla tardía produce `abort_transport`, no un segundo documento HTML/JSON. El bridge puede emitir un frame terminal únicamente si el protocolo de stream lo define y aún admite escribir. Un fallo de reset o conexión incierta fuerza reciclado. Un fallo posterior a commit no se presenta como rollback de negocio.

## Criterios de prueba

Registrar el orden exacto con un recorder; inducir fallos en cada etapa; comprobar que existe un único resultado terminal y que cleanup se intenta siempre. Verificar reporter caído con respuesta válida, renderer caído con fallback seguro, propagación boundary→kernel sin duplicado y stream iniciado sin sustitución de status. Los eventos faltantes por emergencia son aceptables; un éxito ficticio no lo es.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](02_PUBLIC_CONTRACTS_AND_DOMAIN_MODEL.md) · [Siguiente](04_EXCEPTION_MANAGER_AND_ORCHESTRATION.md)
