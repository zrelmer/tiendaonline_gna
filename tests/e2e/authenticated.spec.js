import { test, expect } from '@playwright/test';
import { gotoShop, setViewport } from './helpers/page.js';

test.describe('Dashboard usuario (logueado)', () => {
    test('móvil: sidebar del panel y barra inferior de tienda', async ({ page }) => {
        await setViewport(page, 'mobile');
        await gotoShop(page, '/dashboard');

        await expect(page.locator('body')).toHaveClass(/has-mobile-nav/);
        await expect(page.locator('nav.mobile-menu')).toBeVisible();
        await expect(page.locator('#dashboardMenuToggle')).toBeVisible();
        await expect(page.locator('.user-dashboard-page')).toBeVisible();
    });

    test('tablet: toggle abre sidebar del panel', async ({ page }) => {
        await setViewport(page, 'tabletPortrait');
        await gotoShop(page, '/dashboard');

        const sidebar = page.locator('#dashboardSidebar');
        const toggle = page.locator('#dashboardMenuToggle');

        await expect(toggle).toBeVisible();
        await toggle.click();
        await expect(sidebar).toHaveClass(/show/);
        await expect(page.locator('body')).toHaveClass(/dashboard-sidebar-open/);
    });
});

test.describe('Checkout (logueado)', () => {
    test('móvil: página checkout carga con barra inferior', async ({ page }) => {
        await setViewport(page, 'mobile');
        await gotoShop(page, '/cart/checkout');

        await expect(page).toHaveURL(/\/cart\/checkout/);
        await expect(page.locator('.checkout-page-section')).toBeVisible();
        await expect(page.locator('nav.mobile-menu')).toBeVisible();
    });

    test('tablet: sin scroll horizontal en checkout', async ({ page }) => {
        await setViewport(page, 'tabletLandscape');
        await gotoShop(page, '/cart/checkout');

        const hasOverflow = await page.evaluate(() => {
            const el = document.documentElement;
            return el.scrollWidth > el.clientWidth + 1;
        });
        expect(hasOverflow).toBe(false);
    });
});
