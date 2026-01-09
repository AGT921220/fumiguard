## Monorepo SaaS (Laravel + React)

Estructura:

- `backend/`: Laravel 11 (PHP 8.3) + Sanctum + API base
- `frontend/`: React + TypeScript (Vite) + Tailwind + React Query
- `docker-compose.yml`: nginx + php-fpm + mysql

### Requisitos

- **Docker** y **Docker Compose v2**
- **Node.js** (recomendado 20+) para el frontend local

### Levantar backend con Docker

1) Crear `.env` para Docker Compose:

```bash
cp .env.example .env
```

2) Levantar servicios:

```bash
docker compose up -d --build
```

3) Probar healthcheck:

```bash
curl http://localhost:8080/api/v1/health
```

Notas:
- El contenedor `php-fpm` crea `backend/.env` si no existe, instala dependencias con Composer y genera `APP_KEY` (sin correr migraciones).

### Levantar frontend (local)

```bash
cd frontend
npm install
npm run dev
```

Por defecto Vite levanta en `http://localhost:5173`.

### Backend: API y Sanctum

- **Endpoint**: `GET /api/v1/health`
- **Sanctum**: instalado y publicado (`config/sanctum.php` + migración). Middleware `EnsureFrontendRequestsAreStateful` aplicado al grupo `api`.
- **Arquitectura hexagonal (preparada)**:
  - `backend/app/Domain`
  - `backend/app/Application`
  - `backend/app/Infrastructure`

### OpenAPI / Swagger

El backend genera documentación OpenAPI con **Scribe**:

- **UI**: `http://localhost/docs`
- **OpenAPI YAML**: `backend/storage/app/private/scribe/openapi.yaml`
- **Regenerar**:

```bash
cd backend
php artisan scribe:generate
```

### PDFs (ServiceReport)

Al finalizar un ServiceReport se generan:

- **Reporte de servicio (PDF)**: `GET /api/v1/reports/{id}/pdf`
- **Certificado de fumigación (PDF, folio por tenant)**: `GET /api/v1/reports/{id}/certificate`

Notas:
- Los PDFs se almacenan en `backend/storage/app/private/reports/<tenant>/<report>/...`
- La generación usa **Dompdf**.

### Datos demo (seed)

Al correr `php artisan migrate --seed` se crea:

- 1 tenant: `fumiguard-demo`
- Usuarios:
  - `admin@demo.test` / `password` (TENANT_ADMIN)
  - `dispatcher@demo.test` / `password` (DISPATCHER)

