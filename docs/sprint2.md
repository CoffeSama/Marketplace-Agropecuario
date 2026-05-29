# Documentación Sprint 2 — Marketplace Agropecuario

## Resumen del Sprint

El Sprint 2 extiende la plataforma construida en el Sprint 1 con tres historias de usuario orientadas al core comercial del sistema: publicación de productos agrícolas, sistema de preventa con anticipo, y búsqueda geográfica por proximidad.

| Historia | Título | Estado |
|----------|--------|--------|
| US05 | Publicación de cosecha | Completado |
| US06 | Preventa de cosecha | Completado |
| US07 | Buscador por cercanía | Completado |

---

## US05 — Publicación de Cosecha

### Historia de usuario
> Como **productor verificado**, quiero publicar mis productos agrícolas en el marketplace para que los compradores puedan verlos y adquirirlos.

### Criterios de aceptación cumplidos

- Solo productores con estado `verificado` pueden publicar productos.
- El formulario requiere: nombre, categoría, precio, cantidad disponible, unidad de medida, descripción (mín. 20 caracteres), fecha de disponibilidad y al menos una imagen.
- Se pueden subir hasta 5 imágenes por producto (JPG, PNG, WEBP, máx. 2 MB c/u).
- Si la fecha de disponibilidad es futura, el producto se marca automáticamente como **preventa**.
- El marketplace muestra todos los productos publicados con paginación (12 por página).

### Flujo de uso

1. El productor ingresa a **Mis Productos** desde el menú lateral.
2. Hace click en **Agregar producto**.
3. Completa el formulario y sube las imágenes.
4. El sistema valida los datos y guarda el producto.
5. El producto aparece inmediatamente en el marketplace público.

### Implementación técnica

**Controlador:** `app/Http/Controllers/ProductoController.php`

Métodos:
- `index()` — lista los productos del productor autenticado.
- `create()` — muestra el formulario (requiere rol productor + estado verificado).
- `store()` — valida, guarda el producto e imágenes dentro de una transacción DB.
- `marketplace()` — muestra todos los productos publicados (con filtro de cercanía en US07).

**Modelos:**

`Producto` (`app/Models/Producto.php`)
- Relación `productor()` → `belongsTo(User::class, 'user_id')`
- Relación `imagenes()` → `hasMany(ProductoImagen::class)`
- Relación `preventas()` → `hasMany(Preventa::class)`
- Cast `fecha_disponibilidad` → `date`

`ProductoImagen` (`app/Models/ProductoImagen.php`)
- Tabla: `producto_imagenes`
- Relación `producto()` → `belongsTo(Producto::class)`

**Vistas:**
- `resources/views/productos/create.blade.php` — formulario de publicación
- `resources/views/productos/index.blade.php` — mis productos
- `resources/views/productos/marketplace.blade.php` — marketplace público

### Esquema de base de datos

**Tabla `productos`**

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | bigint PK | Identificador |
| `user_id` | bigint FK → users | Productor dueño |
| `nombre` | varchar(120) | Nombre del producto |
| `categoria` | varchar(80) | Categoría |
| `precio` | decimal(10,2) | Precio por unidad |
| `cantidad_disponible` | decimal(10,2) | Stock disponible |
| `unidad_medida` | varchar(30) | kg, quintal, arroba, etc. |
| `descripcion` | text | Descripción detallada |
| `fecha_disponibilidad` | date | Fecha desde que estará disponible |
| `estado_disponibilidad` | enum | `disponible` / `preventa` |
| `estado` | enum | `publicado` / `oculto` |

**Tabla `producto_imagenes`**

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | bigint PK | Identificador |
| `producto_id` | bigint FK → productos | Producto al que pertenece |
| `ruta` | varchar | Ruta en `storage/app/public/productos/` |

### Rutas

```
GET  /marketplace          productos.marketplace
GET  /mis-productos        productos.index
GET  /mis-productos/agregar  productos.create
POST /mis-productos        productos.store
```

---

## US06 — Preventa de Cosecha

