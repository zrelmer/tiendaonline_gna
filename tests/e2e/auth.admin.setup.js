import { test as setup, expect } from '@playwright/test';

const authFile = 'tests/e2e/.auth/admin.json';

setup('login administrador', async ({ page }) => {
    const email = process.env.E2E_ADMIN_EMAIL;
    const password = process.env.E2E_ADMIN_PASSWORD;

    if (!email || !password) {
        setup.skip(true, 'Define E2E_ADMIN_EMAIL y E2E_ADMIN_PASSWORD en .env o entorno');
    }

    await page.goto('/login');
    await page.locator('input[name="email"]').fill(email);
    await page.locator('input[name="password"]').fill(password);
    await page.locator('button[type="submit"]').click();
    await page.waitForURL(/\/(admin\/dashboard|dashboard)(\?.*)?$/, { timeout: 15_000 });

    await page.context().storageState({ path: authFile });
});
