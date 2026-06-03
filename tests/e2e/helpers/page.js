export { VIEWPORTS, setViewport } from './viewports.js';

/** Navega y cierra la barra de notificación del header si está visible. */
export async function gotoShop(page, path) {
    await page.goto(path);
    const closeNotification = page.locator('.close-notification');
    if (await closeNotification.isVisible()) {
        await closeNotification.click();
    }
}
