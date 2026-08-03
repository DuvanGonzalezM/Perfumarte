# Changelog de entrega — PRAIS

**Estado de partida:** commit `c10dd3e`, 22 de abril de 2026. Es el último estado
del código sobre el que se ejecutó la auditoría externa.

**Estado entregado:** 2 de agosto de 2026.

Este documento describe **todo** lo que cambió entre esas dos fechas, por qué, y
contra qué hallazgo del informe de auditoría responde cada cambio. Su propósito
es que cualquier diferencia que el equipo auditor encuentre respecto de su
informe tenga aquí una explicación trazable.

Verificación de integridad de la entrega: `MANIFEST.sha256`.

---

## Resumen

| | |
|---|---|
| Archivos modificados | 58 |
| Archivos nuevos | 8 |
| Archivos eliminados | 7 |
| Migraciones nuevas | 2 |
| Seeders nuevos | 2 |
| Pruebas automatizadas | 23, todas en verde |

Todo lo que sigue se verificó ejecutando el sistema contra una base MySQL real:
`php artisan migrate:fresh --seed` completo desde cero, y la suite de pruebas
en verde. Los resultados están al final de este documento.

---

## 1. Vulnerabilidades de seguridad corregidas

### 1.1 Toma de control de cuentas sin autenticación — CRÍTICA

**Qué pasaba.** Tres defectos encadenados permitían tomar el control de
cualquier cuenta en estado de contraseña predeterminada conociendo solo su
nombre de usuario:

1. `routes/auth.php` exponía `GET` y `PUT change-password/{username}` bajo el
   middleware `guest`, sin ninguna comprobación de identidad.
2. `PasswordChangeController` filtraba con `whereAnd()`, método que **no existe
   en Eloquent**: la llamada caía en `Query\Builder::dynamicWhere()` y no
   agregaba ni una sola condición al SQL. Los filtros `enabled` y
   `default_password` no se aplicaban. Es un código que parece seguro al leerlo.
3. `AuthenticatedSessionController` redirigía a esa URL **antes** de llamar a
   `$request->authenticate()`. El propio formulario de login entregaba al
   atacante la dirección exacta con la que tomar la cuenta, a cambio de un
   nombre de usuario válido y cualquier contraseña.

**Qué se hizo.**

- Se corrigió `whereAnd()` por `where()`.
- Las rutas salen del grupo `guest` y pasan a un middleware propio,
  `EnsurePasswordChangeAccess`, que solo admite dos vías: un **enlace de
  activación firmado con vencimiento**, o el propio titular autenticado sobre su
  propio usuario. Se añadió `throttle:6,1`.
- El login deja de emitir esa redirección: todas las comprobaciones sobre el
  estado de la cuenta se movieron **después** de validar la credencial.
- El enlace de activación lo emite ahora el **administrador** al crear,
  reactivar o restablecer una cuenta, y la interfaz se lo muestra una sola vez
  para que lo entregue al titular. Vigencia: 72 horas.
- La validación de la nueva contraseña pasa a exigir `Rules\Password::defaults()`.

**Por qué este diseño y no otro.** El sistema no tiene canal de correo —la tabla
`users` no tiene columna `email`— y las cuentas nuevas se crean con
`Str::password(16)`, una contraseña aleatoria que nadie llega a conocer. Por eso
existía el atajo de la redirección: era la única forma de que un usuario nuevo
entrara. Limitarse a firmar la URL no habría cerrado nada, porque el login se la
entregaba a cualquiera; y exigir la contraseña temporal habría dejado sin acceso
a todo usuario nuevo. Trasladar la emisión del enlace al administrador conserva
el flujo y cierra el vector, apoyándose en el canal de entrega fuera de banda que
la operación ya usa.

**Impacto operativo.** Las cuentas que hoy estén en producción con contraseña
predeterminada y sin enlace deben activarse con «Restablecer contraseña» desde
la ficha del usuario, lo que genera un enlace nuevo.

