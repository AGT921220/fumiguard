## Frontend (React + TS + Vite)

### Requisitos

- Node.js (recomendado 20+)
- Backend levantado (Docker) en `http://localhost:8080`

### Variables de entorno

Crea `frontend/.env` (opcional):

```bash
VITE_API_BASE_URL=http://localhost:8080
```

### Levantar en desarrollo

```bash
cd frontend
npm install
npm run dev
```

### Login demo

- `admin@demo.test` / `password`

### Notas

- UI **mobile-first**.
- Manejo de errores con **toasts**.
- Si el backend responde `403` por suscripción inactiva, la UI entra en **solo lectura**.

