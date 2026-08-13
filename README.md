# Hack Club Projects

Laravel 12 + Inertia.js + React application with Hack Club OAuth login.

## Stack

- PHP 8.2 (FPM)
- Laravel 12
- Inertia.js + React
- MySQL 8
- Redis 7
- Nginx
- Vite
- Docker Compose

## Prerequisites

- Docker
- Docker Compose plugin (`docker compose`)

## Ports

- App: `http://localhost:8085`
- MySQL: `localhost:8086`
- Redis: `localhost:8087`

## Quick Start (Docker)

1. Start containers:

```bash
docker compose up -d --build
```

2. Create environment file:

```bash
cp .env.example .env
```

3. Update `.env` for MySQL (required for this compose setup):

```env
APP_NAME="Hack Club"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8085

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=hack_db
DB_USERNAME=hack_user
DB_PASSWORD=hack_password

REDIS_HOST=redis
REDIS_PORT=6379

# Optional Vite HMR overrides
VITE_HMR_HOST=localhost
VITE_HMR_PORT=5173
VITE_HMR_PROTOCOL=ws
```

4. Add Hack Club OAuth values in `.env`:

```env
HACKCLUB_CLIENT_ID=your_client_id
HACKCLUB_CLIENT_SECRET=your_client_secret
HACKCLUB_REDIRECT_URI=http://localhost:8085/auth/hackclub/callback
HACKCLUB_BASE_URL=https://auth.hackclub.com
```

5. Install PHP and Node dependencies in the `web` container:

```bash
docker compose exec web composer install
docker compose exec web npm install
```

6. Generate app key and run migrations:

```bash
docker compose exec web php artisan key:generate
docker compose exec web php artisan migrate
```

7. Build frontend assets:

```bash
docker compose exec web npm run build
```

8. Open the app:

- `http://localhost:8085`

## Development Workflow

Run Vite dev server inside the container:

```bash
docker compose exec web npm run dev
```

### Vite HMR (No Hardcoded Docker IP)

The Vite config reads HMR settings from environment variables.

- `VITE_HMR_HOST`: Preferred host for HMR websocket.
- `VITE_HMR_PORT`: HMR port (default `5173`).
- `VITE_HMR_PROTOCOL`: `ws` or `wss` (default `ws`).

If `VITE_HMR_HOST` is not set, Vite falls back to hostname derived from `APP_URL`.

Examples:

```env
# Local Docker setup
APP_URL=http://localhost:8085
VITE_HMR_HOST=localhost
VITE_HMR_PORT=5173
VITE_HMR_PROTOCOL=ws
```

```env
# Remote/staging with HTTPS tunnel
APP_URL=https://my-app.example.com
VITE_HMR_HOST=my-app.example.com
VITE_HMR_PORT=443
VITE_HMR_PROTOCOL=wss
```

Useful Laravel commands:

```bash
docker compose exec web php artisan optimize:clear
docker compose exec web php artisan migrate
docker compose exec web php artisan test
docker compose exec web php artisan route:list
```

## Stop / Restart

Stop containers:

```bash
docker compose down
```

Stop and remove volumes (resets database data):

```bash
docker compose down -v
```

Restart with fresh build:

```bash
docker compose up -d --build
```

## Troubleshooting

### 1) Permission errors (for example Vite cannot write in `node_modules`)

If files were created by root inside the container, fix ownership from host:

```bash
sudo chown -R $USER:$USER node_modules storage bootstrap/cache
```

Then retry:

```bash
docker compose exec web npm run build
```

### 2) `php` command not found on host

Run Laravel commands inside container instead:

```bash
docker compose exec web php artisan test
```

### 3) Login/OAuth callback errors

Check:

- `HACKCLUB_REDIRECT_URI` exactly matches your OAuth app settings.
- App URL is `http://localhost:8085`.
- Callback route is `/auth/hackclub/callback`.

### 4) Database connection issues

Verify `.env` values match compose service names (`mysql`, `redis`) and restart:

```bash
docker compose restart web
```

### 5) HMR not reconnecting in browser

Check:

- `APP_URL` matches how you open the app in the browser.
- `VITE_HMR_HOST` is reachable from your browser.
- `VITE_HMR_PROTOCOL` is `ws` for local HTTP and `wss` for HTTPS.

## Project Structure (high-level)

- Backend routes: `routes/web.php`
- Auth controller: `app/Http/Controllers/Auth/HackClubController.php`
- Inertia middleware: `app/Http/Middleware/HandleInertiaRequests.php`
- React pages: `resources/js/Pages`
- React layout: `resources/js/Layouts/AuthenticatedLayout.jsx`
- Nginx config: `nginx/default.conf`
- Docker compose: `compose.yml`

## Notes

- The app is configured for Docker-first development.
- Authentication UI and app pages are Inertia React pages.