**Archivos:** `routes/auth.php`, `app/Http/Middleware/EnsurePasswordChangeAccess.php`
(nuevo), `app/Http/Controllers/Auth/PasswordChangeController.php`,
`app/Http/Controllers/Auth/AuthenticatedSessionController.php`,
`app/Http/Controllers/UserController.php`, `app/Http/Kernel.php`,
`resources/js/Pages/Auth/ChangePassword.vue`,
`resources/js/Components/ActivationLink.vue` (nuevo).

**Pruebas:** `tests/Feature/Security/AccountTakeoverTest.php` (6 casos).

### 1.2 IDOR de escritura en inventario — CRÍTICA

**Qué pasaba.** `PUT stock/update` acumulaba cuatro defectos: vivía bajo
`can:Ver Stock` —un permiso de **lectura**—, no validaba `warehouse_id` ni
comprobaba su pertenencia, **borraba físicamente** las referencias ausentes del
payload, y no usaba transacción. Como las 43 claves foráneas del proyecto son
`ON DELETE CASCADE`, ese borrado arrastraba `sale_details` y destruía histórico
de ventas. Un asesor comercial podía vaciar el inventario de otra sede con una
sola petición.

**Qué se hizo.** La ruta pasa a exigir un permiso nuevo, `Editar Stock`. Se
valida `warehouse_id` contra la base y se comprueba que la bodega pertenezca a
una sede del usuario. Las referencias ausentes se ponen **a cero** en vez de
borrarse. Todo el bloque queda en transacción con `lockForUpdate()`.

**Archivos:** `app/Http/Controllers/StockController.php`, `routes/views.php`.
**Pruebas:** `tests/Feature/Security/AuthorizationTest.php` (3 casos).

### 1.3 El importe de la venta lo fijaba el cliente — CRÍTICA

**Qué pasaba.** `storeSales` no tenía **una sola llamada a `validate()`**.
Guardaba `'total' => $request->total` y sumaba esa misma cifra a
`cash_registers.total_collected`, sin compararla nunca con los precios que el
propio servidor calculaba y escribía en `sale_details.price`. Un cajero que
interceptara la petición podía registrar una venta de $180.000 como $1.000: el
stock se descontaba correctamente, el detalle reflejaba los precios reales, y el
cierre de caja cuadraba contra la cifra falsificada. Descuadre invisible para la
auditoría.

**Qué se hizo.** El total lo calcula y lo fija el servidor. El importe que envía
el navegador ya no se usa; si difiere del calculado, se registra una advertencia
en el log con usuario, sede y ambas cifras. Se añadió validación completa del
payload, lista blanca de medios de pago, y comprobación de que el asesor al que
se imputa la venta pertenece a la sede.

Se optó por **registrar** la discrepancia en lugar de rechazar la venta: una
diferencia también puede venir de un desfase entre el cálculo del frontend y el
del servidor, y bloquear la caja por eso sería peor que dejar constancia.

**Archivos:** `app/Http/Controllers/SaleController.php`.
**Pruebas:** `tests/Feature/Security/SaleAmountTest.php` (4 casos).

### 1.4 Escalada al rol TI — ALTA

`updateUserRolePermission` y `storeUser` pasaban `$request->roles` directamente a
`syncRoles()` con la única regla `required`. La lectura ocultaba el rol TI a
quien no lo tuviera, pero la escritura no validaba nada: bastaba enviar su
identificador. Ahora los roles y permisos se validan contra la base y el rol `TI`
solo puede asignarlo un usuario que ya lo tenga.

### 1.5 Rutas sensibles sin control de autorización — ALTA

| Ruta | Antes | Ahora |
|---|---|---|
| `reportes/generate/…` | sin permiso: cualquier autenticado descargaba los informes financieros de toda la compañía | `can:Crear Reporte` |
| `reportes` | sin permiso | `can:Ver Reportes` |
| `users`, `users/{id}`, `api/permissions_roles/{id}` | sin permiso: cualquier autenticado listaba todos los usuarios con sus roles | `can:Ver Usuarios` |
| `ver-caja`, `cerrar-caja` | sin permiso | `can:Cerrar Caja` |
| `inventory/accept` | sin permiso; crea `InventoryValidation` y `CashRegister` | `can:Ver Inventario Sede` |
| `dashboard` | sin permiso | `can:Ver Dashboard` |

