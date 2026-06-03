# Checklist QA responsive — GNA Core Tienda

Recarga forzada (Ctrl+F5) tras cambios en CSS/JS.

## Viewports de prueba

| Viewport | Rango | Uso |
|----------|--------|-----|
| **320×568** | Móvil estrecho | Límites de layout y tipografía |
| **375×667** | Móvil estándar | Flujo principal teléfono |
| **768×1024** | Tablet vertical | iPad / tablet portrait |
| **1024×600** | Tablet horizontal | Ventana ancha baja (DevTools) |
| **960×600** | Tablet (opcional) | Landscape compacto |
| **1280×800** | Escritorio (smoke) | Menú horizontal, sin barra inferior |

En DevTools: modo responsive, zoom **100%**, sin escala del sistema.

## Preparación

- [ ] `php artisan serve` y sitio accesible
- [ ] Probar como **guest** y como **usuario logueado** (cliente; admin en sección admin)
- [ ] Repetir cada bloque crítico en al menos **375×667** y **1024×600**

---

## Móvil — &lt;768px

Probar en **375×667** y **320×568**.

### Navegación (Fase 1)

- [ ] Barra inferior visible en home, tienda, carrito, detalle (no en login/registro/reset)
- [ ] **Inicio**, **Menú** (offcanvas `#primaryMenu`), **Buscar** (abre búsqueda en header), **Deseos**, **Carrito**
- [ ] Menú offcanvas: enlaces correctos, categorías, cerrar con X o backdrop
- [ ] **Sin** bloque «Hola, Mi Cuenta» en header (cuenta vía offcanvas / barra inferior)
- [ ] **Sin** icono perfil suelto duplicado en header
- [ ] **Sin** botón «Menú» con texto en la parte superior del header
- [ ] Sin scroll horizontal en header ni contenido

### Home (Fase 2)

- [ ] Grids en 2 columnas (`col-6`) en productos/categorías
- [ ] Enlaces van a rutas Laravel (no `.html` demo)
- [ ] Banners e imágenes no desbordan el ancho

### Tienda / filtros (Fase 3)

- [ ] Botón filtros abre drawer; overlay y Escape cierran
- [ ] Toolbar sticky usable; paginación táctil
- [ ] Cards de producto legibles en 320px

### Detalle producto (Fase 4)

- [ ] Marca visible en móvil; pestañas en español
- [ ] Barra fija compra aparece al scroll; cantidad sincronizada
- [ ] Barra de compra no queda tapada por la navegación inferior

### Carrito / checkout (Fase 5)

- [ ] Carrito: resumen arriba; filas tipo card
- [ ] Checkout: columnas apiladas; barra fija «Realizar compra»
- [ ] Padding inferior correcto con barra inferior + barra checkout

### Auth (Fase 7)

- [ ] Login, registro, olvidé/reset: formulario ancho completo, botones ≥ 48px
- [ ] **Sin** barra inferior (solo footer auth compacto)
- [ ] Errores de validación visibles (`is-invalid`)

### Dashboard usuario (Fase 6)

- [ ] «Mostrar menú» abre sidebar; overlay/Escape cierran
- [ ] Tablas con layout card (`dashboard-table-mobile`)
- [ ] Modales `modal-fullscreen-sm-down` en direcciones

---

## Tablet — 768px a 1199px

Probar en **768×1024** y **1024×600** (mínimo). Opcional: **960×600**.

Rutas mínimas: `/`, `/shop`, `/cart`, `/login`, `/dashboard` (logueado).

### Header y cuenta

- [ ] **Barra inferior visible** en tienda (home, listado, carrito, detalle, dashboard tienda)
- [ ] **Sin** botón «Menú» (icono + texto) en la esquina superior del header
- [ ] Navegación principal vía pestaña **Menú** de la barra inferior → offcanvas `#primaryMenu`
- [ ] Visible **«Hola, Mi Cuenta»** (bloque con nombre o «Mi Cuenta»)
- [ ] **Sin** icono perfil suelto duplicado (círculo con silueta junto a la lupa)
- [ ] Clic en «Hola, Mi Cuenta» abre/cierra desplegable; enlaces clicables (login, registro, panel, salir)
- [ ] Escape o clic fuera cierra el desplegable de cuenta
- [ ] Búsqueda del header usable (icono lupa o campo según página)

### Navegación inferior (Fase 1 + 10)

- [ ] Cinco ítems: Inicio, Menú, Buscar, Deseos, Carrito
- [ ] **Buscar** en barra inferior abre/activa búsqueda del header
- [ ] Offcanvas: categorías dinámicas, cerrar con X o backdrop
- [ ] Footer con margen inferior: contenido no queda oculto bajo la barra
- [ ] «Tap to top» por encima de la barra inferior, no solapada

