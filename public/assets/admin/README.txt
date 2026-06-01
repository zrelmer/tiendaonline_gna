Assets de la plantilla Fastkart Admin
=====================================

El HTML de demo del panel admin usa archivos que NO vienen con la tienda (frontend).

Para usar el diseño completo de Fastkart Admin:

1. En el ZIP de la plantilla, localiza la carpeta "assets" del BACKEND / ADMIN
   (no la de la tienda pública).

2. Copia todo su contenido aquí, de modo que existan rutas como:
   - public/assets/admin/css/style.css
   - public/assets/admin/js/sidebar-menu.js
   - public/assets/admin/images/logo/...

3. En resources/views/partials/admin/head.blade.php y scripts.blade.php
   descomenta las líneas marcadas para cargar esos archivos.

Mientras no copies esos archivos, el panel usa el layout en custom.css
(resources/views/layouts/appadmin.blade.php) con Bootstrap y Feather del proyecto.