Además, la tabla `reports` no registraba autor: se añadió `user_id` y se escribe
en cada generación.

### 1.6 Cambio de contraseña sin exigir la actual — ALTA

`App\Http\Controllers\PasswordController::update` no pedía la contraseña actual:
quien obtuviera una sesión podía fijar una nueva sin conocer la anterior. Ahora
exige `current_password` y `Password::defaults()`. Su método `show()` devolvía
`view('auth.change-password')`, plantilla inexistente —`resources/views/` solo
contiene `app.blade.php`—, lo que producía un **500 permanente**; ahora renderiza
una página Inertia real.

### 1.7 Conexiones persistentes de base de datos

Se retiró `PDO::ATTR_PERSISTENT => true` de `config/database.php`. Con PHP-FPM las
conexiones persistentes sobreviven al final de la petición y arrastran estado
entre peticiones distintas.

> Nota para el equipo auditor: el informe recibido daba este punto por
> «ya corregido». No lo estaba: seguía presente en `config/database.php:64` en el
> último commit auditado. Ahora sí está corregido.

---

## 2. Integridad de datos

### 2.1 Ninguna operación multi-tabla estaba en transacción

Solo 3 de 17 operaciones que escriben en varias tablas usaban `DB::transaction`.
La más grave, `DispatchController::updateDispatch`, borraba todos los detalles
del despacho y los recreaba en un bucle: si el bucle fallaba, el borrado ya
estaba confirmado y el despacho quedaba vacío de forma irrecuperable.

Se añadió transacción a: `updateDispatch`, `storeReturnedQuantities`,
`SupplyReceptionController::receive`, `CashRegisterController::store`,
`PurchaseOrderController::updateOrders`, `RequestPraisController::update`,
`AuditController::confirmCashAudit`, `AuditController::storeAuditInventory`,
`DamageReturnController::approvedDamageReturn` y `approveReturnFinal`,
`ConsumableController::storeConsumable`, `approvedConsumableReturn` y
`approveReturnFinal`, `RepackageController::storeRepackage` y `updateRepackage`,
`InventoryLocationController::accept`, `AssignmentController::updateAssignment`,
`UserController::storeUser` y `updateUserRolePermission`.

En `DamageReturnController` y `ConsumableController` había además un `return` a
mitad del bucle que abandonaba dejando confirmados los movimientos de stock ya
aplicados; se sustituyó por una excepción que revierte la transacción completa.

### 2.2 No existía ninguna primitiva de bloqueo en el proyecto

`grep -rn "lockForUpdate\|sharedLock" app/` no devolvía un solo resultado. El
100 % de las actualizaciones de inventario era leer-luego-escribir sin bloqueo:
dos cajeros vendiendo a la vez con stock 10 leían ambos 10 y ambos escribían 5;
se vendían 10 unidades y el sistema descontaba 5. `DB::transaction` con el nivel
`REPEATABLE READ` de MySQL **no** previene esta pérdida de actualización.

Se añadió `lockForUpdate()` en todas las lecturas de inventario, caja y despacho
que después se modifican.

### 2.3 Operaciones no idempotentes

Un doble clic o un reintento del navegador duplicaba efectos. Se añadió guarda de
estado dentro de la transacción y con la fila bloqueada en:

- `approvedDispatch` — descontaba el stock dos veces.
- `SupplyReceptionController::receive` — ingresaba el stock dos veces.
- `storeReturnedQuantities` — reintegraba el stock dos veces.
- `CashRegisterController::store` — **una caja ya cerrada podía volver a
  cerrarse con cifras distintas**, sobrescribiendo el arqueo original.
- `InventoryLocationController::accept` — abría dos cajas para el mismo día.
- `DamageReturn` y `Consumable`: `approveReturnFinal` daba de baja dos veces.

### 2.4 Borrado de sede en cascada — CRÍTICA

`DELETE /sedes/delete/{location_id}` ejecutaba `Location::destroy()`. Con las 43
claves foráneas en `ON DELETE CASCADE` sin una sola excepción, una única petición
HTTP arrasaba con la caja de la sede, sus ventas, el detalle de esas ventas, sus
auditorías, sus bodegas, su inventario y las seis tablas que dependen de él. Sin
transacción, sin confirmación y sin registro.

