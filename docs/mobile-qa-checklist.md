# Checklist QA móvil — GNA Core Tienda

Probar en **375×667** (iPhone estándar) y **320×568** (pantalla estrecha). Recarga forzada (Ctrl+F5) tras cambios en CSS/JS.

## Preparación

- [ ] `php artisan serve` y sitio accesible
- [ ] DevTools → modo responsive, sin zoom del navegador
- [ ] Usuario guest y usuario logueado (cliente + admin si aplica)

## Tienda pública

### Navegación (Fase 1)

- [ ] Barra inferior visible en home, tienda, carrito, detalle (no en login/registro/reset)
- [ ] **Inicio**, **Menú** (offcanvas `#primaryMenu`), **Buscar** (abre búsqueda en header), **Deseos**, **Carrito**
- [ ] Menú offcanvas: enlaces correctos, categorías, cerrar con X o backdrop
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
- [ ] Barra no queda tapada por la navegación inferior

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

## Panel admin (Fase 8)

Requiere sesión admin.

- [ ] Menú hamburguesa abre sidebar Rica; overlay y Escape cierran
- [ ] Listados (productos, pedidos, inventario…): filas en tarjetas con etiquetas
- [ ] Formularios: campos apilados, botones táctiles
- [ ] Dashboard admin: KPIs en una columna; tablas widget legibles

## Regresiones globales (Fase 9–10)

- [ ] `custom.css` + `shop-mobile.js` cargan en tienda
- [ ] `custom-admin.css` + `admin-mobile.js` cargan en admin
- [ ] Mini-carrito flotante lateral **oculto** en móvil (barra inferior cubre carrito)
- [ ] «Tap to top» no solapa la barra inferior
- [ ] Sin enlaces rotos a `*.html` en flujos principales

## Criterio de cierre

- [ ] 0 scroll horizontal involuntario en rutas anteriores
- [ ] Controles principales ≥ 44×44px
- [ ] Textos críticos no cortados sin poder leer (word-break donde aplica)

## Rutas sugeridas

| Ruta | Nombre |
|------|--------|
| `/` | Home |
| `/shop` o listado tienda | Tienda |
| `/producto/{id}/{slug}` | Detalle |
| `/cart` | Carrito |
| `/checkout` | Checkout (logueado) |
| `/login`, `/register` | Auth |
| `/dashboard` | Panel usuario |
| `/admin` | Panel admin |
