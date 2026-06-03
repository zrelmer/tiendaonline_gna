import { test, expect } from '@playwright/test';
import { gotoShop } from './helpers/page.js';

test.describe('Capturas de regresión visual (smoke)', () => {
    test('home móvil', async ({ page }) => {
        await gotoShop(page, '/');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveScreenshot('home-mobile.png', {
            maxDiffPixelRatio: 0.02,
            fullPage: false,
        });
    });

    test('login tablet 1024×600', async ({ page }) => {
        await page.setViewportSize({ width: 1024, height: 600 });
        await gotoShop(page, '/login');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveScreenshot('login-tablet.png', {
            maxDiffPixelRatio: 0.02,
            fullPage: false,
        });
    });
});