Se añadió `SoftDeletes` al modelo `Location` y la columna `deleted_at`. Marcar la
sede como eliminada corta la cascada en el origen y conserva todo el histórico.
Se añadió además una guarda que impide eliminar una sede con caja sin cerrar.

### 2.5 Truncamiento silencioso de stock

`SaleController` calcula el descuento como `(quantity * units) * 0.5`, que para
una venta de 5 ml da `2.5`. Ese valor se escribía en `inventories.quantity`,
declarada `INT`: MySQL truncaba en silencio y el inventario perdía media unidad
en cada venta fraccionada.

La columna pasa a `DECIMAL(12,2)`. Los importes en pesos se dejan como enteros: el
COP no maneja centavos en retail y `INT` es su representación exacta.

### 2.6 El patrón `(int) $request->campo ?? null`

Nunca produce `null`, porque el cast se evalúa antes que el operador: omitir
`enabled` del payload **deshabilitaba al usuario en silencio**, y omitir
`boss_user` o `zone_id` escribía `0`. Se sustituyó por comprobación explícita de
presencia en `UserController`.

---

## 3. Reproducibilidad: el sistema ya se levanta desde cero

Este bloque responde al alegato de documentación técnica incompleta. Antes de
estos cambios, un tercero que recibiera el código **no podía poner PRAIS en
funcionamiento**.

### 3.1 Las migraciones no reproducían el esquema

Cinco columnas que el código lee y escribe no las creaba ninguna migración:

| Columna | Dónde se usa | Consecuencia de su ausencia |
|---|---|---|
| `warehouses.price5` | `SaleController:76` | las ventas de 5 ml se facturaban a $0 |
| `users.first_login` | `CheckFirstLogin`, `PasswordController` | escritura a columna inexistente |
| `audit_cash.cash_register_id` | `AuditController:84` | fallo al confirmar arqueo |
| `products.name` | `LocationsController`, `SaleController` | vistas de venta y arqueo en blanco |
| `sales.location_id` | `CashRegisterController:70` | **el listado de ventas por sede salía siempre vacío** |

Además `inventories.position` se declaró `NOT NULL` sin valor por defecto y
**ninguno** de los diez `Inventory::create()` del proyecto la envía: con
`'strict' => true`, toda creación de inventario fallaba en una base nueva.

La migración `2026_08_02_000000_reconcile_schema_drift` declara las columnas
faltantes y hace `position` nullable. `users.first_login` no se añadió: se
eliminó su uso, porque el estado real vive en `default_password`.

> **Nota importante sobre los tipos.** Los tipos de estas columnas se
> **infirieron del uso que hace el código**, no del esquema productivo, al que
> IVYS no tiene acceso desde el 2 de abril de 2026. Deben validarse contra
> producción con `SHOW CREATE TABLE`. La migración usa comprobaciones
> `Schema::hasColumn()`, de modo que sobre una base cuyo esquema ya tenga esas
> columnas se omiten sin error y sin alterar nada.

### 3.2 No había fuente de verdad de roles y permisos

`DatabaseSeeder` estaba vacío. Los permisos y roles de Spatie existían
**únicamente como datos en el entorno productivo**, creados a mano por la
interfaz. `php artisan migrate --seed` producía un sistema sin ningún permiso, en
el que nadie podía acceder a nada.

`database/seeders/RolePermissionSeeder.php` declara ahora **55 permisos y 12
roles**. El inventario de permisos se extrajo del propio código —los `can:` y
`role:` de `routes/views.php` y los `can()` e `is()` de `resources/js`—, que es la
fuente de verdad de facto.

> **Nota sobre la matriz permiso→rol.** Los **nombres** de los permisos son
> exactos: proceden del código y cualquier discrepancia produciría un 403. La
> **asignación** de permisos a cada rol es una **propuesta razonada** a partir de
> la semántica de cada perfil; reconstruirla con exactitud exigiría
> `SELECT name FROM permissions` y la tabla `role_has_permissions` de producción.
> Debe validarse con el negocio antes de darla por definitiva.

