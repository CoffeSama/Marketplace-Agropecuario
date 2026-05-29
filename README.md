# Marketplace Agropecuario

Plataforma web para la compra y venta de productos agrícolas en Bolivia.  
Conecta productores, compradores y transportistas con registro por rol, verificación documental, geolocalización de predios y sistema de preventa de cosechas.

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.x / Laravel 12 |
| Frontend | AdminLTE 3 / Bootstrap 4 / Vite |
| Base de datos | PostgreSQL 16 |
| Mapas | Leaflet.js + OpenStreetMap + Nominatim |
| Contenedores | Docker / Docker Compose |

---

## Funcionalidades implementadas

### Sprint 1 — Registro, verificación y geolocalización
- Registro con roles: **Productor**, **Comprador**, **Transportista**
- Validación de documentos por el administrador
- Panel de administración con dashboard de solicitudes
- Mapa GPS interactivo para que el productor marque la ubicación de su predio

### Sprint 2 — Marketplace y preventa
- **US05** — Publicación de cosecha: productores verificados publican productos con imágenes, categoría, precio y fecha de disponibilidad
- **US06** — Preventa de cosecha: compradores reservan productos futuros pagando un anticipo del 40%
- **US07** — Buscador por cercanía: filtro por radio (10/25/50/100 km) usando la fórmula Haversine sobre coordenadas GPS del productor, ordenado por distancia

---

## Instalación local

### Requisitos
- PHP 8.2+
- Composer
- Node.js y npm
- PostgreSQL 16
- Git

### Pasos

```bash
git clone https://github.com/CoffeSama/Marketplace-Agropecuario.git
cd Marketplace-Agropecuario

cp .env.example .env
# Configura DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD en .env

composer install --ignore-platform-reqs
npm install && npm run build

php artisan key:generate
php artisan migrate --seed
php artisan storage:link

php -d upload_max_filesize=10M -d post_max_size=20M artisan serve --port=8001
```

Acceso: `http://localhost:8001`

---

## Instalación con Docker

```bash
git clone https://github.com/CoffeSama/Marketplace-Agropecuario.git
cd Marketplace-Agropecuario

# Levantar solo la base de datos
docker compose up -d db

cp .env.example .env
# Configurar: DB_HOST=127.0.0.1, DB_PORT=5432

composer install --ignore-platform-reqs
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --port=8001
```

---

## Credenciales de prueba

| Rol | Email | Contraseña |
|-----|-------|------------|
| Administrador | admin@agrovida.com | admin123 |
| Productor (verificado, con GPS y productos) | productor@test.com | productor123 |

---

## Estructura de base de datos

| Tabla | Descripción |
|-------|-------------|
| `users` | Datos comunes de todos los usuarios |
| `roles` | Roles del sistema (admin, productor, comprador, transportista) |
| `productores` | Datos específicos del productor, incluye `latitud` y `longitud` |
| `compradores` | Datos específicos del comprador |
| `transportistas` | Datos específicos del transportista |
| `solicitudes_vendedor` | Solicitudes de verificación documental |
| `productos` | Publicaciones de cosecha |
| `producto_imagenes` | Imágenes asociadas a cada producto |
| `preventas` | Reservas de compra con anticipo del 40% |

---

## Notas

- El archivo `.env.example` no contiene credenciales reales.
- Para producción configurar `APP_ENV=production` y `APP_DEBUG=false`.
- Las imágenes se almacenan en `storage/app/public` — ejecutar `php artisan storage:link` para el enlace público.
