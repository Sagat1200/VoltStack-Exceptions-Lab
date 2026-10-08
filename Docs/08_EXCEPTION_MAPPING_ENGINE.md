# 08 — Motor de mapping de excepciones

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Registro de reglas

MappingPlan traduce FailureSnapshot a SemanticError. Cada regla declara `id`, `exceptionType`, `priority`, `serviceId`, `originPackage` y predicado opcional sobre metadata saneada. Las reglas se registran en bootstrap; no durante un request. La implementación no requiere convertir o envolver el Throwable original para cambiar su significado público.

## Resolución determinista

1. Construir candidatos por clase exacta, ancestros e interfaces conocidas.
2. Ordenar por priority descendente; luego especificidad: clase exacta, ancestro más cercano, interfaz.
3. Para igual priority y especificidad, ordenar por id ASCII ascendente. Reglas exclusivas con empate se rechazan durante compilación; reglas no exclusivas aceptan ese desempate explícito.
4. Evaluar predicados puros con un máximo de candidatos. El primer mapper que devuelve SemanticError termina la búsqueda. Null significa “no aplica”.
5. Sin coincidencia, producir `internal.error`, effect heredado del propietario y retryAdvice=never.

Cada regla opera sobre la misma captura inicial. El resultado no vuelve a entrar al motor; por ello A→B→A no puede crear ciclos. Un adapter de librería que requiera inspeccionar el objeto original actúa como extractor confiable durante normalización, antes de construir snapshots para extensiones ordinarias.

```php
// API de registro propuesta; ejecutada solo al construir el plan.
$rules->map(
    id: 'billing.insufficient_balance',
    exceptionType: InsufficientBalance::class,
    serviceId: BalanceErrorMapper::class,
    priority: 100,
);
```

Los atributos `#[MapsTo('billing.insufficient_balance')]` son azúcar opcional procesado al compilar. No se requiere que las excepciones de dominio dependan de ellos. La configuración explícita puede sobrescribir atributos por prioridad; el compilador informa la regla ganadora y las sombreadas.

## Herencia, seguridad y errores

Una regla genérica para RuntimeException nunca debe ocultar una regla de dominio específica por accidente: prioridades explícitas y diagnóstico de shadowing permiten detectarlo. Si el mapper lanza o devuelve código no registrado, se clasifica como fallo de extensión, conserva causa original en diagnóstico saneado y usa internal.error. No se prueba el siguiente mapper para ocultar configuración rota.

Los predicados no reciben passwords, Request ni entidades; tampoco realizan queries. Reglas de tenant deben basarse en perfiles de política confiables, no ejecutar código obtenido de ese tenant. El runtime puede cachear la lista de candidatos por clase y versión del plan, pero no el resultado contextual.

## Pruebas contractuales

Verificar clase exacta frente a ancestro, dos interfaces, prioridades empatadas, null, unknown class y regla que falla. Permutar orden de registro debe producir idéntico plan y resultados. Cambiar locale o tenant no debe reutilizar un SemanticError de otra petición. Explicar mapping muestra ids y condiciones, sin datos personales.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](07_NORMALIZATION_AND_PHP_ERROR_CAPTURE.md) · [Siguiente](09_DOMAIN_TO_TRANSPORT_MAPPING.md)