El seeder es aditivo e idempotente (`firstOrCreate` y `givePermissionTo`, no
`syncPermissions`): ejecutarlo sobre una base con datos no revoca ninguna
asignación existente.

`database/seeders/AdminUserSeeder.php` crea la cuenta técnica inicial con rol
`TI`. Sin ella una instalación nueva queda sin ningún usuario.

### 3.3 El `README` no mencionaba `migrate`

Los pasos de despliegue eran `make`, `make app_key`, `make compile`, `make serve`.
Un tercero que siguiera el documento obtenía una aplicación **sin tablas**. El
`README` incluye ahora la secuencia completa, comandos de verificación posterior,
y la explicación del flujo de credenciales. Se añadieron los targets `migrate`,
`seed`, `fresh`, `test` y `lint` al `makefile`.

### 3.4 Deriva de permisos entre perfiles Administrador

`UserController::storePermission` usaba
`Role::whereIn('name', ['Administrador','TI'])->firstOrFail()`, que devuelve **un
solo rol**: cada permiso nuevo se concedía a `Administrador` **o** a `TI` según
cuál tuviera el `id` menor, nunca a ambos —al contrario de lo que sugería el
nombre de la variable—. Sumado a la caché de permisos de 24 horas, esta es la
causa de la inconsistencia de ACL entre dos perfiles Administrador que reporta el
informe de auditoría. La observación del auditor era correcta; esta era la causa.
Ahora se itera sobre ambos roles.

---

## 4. Errores 500 corregidos

El patrón `$user->location_user[0]->warehouses[0]` aparecía en siete puntos. Las
rutas afectadas están protegidas por permisos típicos de Administrador, Gerencia
y Jefe de operaciones: perfiles que **no tienen filas en `location_user`**. Con la
relación vacía, `location_user[0]` es `null` y la lectura produce
*Attempt to read property on null* → **HTTP 500**.

| Ubicación | Módulo afectado |
|---|---|
| `DamageReturnController::createDamageReturn` | Devoluciones |
| `SaleController::sales`, `createSales`, `storeSales` | Ventas |
| `SupplyReceptionController::show` | Recepción de Insumos |
| `InventoryLocationController::accept`, `current` | Inventario de sede |
| `CheckInventoryAccess` (middleware) | fallaba antes de llegar a ningún controlador |
| `StockController::getMultipleInventory` | Stock múltiple |

Todos devuelven ahora un mensaje explicativo en lugar de un 500.

Otros 500 corregidos:

- **`ConsumableController`** invocaba `DamageReturn::findOrFail()` **sin importar
  la clase**: en un archivo con `namespace App\Http\Controllers`, el nombre se
  resolvía al namespace actual → `Class "App\Http\Controllers\DamageReturn" not
  found`. Dos rutas activas del flujo de devolución de consumibles estaban rotas.
- **`Inertia::render('Dispatch/Dispatchdetail')`** con `d` minúscula, en
  `DispatchController` y `returnedDispatchController`. El archivo real es
  `DispatchDetail.vue`; en Linux, sensible a mayúsculas, ese nombre no está en
  `public/build/manifest.json` y `@vite` lanza `ViteException`.
- **`SaleController::storeSales`** encadenaba `->first()->inventory_id` sobre la
  búsqueda del producto «Bolsa de regalo»: si la bodega no lo tenía, 500 al
  vender.
- **`App\Rules\Recaptcha`** accedía a `$response['success']` sin comprobar que la
  llamada a Google hubiera devuelto algo.

---

## 5. Discordancias entre el menú y las rutas

Cuatro entradas del menú lateral se condicionaban por un permiso distinto del que
exigía su ruta destino: el usuario veía la opción y recibía un 403 al pulsarla.

| Menú | Ruta destino | Exigía antes | Ahora |
|---|---|---|---|
| `Ver Dashboard` | `dashboard` | ningún permiso | `can:Ver Dashboard` |
| `Ver Inventario Sede` | `inventory.current` | `role:Asesor comercial` | `can:Ver Inventario Sede` |
| `Ver Transformaciones` | `LabTransformation.list` | `can:Ver Reenvases` | `can:Ver Transformaciones` |
| `Ver Reportes` | `reportes` | ningún permiso | `can:Ver Reportes` |

