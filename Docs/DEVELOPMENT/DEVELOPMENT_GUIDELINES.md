# EXCEPTION-LAB · DEVELOPMENT GUIDELINES

## 1. Proposito

Estas guias fijan como debe ejecutarse el desarrollo incremental de `VoltStack\Quantum\Exceptions` en el framework real.

## 2. Ubicaciones autoritativas

- Diseno normativo: `vendor/voltstack/exception-lab/Docs`
- Desarrollo operativo: `vendor/voltstack/exception-lab/Docs/DEVELOPMENT`
- Implementacion real: `vendor/voltstack/framework/src/Quantum/Exceptions`
- Namespace PHP actual del framework: `Quantum\Exceptions`
- Namespace normativo de referencia del lab: `VoltStack\Quantum\Exceptions`

## 3. Reglas de trabajo

1. Antes de cada bloque:
   - actualizar checklist, backlog y log con el alcance del ciclo
   - registrar impacto previsto en la matriz
2. Durante cada bloque:
   - respetar el namespace real del framework `Quantum\Exceptions` mientras el autoload PSR-4 del framework no cambie
   - conservar trazabilidad documental con el namespace normativo `VoltStack\Quantum\Exceptions`
   - preferir contratos y objetos de valor inmutables
   - introducir bridges para HTTP, SPA, CLI, Jobs y runtime en lugar de acoplar el nucleo
3. Despues de cada bloque:
   - registrar resultados reales
   - actualizar la version documental/progreso
   - marcar pendientes y riesgos residuales

## 4. Invariantes que no se negocian

- El dominio no conoce HTTP.
- `Throwable::getCode()` no define automaticamente status HTTP.
- `report()` y `handle()` comparten identidad de ocurrencia cuando se trata del mismo objeto.
- Si el transporte ya comenzo, no se emite una segunda respuesta.
- `Committed`, `Partial` y `Unknown` no se degradan a `None` por conveniencia.
- Un retry automatico de mutacion incierta queda prohibido en v1.
- No se exponen stack traces, SQL, secrets, tokens ni datos privados en salidas publicas.
- Ningun singleton puede retener request, user, tenant, scope o `Throwable` de una operacion anterior.

## 5. Criterios de diseno

- API publica pequena y estable
- DTOs cerrados y tipados
- limites explicitos de bytes, profundidad, cantidad y tiempo
- saneamiento temprano y proyeccion publica por allowlist
- pruebas contractuales antes de ampliar ergonomia DX

## 6. Politica de pruebas por bloque

- Cada bloque debe definir pruebas unitarias/contractuales minimas
- Los casos de privacidad, no duplicacion y aislamiento por scope tienen prioridad alta
- Si una prueba no puede ejecutarse en el ciclo, debe quedar anotada como deuda explicita

## 7. Politica de cambios

- No introducir compatibilidad binaria inventada con modulos aun no certificados
- No mezclar helpers ergonomicos con rutas paralelas al pipeline central
- No dar por implementado un gate por existir documentacion; se exige evidencia ejecutable