### Historia de usuario
> Como **comprador**, quiero reservar productos que aún no están disponibles pagando un anticipo, para asegurar mi compra antes de la cosecha.

### Criterios de aceptación cumplidos

- Solo compradores pueden realizar preventas.
- Solo se pueden reservar productos con `estado_disponibilidad = preventa`.
- El sistema calcula automáticamente: total, anticipo (40%) y saldo pendiente (60%).
- El comprador puede ver todas sus preventas en **Mis Preventas**.
- El productor puede ver las reservas de sus productos en **Ventas Futuras**.
- El comprador puede completar el pago del saldo cuando la fecha de disponibilidad ya pasó.
- El pago no puede completarse si la cosecha aún no está disponible.

### Flujo de uso — Comprador

1. Ingresa al marketplace y encuentra un producto en preventa.
2. Ingresa la cantidad a reservar y confirma.
3. El sistema descuenta la cantidad del stock disponible y registra la preventa.
4. En **Mis Preventas** ve el estado: anticipo pagado, saldo pendiente y fecha de entrega.
5. Cuando llega la fecha, puede completar el pago del saldo restante.

### Flujo de uso — Productor

1. Ingresa a **Ventas Futuras** desde el menú lateral.
2. Ve todas las reservas activas sobre sus productos con datos del comprador y montos.

### Implementación técnica

**Controlador:** `app/Http/Controllers/PreventaController.php`

Métodos:
- `store(Request $request, Producto $producto)` — crea la preventa, calcula montos y descuenta stock en transacción.
- `misPreventas()` — lista preventas del comprador autenticado.
- `ventasFuturas()` — lista preventas de los productos del productor autenticado.
- `completarPago(Preventa $preventa)` — marca la preventa como completada si la cosecha ya está disponible.

**Modelo:** `Preventa` (`app/Models/Preventa.php`)
- Relación `producto()` → `belongsTo(Producto::class)`
- Relación `comprador()` → `belongsTo(User::class, 'comprador_id')`
- Cast `fecha_disponibilidad` → `date`

**Cálculo de montos:**
```
total    = precio × cantidad
anticipo = total × 0.40
saldo    = total × 0.60
```

### Esquema de base de datos

**Tabla `preventas`**

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | bigint PK | Identificador |
| `producto_id` | bigint FK → productos | Producto reservado |
| `comprador_id` | bigint FK → users | Comprador |
| `cantidad` | decimal(10,2) | Cantidad reservada |
| `total` | decimal(10,2) | Monto total |
| `anticipo` | decimal(10,2) | 40% pagado al reservar |
| `saldo` | decimal(10,2) | 60% pendiente |
| `estado` | enum | `pendiente_saldo` / `completado` / `cancelado` |
| `fecha_disponibilidad` | date | Fecha de entrega esperada |

### Rutas

```
POST /preventas/{producto}              preventas.store
GET  /mis-preventas                     mis-preventas
GET  /ventas-futuras                    ventas-futuras
POST /preventas/{preventa}/completar-pago  preventas.completar-pago
```

---

## US07 — Buscador por Cercanía

### Historia de usuario
> Como **comprador**, quiero filtrar productos por distancia desde mi ubicación para reducir costos logísticos.

### Criterios de aceptación cumplidos

- El sistema detecta la ubicación del comprador mediante el GPS del navegador (`navigator.geolocation`).
- El usuario selecciona un radio de búsqueda: **10 km, 25 km, 50 km o 100 km**.
- El sistema calcula la distancia real entre la ubicación del comprador y las coordenadas del predio del productor.
- Solo se muestran productos de productores cuya finca está dentro del radio seleccionado.
- Los resultados se ordenan de menor a mayor distancia (más cercano primero).
- Cada tarjeta de producto muestra el badge con la distancia exacta en km.
- Si no hay productos en el radio, se muestra un mensaje con opción de quitar el filtro.
- Sin filtro activo, el marketplace muestra todos los productos normalmente.

### Flujo de uso