Además:

- **Usuarios y Sedes no figuraban en el menú lateral.** Sus únicos accesos
  estaban en `AccountControls.vue`, condicionados por una lista fija de **roles**
  en lugar de por permiso, de modo que `TI`, `Subdirector`, `Supervisor` y
  `Jefe de operaciones` no los veían aunque tuvieran el permiso. Ahora se
  condicionan por permiso, igual que el resto del menú.
- **Novedades no tenía ningún enlace en todo el frontend**, pese a tener rutas y
  páginas completas: el módulo era inalcanzable. Se añadió al menú.

---

## 6. Frontend

- **Se eliminó la única dependencia externa en runtime.**
  `Components/Table.vue` cargaba el idioma de DataTables desde
  `https://cdn.datatables.net/...`. DataTables **no inicializa la tabla hasta que
  ese `$.ajax` —sin `timeout`— resuelve**: si la red corporativa descarta la
  salida HTTPS, la tabla no se dibuja durante todo el tiempo de espera del
  navegador. Afectaba a 35 páginas. La traducción va ahora incrustada.
- **Los mensajes flash nunca llegaban a la vista.** Los controladores usaban
  `with('success')` y `with('error')` en once puntos, pero
  `HandleInertiaRequests::share()` no compartía la sesión flash: los mensajes se
  perdían. Ahora se comparten.
- **`resources/js/Components/ActivationLink.vue`** (nuevo) muestra al
  administrador el enlace de activación, con botón de copia.
- **`resources/js/Pages/Auth/UpdatePassword.vue`** (nuevo) sustituye la vista
  Blade inexistente del cambio de contraseña autenticado.

---

## 7. Base de datos: restricciones e índices

`2026_08_02_000001_harden_schema_constraints`:

- **`users.username`** no tenía índice `unique` a nivel de base de datos: la
  unicidad dependía solo de la regla de validación, que no protege frente a dos
  peticiones concurrentes.
- **`boss_user` y `zone_id`** se declararon como `string()` con `constrained()`
  encadenado, que sobre una columna de texto **es inerte**: no existía ninguna
  clave foránea real. Se convierten a `BIGINT UNSIGNED` con clave foránea. Los
  valores que no apuntan a ninguna fila —incluido el `0` que escribía el patrón
  descrito en §2.6— pasan a `NULL` antes de crear la restricción.
- **Índices** en `suppliers(status)`, `suppliers(name)`,
  `purchase_orders(created_at)`, `reports(created_at)`, `reports(type_report)`,
  `zones(zone_name)`, `products(category,status)` y `products(reference)`.
- **Casts booleanos** en `users.enabled` y `products.status`. Sin ellos, y con
  `PDO::ATTR_EMULATE_PREPARES` activo, MySQL devuelve el `tinyint(1)` como la
  cadena `"1"` y cualquier comparación estricta falla. Es la fragilidad latente
  que probablemente explica la observación del auditor sobre los tipos de esas
  columnas.

Todas las operaciones están guardadas para poder ejecutarse sobre una base con
datos: si un dato existente impide aplicar una restricción, se informa por
consola y se continúa en lugar de abortar la migración completa.

---

## 8. Infraestructura

- **`.dockerignore` (nuevo).** No existía, y `docker/Dockerfile` hace `COPY . .`:
  cualquier imagen construida incluía `.env` con credenciales reales, el
  directorio `.git` completo, `node_modules` y `vendor`.
- **La etapa de producción del `Dockerfile` no se construía nunca.**
  `docker-compose.yml` y `docker-compose.prod.yml` fijaban ambos
  `target: builder`. La etapa 2 —la única que copia el `php.ini` de producción y
  el `supervisord.conf`, y que ejecuta `composer install --no-dev`— quedaba sin
  construir: **producción corría con dependencias de desarrollo y sin su
  configuración**. La etapa se nombró `production` y `docker-compose.prod.yml`
  apunta a ella.
