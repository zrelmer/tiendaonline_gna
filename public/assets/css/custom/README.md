# Módulos CSS tienda (`custom/`)

Cargados en orden vía `assets/css/custom.css` (`@import`).

| Archivo | Contenido |
|---------|-----------|
| `tokens.css` | Variables `--gna-mobile-*` (Fase 9) |
| `auth-guest.css` | Login/registro: sin carrito flotante |
| `nav.css` | Barra inferior, header, cuenta, offcanvas (Fases 1, 10) |
| `home.css` | Home móvil (Fase 2) |
| `shop.css` | Listado tienda / filtros (Fase 3) |
| `product.css` | Detalle producto (Fase 4) |
| `cart-checkout.css` | Carrito y checkout (Fase 5) |
| `dashboard-layout.css` | Panel usuario — layout móvil (Fase 6) |
| `auth-pages.css` | Formularios auth (Fase 7) |
| `misc.css` | Ajustes globales puntuales |
| `dashboard-tabs.css` | Panel usuario — pestañas |
| `desktop.css` | Escritorio ≥1200px (tap-top, cuenta) |

Al editar responsive de la tienda, abre el módulo de la fase correspondiente en lugar del monolito anterior.

## Especificidad (sin `!important`)

Los overrides cargan **después** de `style.css`. Prioriza selectores largos (`header.header-2`, `body.has-mobile-nav`, `.mobile-menu-offcanvas.categories-canvas`) en lugar de `!important`. Si el tema gana, amplía el selector antes de volver a `!important`.
