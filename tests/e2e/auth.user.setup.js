import { test as setup, expect } from '@playwright/test';

const authFile = 'tests/e2e/.auth/user.json';

setup('login cliente', async ({ page }) => {
    const email = process.env.E2E_USER_EMAIL;
    const password = process.env.E2E_USER_PASSWORD;

    if (!email || !password) {
        setup.skip(true, 'Define E2E_USER_EMAIL y E2E_USER_PASSWORD en .env o entorno');
    }

    await page.goto('/login');
    await page.locator('input[name="email"]').fill(email);
    await page.locator('input[name="password"]').fill(password);
    await page.locator('button[type="submit"]').click();
    await page.waitForURL(/\/(dashboard|home)(\?.*)?$/, { timeout: 15_000 });
    await expect(page.locator('body')).not.toHaveClass(/auth-guest-page/);

    await page.context().storageState({ path: authFile });
});