- **OPcache nunca se habilitaba.** El `Dockerfile` instalaba
  `pdo pcntl pdo_mysql gd zip` y jamás ejecutaba `docker-php-ext-enable opcache`,
  así que todo el bloque `[opcache]` del `php.ini` era inerte. Ahora se instala
  explícitamente. Se añadió también `pcntl` a la etapa de producción, donde
  faltaba pese a necesitarlo los workers de cola y Reverb.
- **`opcache.validate_timestamps`** pasa a `1` en el `php.ini` **local**, donde
  estaba en `0` y obligaba a reiniciar php-fpm tras cada cambio de código. En
  producción se mantiene en `0`, que es lo correcto.
- **`certbot/` y el material TLS** se retiraron del repositorio el 29 de julio de
  2026. La gestión de certificados no formaba parte del alcance contractual. Se
  eliminaron los montajes correspondientes de ambos `docker-compose` y el bloque
  `listen 443 ssl` del nginx local, que apuntaba a rutas inexistentes e impedía
  arrancar. Producción no depende de ello: usa `/etc/ssl_aws`, montado desde
  `docker/production/ssl`.

  > **Acción pendiente para el operador del entorno.** La clave privada TLS
  > estuvo versionada durante meses y permanece en el historial del repositorio
  > original y en todo clon existente. **Debe revocarse el certificado y
  > emitirse un par nuevo sin reutilizar la clave.** IVYS no tiene acceso al
  > entorno desde el 2 de abril de 2026 y no puede ejecutarlo.

- **`public/hot` y `bootstrap/cache/routes-v7.php` se eliminaron del checkout.**
  Ambos son artefactos locales, y ambos rompen un despliegue si viajan con el
  código:
  - Con `public/hot` presente, `@vite` sirve los assets desde un servidor Vite
    inexistente y **la aplicación queda en blanco**.
  - `bootstrap/cache/routes-v7.php` era la caché de rutas del 22 de abril de
    2026. Durante la verificación de esta entrega se comprobó **empíricamente**
    que Laravel la estaba sirviendo e ignorando por completo `routes/`: los
    cambios de autorización no surtían efecto hasta eliminarla. Es un riesgo
    real de despliegue, y por eso `.dockerignore` excluye `bootstrap/cache/*`.

---

## 9. Pruebas

**Estado anterior.** Los directorios `tests/` no estaban vacíos: contenían 9
archivos con 25 casos. Pero **23 de esos 25 fallaban**, y lo hacían desde el
primer día: `UserFactory` era la de Laravel Breeze sin adaptar y generaba
`email`, `email_verified_at` y `remember_token`, columnas que la tabla `users` de
este proyecto **nunca ha tenido**. Además, `RegistrationTest`, `PasswordResetTest`,
`EmailVerificationTest` y `PasswordConfirmationTest` ejercitaban rutas que no
existen en el proyecto, y `ProfileTest` ejercitaba rutas comentadas en
`routes/web.php`.

Es decir: la cobertura efectiva de lógica de negocio era **cero**, y ni siquiera
el andamiaje ejecutaba.

**Estado entregado.** Se corrigió `UserFactory` contra el esquema real, se
eliminó el andamiaje que probaba funcionalidad inexistente, y se escribió una
suite de regresión sobre los defectos corregidos:

| Archivo | Casos | Qué prueba |
|---|---|---|
| `tests/Feature/Auth/AuthenticationTest.php` | 4 | login por `username`, credencial incorrecta, logout |
| `tests/Feature/Security/AccountTakeoverTest.php` | 6 | §1.1: acceso sin firma, oráculo del login, enlace válido, enlace vencido, cuenta deshabilitada |
| `tests/Feature/Security/AuthorizationTest.php` | 8 | §1.2, §1.4, §1.5: IDOR de stock, borrado físico, bodega ajena, escalada a TI, rutas sin permiso |
| `tests/Feature/Security/SaleAmountTest.php` | 4 | §1.3, §2.5: total del servidor, imputación por sede, medios de pago, stock fraccionado |
| `tests/Unit/ExampleTest.php` | 1 | — |

