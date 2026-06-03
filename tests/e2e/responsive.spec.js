import { test, expect } from '@playwright/test';
import { gotoShop, setViewport } from './helpers/page.js';

test.describe('Home — barra inferior y header', () => {
    test('móvil: barra inferior visible, cuenta header oculta', async ({ page }) => {
        await setViewport(page, 'mobile');
        await gotoShop(page, '/');
        await expect(page.locator('body')).toHaveClass(/has-mobile-nav/);
        await expect(page.locator('nav.mobile-menu')).toBeVisible();
        await expect(page.locator('.header-user-dropdown')).toBeHidden();
        await expect(page.locator('.button-item')).toBeHidden();
    });

    test('tablet vertical: barra inferior + Hola Mi Cuenta, sin icono duplicado', async ({ page }) => {
        await setViewport(page, 'tabletPortrait');
        await gotoShop(page, '/');
        await expect(page.locator('nav.mobile-menu')).toBeVisible();
        await expect(page.locator('.header-user-dropdown')).toBeVisible();
        await expect(page.locator('li.header-phone-hide')).toBeHidden();
        await expect(page.locator('.header-menu-toggle')).toHaveCount(0);
    });

    test('tablet horizontal 1024×600: misma navegación tablet', async ({ page }) => {
        await setViewport(page, 'tabletLandscape');
        await gotoShop(page, '/');
        await expect(page.locator('nav.mobile-menu')).toBeVisible();
        await expect(page.locator('.header-user-dropdown')).toBeVisible();
        await expect(page.locator('li.header-phone-hide')).toBeHidden();
    });

    test('escritorio: sin barra inferior', async ({ page }) => {
        await setViewport(page, 'desktop');
        await gotoShop(page, '/');
        await expect(page.locator('nav.mobile-menu')).toBeHidden();
        await expect(page.locator('.header-user-dropdown')).toBeVisible();
    });
});

test.describe('Login — auth guest', () => {
    test('1024×600: sin barra inferior ni carrito flotante', async ({ page }) => {
        await setViewport(page, 'tabletLandscape');
        await gotoShop(page, '/login');
        await expect(page.locator('body')).toHaveClass(/auth-guest-page/);
        await expect(page.locator('body')).not.toHaveClass(/has-mobile-nav/);
        await expect(page.locator('nav.mobile-menu')).toHaveCount(0);
        await expect(page.locator('.button-item')).toHaveCount(0);
        await expect(page.locator('li.header-phone-hide')).toBeHidden();
    });

    test('móvil: formulario auth sin barra inferior', async ({ page }) => {
        await setViewport(page, 'mobile');
        await gotoShop(page, '/login');
        await expect(page.locator('nav.mobile-menu')).toHaveCount(0);
        await expect(page.locator('.auth-page-section')).toBeVisible();
    });
});

test.describe('Tienda y carrito — smoke layout', () => {
    test('tablet: barra inferior en /shop y /cart', async ({ page }) => {
        await setViewport(page, 'tabletLandscape');
        for (const path of ['/shop', '/cart']) {
            await gotoShop(page, path);
            await expect(page.locator('nav.mobile-menu')).toBeVisible();
            await expect(page.locator('.button-item')).toBeHidden();
        }
    });

    test('sin scroll horizontal en home y shop (móvil y tablet)', async ({ page }) => {
        for (const vp of ['mobile', 'tabletLandscape']) {
            await setViewport(page, vp);
            for (const path of ['/', '/shop']) {
                await gotoShop(page, path);
                const hasOverflow = await page.evaluate(() => {
                    const el = document.documentElement;
                    return el.scrollWidth > el.clientWidth + 1;
                });
                expect(hasOverflow, `scroll horizontal en ${path} @ ${vp}`).toBe(false);
            }
        }
    });
});

test.describe('Menú cuenta — clic tablet', () => {
    test('768px: desplegable abre y cierra', async ({ page }) => {
        await setViewport(page, 'tabletPortrait');
        await gotoShop(page, '/');
        const trigger = page.locator('.header-account-trigger');
        const menu = page.locator('#headerAccountMenu');
        await expect(trigger).toBeVisible();
        await expect(menu).toBeHidden();
        await trigger.click();
        await expect(page.locator('.header-user-dropdown')).toHaveClass(/is-open/);
        await expect(menu).toBeVisible();
        await page.keyboard.press('Escape');
        await expect(page.locator('.header-user-dropdown')).not.toHaveClass(/is-open/);
    });
});

test.describe('Assets responsive', () => {
    test('custom.css y shop-mobile.js cargan en home', async ({ page }) => {
        await setViewport(page, 'mobile');
        const cssWait = page.waitForResponse(
            (res) =>
                (res.url().includes('custom.css') || res.url().includes('custom.bundle.css')) &&
                res.status() === 200
        );
        const jsWait = page.waitForResponse(
            (res) => res.url().includes('shop-mobile.js') && res.status() === 200
        );
        await gotoShop(page, '/');
        await expect(page.locator('link[href*="custom.css"], link[href*="custom.bundle.css"]')).toHaveCount(1);
        await cssWait;
        await jsWait;
    });
});
