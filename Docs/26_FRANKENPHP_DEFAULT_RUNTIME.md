# 26 — FrankenPHP como runtime predeterminado

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Perfil predeterminado

FrankenPHP worker es el primer runtime persistente objetivo. El [modo worker de FrankenPHP](https://frankenphp.dev/docs/worker/) mantiene la aplicación preparada entre solicitudes; por eso el diseño separa bootstrap del proceso y scope de cada operación. Esta documentación no declara una versión ya certificada: el release deberá fijar PHP, FrankenPHP, extensiones y modo de ejecución efectivamente probado.

## Bootstrap y request loop

Bootstrap carga autoload, instala handler mínimo, valida configuración, construye plan inmutable y factories, registra bridges y declara worker listo. Cada callback de request abre scope, ejecuta HTTPKernel y captura escapes. El adapter del runtime controla la finalización real, incluyendo el ciclo del body/stream.

```php
// Pseudocódigo de adaptación: $server encapsula la API real fijada por release.
$server->onRequest(function ($incoming) use ($scopeFactory, $kernel, $emergency) {
    $scope = $scopeFactory->begin($incoming);
    try {
        $kernel->run($incoming, $scope);
    } catch (\Throwable $uncaught) {
        $emergency->finalizeUncaught($uncaught, $scope);
    } finally {
        $reset = $scopeFactory->close($scope);
        if (!$reset->reusable) {
            $serverControl = $scopeFactory->serverControl();
            $serverControl->requestRecycle();
        }
    }
});
```

Este ejemplo describe responsabilidades y no copia una API FrankenPHP inexistente. `kernel->run()` aquí incluye la vida del stream; si la API real retorna un stream diferido, el adapter debe retrasar close hasta su cierre. No basta un finally alrededor de la construcción de Response.

## Captura global y limpieza

Los handlers PHP se instalan una vez y enrutan al contexto activo verificado. No se apilan registros por request ni se conserva el error anterior. Deduplicación, locale, identidad, estado de debug y responseCommitted se reinician en cada scope. Los datos globales de librerías ajenas requieren resetters explícitos o excluir esa librería del perfil persistente.

Un fallo de aplicación recuperable devuelve error y permite siguiente request si todos los resetters pasan. Un fatal, reset incompleto, conexión de protocolo corrupta o fallo de integridad de proceso exige recycle/terminate. El supervisor repone el worker; el manager no llama exit() dentro del request normal.

## Operación

Deploy cambia versión de plan y drena workers antiguos; no muta el catálogo de workers en vuelo. Configurar máximo de requests/memoria como protección adicional, readiness después de bootstrap y graceful shutdown con deadline. Telemetry flush se acota para que un exporter caído no impida reinicio.

## Suite de certificación

Ejecutar secuencia alternada de 10.000 requests con tenants A/B, distintos locales y fallos cada varios requests; verificar cero datos cruzados. Incluir HTML/JSON/SPA, stream, cancelación, reporte fallido y reset defectuoso. Medir memoria retenida y demostrar que un worker marcado no reusable no acepta la siguiente solicitud. OOM y fallos del proceso se inyectan en workers aislados con supervisor real.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](25_PERSISTENT_RUNTIME_LIFECYCLE_AND_ISOLATION.md) · [Siguiente](27_ROADRUNNER_OPENSWOOLE_AND_RUNTIME_ADAPTERS.md)