**23 casos, 46 aserciones, todos en verde.**

Esto no es una suite completa: cubre las correcciones de seguridad, no el
conjunto de los flujos de negocio. Una suite de los flujos críticos —venta,
despacho, cierre de caja, auditoría, cada uno con su prueba de permiso— sigue
siendo trabajo pendiente.

---

## 10. Lo que NO se cambió, y por qué

Se dejó constancia expresa para que la ausencia de estos cambios no se
interprete como omisión:

- **No se aplicó formateo automático al conjunto del proyecto.** Laravel Pint
  reporta deuda de estilo preexistente en la mayoría de los archivos. Aplicarlo
  habría producido un diff masivo en el que las correcciones sustantivas
  quedarían sepultadas entre cambios cosméticos, y este documento dejaría de ser
  verificable línea a línea. Solo los archivos nuevos se entregan formateados.
- **No se extrajo la lógica a una capa de servicio.** El proyecto no tiene
  `app/Services/` y concentra la lógica en 25 controladores. Es una observación
  correcta del informe de auditoría, pero es una refactorización de semanas y
  ejecutarla junto a correcciones de seguridad habría hecho imposible verificar
  unas y otras por separado.
- **No se paginó Despachos en servidor** (959 registros enviados al cliente).
- **No se cambiaron las 43 claves foráneas `ON DELETE CASCADE`** a `restrict`.
  Se cortó la cascada en su único disparador real —el borrado de sede, §2.4— con
  `SoftDeletes`, que resuelve el riesgo sin una migración estructural sobre 43
  restricciones que no puede validarse contra el esquema productivo.
- **No se resolvió la dependencia de identificadores fijos.** Cinco módulos
  asumen bodegas `1`, `2`, `3` y productos `1`, `2` con literales escritos a mano
  (`whereIn('warehouse_id', [2, 3])`, `Warehouse::findOrFail(3)`). Si en
  producción esos identificadores no son los que el código asume, los selectores
  de esos módulos se vacían simultáneamente. Es una hipótesis que **solo puede
  confirmarse consultando la base de datos de producción**.

---

## 11. Verificaciones pendientes que requieren acceso a producción

IVYS no tiene acceso al entorno desde el 2 de abril de 2026. Los siguientes
puntos quedan indeterminados sin él:

| # | Consulta | Qué decidiría |
|---|---|---|
| 1 | `SHOW CREATE TABLE users; SHOW CREATE TABLE products;` | Los tipos reales de `enabled` y `status` en producción, y la validación de las columnas declaradas en §3.1 |
| 2 | `SELECT user_id, username FROM users WHERE LENGTH(password) < 50;` | Detecta contraseñas sin hashear sin exponer ninguna: un hash bcrypt son 60 caracteres |
| 3 | `SELECT name FROM permissions;` y `role_has_permissions` | Permitiría sustituir la matriz propuesta de §3.2 por la real |
| 4 | `SELECT warehouse_id, name FROM warehouses;` y `SELECT product_id FROM products WHERE product_id IN (1,2);` | Confirmaría o descartaría la hipótesis de §10 sobre los identificadores fijos |
| 5 | Logs de PHP-FPM del 22-may al 4-jul-2026 | Los `Attempt to read property on null` y los `ViteException` descritos en §4 aparecerían con fecha y URI |

---

## 12. Resultado de la verificación de esta entrega

Ejecutado el 2 de agosto de 2026 contra MySQL 8.4 en contenedor:

```
php artisan migrate:fresh --seed
  34 migraciones aplicadas, sin errores
  RolePermissionSeeder: 55 permisos y 12 roles
  AdminUserSeeder: usuario inicial creado

./vendor/bin/pest
  Tests: 23 passed (46 assertions)
```

Esquema resultante comprobado columna a columna: `warehouses.price5`,
`audit_cash.cash_register_id`, `products.name`, `sales.location_id`,
`reports.user_id` y `locations.deleted_at` presentes;
`inventories.quantity` en `decimal(12,2)`; `inventories.position` nullable;
`users.boss_user` y `users.zone_id` en `bigint unsigned` con clave foránea;
índice `unique` en `users.username`.
