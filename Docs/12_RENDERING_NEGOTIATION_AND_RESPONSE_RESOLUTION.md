# 12 — Negociación y resolución de presentación

**VoltStack / Quantum / Exceptions · Especificación técnica propuesta 1.0 · 4 de octubre de 2026**

## Selección de representación

ResponseResolver recibe descriptor y TransportContext verificado. Primero el bridge determina si es HTTP, CLI, Job o boundary. Para HTTP, el perfil de ruta declara representaciones soportadas. Una señal SPA solo habilita su protocolo en endpoints registrados y con versión aceptada; un header no concede permisos ni convierte un endpoint arbitrario en SPA.

Orden HTTP: protocolo SPA válido y ruta compatible → perfil explícito API/HTML → negociación Accept sobre formatos permitidos → fallback del perfil. Accept se procesa con q-values, especificidad y límite de longitud. `q=0` excluye un tipo. Un endpoint API sin Accept usa JSON; una navegación browser sin Accept usa HTML. Un conflicto irresoluble usa 406 con cuerpo mínimo permitido o vacío si nada es aceptable; no vuelve a negociar recursivamente ese error.

## Plan y renderer

TransportMapper define status, headers y acción. Renderer produce bytes y mediaType. El ResponseGuard valida que el resultado conserve status permitido, presupuesto y seguridad. El renderer no puede reducir un 500 a 200 silenciosamente. Un fallback local de componente es una decisión explícita de la operación, descrita en 17, y no una normalización universal a éxito.

El output no se envía desde el manager. El emisor propietario controla HEAD, cancelación, compresión y si ya hay headers. HEAD conserva status/headers de la representación pero suprime cuerpo. Los errores normales no generan 204/304. Content-Length se calcula después del encoding final o se deja al servidor; jamás se reutiliza el de la respuesta fallida.

## Fallo del renderer

HTML falla → documento estático mínimo prevalidado; JSON falla → bytes JSON constantes; SPA falla → envelope mínimo compatible si se conoce la versión; CLI falla → línea fija en stderr. No se hace fallback SPA→HTML dentro de una respuesta que el cliente interpretará como protocolo. Si la versión SPA es incompatible, aplicar 16.

Un fallo con transporte comprometido devuelve abort_transport. No se agregan trazas al body parcialmente enviado. Un stream con protocolo terminal propio puede cerrar con su frame de error, pero el HTTP status inicial no cambia y la observabilidad registra fallo de stream.

## Validación y negociación adversarial

Probar Accept ausente, comodines, q=0, sintaxis inválida, un millón de valores (rechazo por límite), HTML escapado, JSON inválido y HEAD. Comprobar que datos privados no se filtran al elegir debug, que plugin no puede inyectar Set-Cookie/Location arbitrarios y que no se ejecuta una segunda emisión tras conexión cerrada. Los fallbacks deben tener pruebas de bytes exactos y no depender del catálogo de traducciones.

---

[Índice](00_EXCEPTION_SYSTEM_PROJECT_CONTEXT.md) · [Anterior](11_REPORTER_PIPELINE_AND_DELIVERY.md) · [Siguiente](13_HTTP_EXCEPTIONS_AND_STATUS_SEMANTICS.md)
