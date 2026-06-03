# Pruebas E2E responsive (Playwright)

Smoke de layout + rutas con sesión + admin (opcional) + regresión visual (opcional).

## Requisitos

- Node.js 18+
- `php artisan serve` (o `PLAYWRIGHT_BASE_URL`)

```bash
npm install
npx playwright install chromium
```

## Comandos

| Comando | Qué ejecuta |
|---------|-------------|
| `npm run test:e2e` | Guest + smoke escritorio (sin credenciales) |
| Con `E2E_USER_*` en `.env` | + dashboard y checkout logueados (3 viewports) |
| Con `E2E_ADMIN_*` en `.env` | + panel admin |
| `npm run test:e2e:visual` | Crea/actualiza capturas baseline |
| `npm run test:e2e:visual:check` | Compara capturas (regresión visual ligera) |
| `npm run build:css` | Genera `custom.bundle.css` (1 petición CSS) |

Cargar variables en PowerShell:

```powershell
$env:E2E_USER_EMAIL="tu@correo.com"
$env:E2E_USER_PASSWORD="tu-clave"
npm run test:e2e
```

## Proyectos Playwright

- **guest** — `responsive.spec.js` (home, login, shop, cuenta)
- **desktop-smoke** — test «escritorio» a 1280px
- **setup-user** + **user-*** — `authenticated.spec.js` (dashboard, checkout)
- **setup-admin** + **admin** — `admin-responsive.spec.js`
- **visual** — solo vía `test:e2e:visual*`

## Archivos

- `playwright.config.js`
- `tests/e2e/responsive.spec.js`
- `tests/e2e/authenticated.spec.js`
- `tests/e2e/admin-responsive.spec.js`
- `tests/e2e/visual.spec.js`
- `tests/e2e/auth.*.setup.js`
- `tests/e2e/helpers/`

## CSS bundle (producción)

Tras editar módulos en `assets/css/custom/`:

```bash
npm run build:css
```

`head.blade.php` sirve `custom.bundle.css` si existe; si no, `custom.css` con `@import` (desarrollo).
