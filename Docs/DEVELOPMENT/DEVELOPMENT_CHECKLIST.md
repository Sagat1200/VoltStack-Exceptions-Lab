# EXCEPTION-LAB · DEVELOPMENT CHECKLIST

## Estado general

- [x] Crear base documental de `Docs/DEVELOPMENT`
- [x] Fijar ruta real de implementacion en el framework
- [x] Definir bloques iniciales `EXC-001` a `EXC-012`
- [x] Registrar trazabilidad documental minima
- [x] Abrir `EXC-001` en codigo
- [x] Crear estructura base del modulo y del namespace real `Quantum\Exceptions`
- [x] Definir contratos publicos iniciales
- [x] Definir modelos inmutables minimos
- [x] Abrir suite de pruebas contractuales iniciales

## Checklist por bloque

### EXC-001 · Apertura del modulo

- [x] Apertura documental del bloque
- [x] Crear estructura de carpetas en `vendor/voltstack/framework/src/Quantum/Exceptions`
- [x] Definir convenciones internas del modulo
- [x] Preparar punto basal para Core, Contracts y Model
- [x] Registrar resultado en log, matrix y versions

### EXC-002 · Contratos y modelo

- [x] `ExceptionManagerInterface`
- [x] `ExceptionNormalizerInterface`
- [x] mapper semantico base sin romper el legacy `ExceptionMapperInterface`
- [x] `TransportMapperInterface`
- [x] `ExceptionReporterInterface`
- [x] `ExceptionRendererInterface`
- [x] DTOs/public contracts cerrados

### EXC-003 · Contexto y scope

- [x] `ExceptionContext`
- [x] scope lifecycle base
- [x] occurrence registry scoped
- [x] politicas minimas de identidad de ocurrencia

### EXC-004+ · Siguientes bloques

- [x] Normalizacion y captura
- [x] Mapping y catalogo
- [x] Manager y pipeline
- [x] Reporting y dedup
- [x] Reporters basales y observabilidad
- [x] HTTP/CLI base
- [x] Problem Details
- [x] SPA
- [x] Runtime persistente
- [ ] Configuracion y gates
