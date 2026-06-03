/**
 * Une módulos custom/ en un solo CSS (menos peticiones HTTP en producción).
 * Orden = custom.css @import
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..', 'public', 'assets', 'css');
const modules = [
    'custom/tokens.css',
    'custom/auth-guest.css',
    'custom/nav.css',
    'custom/home.css',
    'custom/shop.css',
    'custom/product.css',
    'custom/cart-checkout.css',
    'custom/dashboard-layout.css',
    'custom/auth-pages.css',
    'custom/misc.css',
    'custom/dashboard-tabs.css',
    'custom/desktop.css',
];

const banner = `/* Generado por npm run build:css — no editar a mano */\n`;
const body = modules
    .map((rel) => readFileSync(join(root, rel), 'utf8'))
    .join('\n');

writeFileSync(join(root, 'custom.bundle.css'), banner + body, 'utf8');
console.log('OK public/assets/css/custom.bundle.css');