### Layout por sección

- [ ] **Home:** grids legibles (2–3 columnas según ancho); sin scroll horizontal
- [ ] **Tienda:** filtros/drawer operativos; cards y paginación legibles
- [ ] **Detalle:** barra fija de compra al scroll; no tapada por barra inferior
- [ ] **Carrito / checkout:** columnas apiladas si aplica; padding inferior con barra inferior (+ barra checkout en checkout)
- [ ] **Dashboard usuario:** sidebar con «Mostrar menú»; tablas en modo card si &lt;992px

### Auth en tablet (sin `has-mobile-nav`)

- [ ] Login, registro, reset: **sin** barra inferior
- [ ] Formulario centrado/ancho completo; botones ≥ 48px
- [ ] **Sin** icono perfil suelto duplicado junto a la lupa (solo «Hola, Mi Cuenta» si aplica)
- [ ] Carrito flotante lateral del tema **no** visible en auth (`auth-guest-page`)

### Regresiones tablet (Fase 9–10)

- [ ] Mini-carrito flotante lateral **oculto** (&lt;1200px con barra inferior)
- [ ] `custom.css` (imports en `assets/css/custom/`) + `shop-mobile.js` cargan correctamente
- [ ] Sin enlaces rotos a `*.html` en flujos principales

---

## Escritorio — ≥1200px

Smoke rápido en **1280×800** (o ventana maximizada).

- [ ] **Sin** barra inferior fija
- [ ] Menú horizontal del tema visible y usable
- [ ] «Hola, Mi Cuenta» con desplegable por **clic** (no solo hover)
- [ ] Mini-carrito / iconos header según diseño escritorio del tema
- [ ] Sin scroll horizontal involuntario en home y tienda

---

## Panel admin (Fase 8)

Requiere sesión admin. Probar en **375×667** y **1024×600**.

- [ ] Menú hamburguesa abre sidebar Rica; overlay y Escape cierran
- [ ] Listados (productos, pedidos, inventario…): filas en tarjetas con etiquetas en móvil/tablet
- [ ] Formularios: campos apilados en estrecho; botones táctiles
- [ ] Dashboard admin: KPIs en una columna en &lt;992px; tablas widget legibles

---

## Regresiones globales (todas las resoluciones)

- [ ] `custom.css` + módulos `assets/css/custom/*.css` + `shop-mobile.js` cargan en tienda
- [ ] `custom-admin.css` + `admin-mobile.js` cargan en admin
- [ ] Controles principales ≥ 44×44px en rutas táctiles
- [ ] Textos críticos no cortados sin poder leer (word-break donde aplica)
- [ ] 0 scroll horizontal involuntario en rutas del checklist

## Automatizado (Playwright)

Con `php artisan serve` en marcha:

```bash
npm install
npx playwright install chromium
npm run test:e2e
```

Detalle: [playwright-responsive.md](playwright-responsive.md). Cubre barra inferior, auth, scroll horizontal y menú cuenta en viewports 375, 768, 1024×600 y 1280.

## Criterio de cierre

- [ ] Checklist **móvil** completo en 375×667 y 320×568
- [ ] Checklist **tablet** completo en 768×1024 y 1024×600
- [ ] Smoke **escritorio** en ≥1280px sin regresiones de header/cuenta
- [ ] `npm run test:e2e` en verde (smoke responsive)

## Rutas sugeridas

| Ruta | Nombre | Móvil | Tablet | Escritorio |
|------|--------|:-----:|:------:|:----------:|
| `/` | Home | ✓ | ✓ | ✓ |
| `/shop` o listado tienda | Tienda | ✓ | ✓ | ✓ |
| `/producto/{id}/{slug}` | Detalle | ✓ | ✓ | opcional |
| `/cart` | Carrito | ✓ | ✓ | opcional |
| `/checkout` | Checkout (logueado) | ✓ | ✓ | opcional |
| `/login`, `/register` | Auth | ✓ | ✓ | opcional |
| `/dashboard` | Panel usuario | ✓ | ✓ | opcional |
| `/admin` | Panel admin | ✓ | ✓ | opcional |

## Referencia de breakpoints (código)

| Ancho | Comportamiento |
|-------|----------------|
| &lt;768px | Barra inferior; header cuenta oculta; iconos header móvil ocultos donde aplica |
| 768–1199px | Barra inferior; «Hola, Mi Cuenta»; sin icono perfil duplicado; sin botón Menú en header |
| ≥1200px | Sin barra inferior; menú horizontal; cuenta por clic |
