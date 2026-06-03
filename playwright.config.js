import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8000';
const hasUserCreds = !!(process.env.E2E_USER_EMAIL && process.env.E2E_USER_PASSWORD);
const hasAdminCreds = !!(process.env.E2E_ADMIN_EMAIL && process.env.E2E_ADMIN_PASSWORD);

const projects = [
    {
        name: 'guest',
        testMatch: '**/responsive.spec.js',
        use: { ...devices['Desktop Chrome'] },
    },
    {
        name: 'desktop-smoke',
        testMatch: '**/responsive.spec.js',
        grep: /escritorio/,
        use: {
            viewport: { width: 1280, height: 800 },
        },
    },
];

if (hasUserCreds) {
    projects.unshift(
        { name: 'setup-user', testMatch: '**/auth.user.setup.js' },
        {
            name: 'user-chromium',
            testMatch: '**/authenticated.spec.js',
            dependencies: ['setup-user'],
            use: {
                ...devices['Desktop Chrome'],
                storageState: 'tests/e2e/.auth/user.json',
            },
        },
        {
            name: 'user-mobile',
            testMatch: '**/authenticated.spec.js',
            dependencies: ['setup-user'],
            use: {
                ...devices['iPhone 13'],
                storageState: 'tests/e2e/.auth/user.json',
            },
        },
        {
            name: 'user-tablet',
            testMatch: '**/authenticated.spec.js',
            dependencies: ['setup-user'],
            use: {
                viewport: { width: 1024, height: 600 },
                storageState: 'tests/e2e/.auth/user.json',
            },
        }
    );
}

if (hasAdminCreds) {
    projects.unshift(
        { name: 'setup-admin', testMatch: '**/auth.admin.setup.js' },
        {
            name: 'admin',
            testMatch: '**/admin-responsive.spec.js',
            dependencies: ['setup-admin'],
            use: {
                viewport: { width: 1024, height: 600 },
                storageState: 'tests/e2e/.auth/admin.json',
            },
        }
    );
}

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: [['list'], ['html', { open: 'never' }]],
    timeout: 30_000,
    snapshotPathTemplate: '{testDir}/snapshots/{testFilePath}/{arg}{ext}',
    use: {
        baseURL,
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        video: 'off',
    },
    projects,
});
