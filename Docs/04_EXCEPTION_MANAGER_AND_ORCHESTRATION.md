# 04 — Exception Manager y orquestación

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Responsabilidad

ExceptionManager es la entrada de aplicación y de los catch propietarios. Recibe explícitamente ExceptionContext y un plan inmutable. Coordina servicios; no decide políticas de negocio, no es service locator, no emite bytes y no controla ACK. Su dependencia de runtime es un puerto de scope, no una lectura global de “request actual”.

```php
try {
    return $controller->execute($command);
} catch (\Throwable $error) {
    $result = $exceptions->handle($error, $context);
    return $httpBridge->resolve($result, $error); // puerto propuesto
}
```

`httpBridge->resolve()` adapta el resultado al HTTPKernel; la firma ilustrativa no establece una API ya existente de HTTP. No se usa este catch dentro de cada método de dominio: normalmente basta la frontera de entrada, y boundaries deliberadas en componentes/operaciones recuperables.

## Composición interna

| Servicio | Función |
|---|---|
| OccurrenceRegistry | identidad por objeto y scope, receipts y contadores |
| Normalizer | snapshot de diagnóstico limitado |
| MappingPlan | resolución semántica determinista |
| PrivacyPolicy | datos autorizados para cada destino |
| RecoveryPolicy | consejo según efecto, cancelación e idempotencia |
| ReportCoordinator | filtros, deduplicación, fanout y receipts |
| ResponseResolver | transport mapping y renderer seleccionado |
| EmergencyHandler | resultado mínimo y marca de reciclado |

Manager es scoped; reglas, catálogos y factories son compartidos e inmutables. No retiene una colección ilimitada de errores: el registro tiene máximo por scope y conserva un contador de descartes al saturarse. Fuera de un scope explícito se crea un contexto de bootstrap limitado; no se hereda el del último usuario.

## Semántica de report y handle

`report($e, $context)` sirve para fallos absorbidos deliberadamente. No cambia el resultado de la operación, ni declara recuperación segura. Si luego se relanza `$e`, `handle()` reutiliza la ocurrencia y receipts, pero sí calcula transporte. El mismo objeto relanzado no incrementa de nuevo la métrica de ocurrencias. Si se necesita registrar un intento nuevo de operación, se abre contexto de intento y se genera nueva ocurrencia.

Los callbacks cómodos de DX se adaptan a los contratos; no forman una ruta paralela. Un `dontReport` impide reporters ordinarios, no el conteo agregado ni auditoría obligatoria del subsistema de seguridad. Un renderer custom recibe la misma proyección pública y límites que el predeterminado.

## Robustez

El manager preserva el Throwable original solo mientras dure el catch. Errores del propio subsistema usan código fijo `exceptions.internal_failure` y referencia de ocurrencia. No se concatena el mensaje de una excepción secundaria a la salida pública. La ruta mínima evita resolución de dependencias y trabaja con recursos preasignados cuando el bridge lo permite.

Se prueba con dependencias fake que ningún reporter detiene el rendering, que report-only no invoca renderer, que el descriptor original no puede mutarse y que cerrar el scope invalida futuras llamadas. Una llamada tras cierre falla de forma explícita ante el propietario; no abre silenciosamente un nuevo scope con identidad antigua.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](03_EXCEPTION_LIFECYCLE_AND_PIPELINE.md) · [Siguiente](05_EXCEPTION_CONTEXT_AND_SCOPE_SYSTEM.md)