1. El comprador ingresa al marketplace.
2. En el panel de filtro hace click en **Detectar mi ubicación** — el navegador solicita permiso de geolocalización.
3. Selecciona un radio del dropdown.
4. Hace click en **Buscar**.
5. Ve los productos filtrados con su distancia en el badge verde de cada tarjeta.
6. Puede hacer click en **Quitar filtro** para volver a la vista completa.

### Implementación técnica

**Controlador:** `app/Http/Controllers/ProductoController.php` — método `marketplace(Request $request)`

Parámetros GET recibidos:
- `lat` — latitud del comprador (float)
- `lng` — longitud del comprador (float)
- `radio` — radio en kilómetros (int)

**Algoritmo — Fórmula Haversine en SQL:**

El cálculo se ejecuta directamente en PostgreSQL para filtrar y ordenar en una sola consulta:

```sql
6371 * acos(
  LEAST(1.0,
    cos(radians(:lat)) * cos(radians(productores.latitud))
    * cos(radians(productores.longitud) - radians(:lng))
    + sin(radians(:lat)) * sin(radians(productores.latitud))
  )
) AS distancia
```

- `6371` = radio de la Tierra en km
- `LEAST(1.0, ...)` previene errores de dominio en `acos()` por imprecisiones de punto flotante
- El JOIN con `productores` conecta cada producto con las coordenadas GPS del predio del productor

**Consulta cuando el filtro está activo:**

```php
$query->join('productores', 'productores.user_id', '=', 'productos.user_id')
      ->whereNotNull('productores.latitud')
      ->whereNotNull('productores.longitud')
      ->selectRaw("productos.*, $haversine as distancia", [$lat, $lng, $lat])
      ->whereRaw("$haversine <= ?", [$lat, $lng, $lat, $radio])
      ->orderBy('distancia');
```

**Geolocalización en el frontend (`marketplace.blade.php`):**

```javascript
navigator.geolocation.getCurrentPosition(
    (pos) => {
        document.getElementById('input-lat').value = pos.coords.latitude;
        document.getElementById('input-lng').value = pos.coords.longitude;
    },
    (err) => { /* manejo de errores por código */ },
    { timeout: 10000, maximumAge: 60000 }
);
```

Las coordenadas se envían como campos ocultos en el formulario GET. El formulario valida que haya ubicación detectada antes de enviar si se seleccionó un radio.

### Dependencia con Sprint 1

US07 depende directamente de que los productores tengan coordenadas guardadas en la tabla `productores` (columnas `latitud` y `longitud`), funcionalidad implementada en Sprint 1 mediante el mapa interactivo de ubicación de predio.

### Variables pasadas a la vista

| Variable | Tipo | Descripción |
|----------|------|-------------|
| `$productos` | LengthAwarePaginator | Productos filtrados (o todos si sin filtro) |
| `$filtroActivo` | bool | Indica si el filtro de cercanía está activo |
| `$radio` | int\|null | Radio seleccionado en km |

---

## Diagrama de relaciones — Sprint 2

```
users (productor)
  │
  ├── productores (latitud, longitud)   ← usado en US07
  │
  └── productos
        │
        ├── producto_imagenes           ← US05
        │
        └── preventas
              │
              └── users (comprador)    ← US06
```

---

## Restricciones de acceso por rol

| Funcionalidad | Admin | Productor verificado | Comprador | Transportista |
|---------------|:-----:|:--------------------:|:---------:|:-------------:|
| Ver marketplace | ✓ | ✓ | ✓ | ✓ |
| Publicar producto | — | ✓ | — | — |
| Ver mis productos | — | ✓ | — | — |
| Realizar preventa | — | — | ✓ | — |
| Ver mis preventas | — | — | ✓ | — |
| Ver ventas futuras | — | ✓ | — | — |
| Filtro por cercanía | ✓ | ✓ | ✓ | ✓ |

---

## Credenciales de prueba

| Rol | Email | Contraseña | Notas |
|-----|-------|------------|-------|
| Administrador | admin@agrovida.com | admin123 | Acceso total |
| Productor | productor@test.com | productor123 | Verificado, con GPS y 2 productos publicados |
