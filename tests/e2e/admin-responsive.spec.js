import { test, expect } from '@playwright/test';
import { gotoShop, setViewport } from './helpers/page.js';

test.describe('Panel admin responsive', () => {
    test('móvil: dashboard admin accesible', async ({ page }) => {
        await setViewport(page, 'mobile');
        await gotoShop(page, '/admin/dashboard');

        await expect(page).toHaveURL(/\/admin\/dashboard/);
        await expect(page.locator('body')).toBeVisible();
    });

    test('tablet: listado productos sin scroll horizontal', async ({ page }) => {
        await setViewport(page, 'tabletLandscape');
        await gotoShop(page, '/admin/productos');

        await expect(page).toHaveURL(/\/admin\/productos/);
        const hasOverflow = await page.evaluate(() => {
            const el = document.documentElement;
            return el.scrollWidth > el.clientWidth + 1;
        });
        expect(hasOverflow).toBe(false);
    });
});
