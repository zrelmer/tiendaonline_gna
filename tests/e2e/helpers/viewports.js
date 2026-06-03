/** Viewports alineados con docs/mobile-qa-checklist.md */
export const VIEWPORTS = {
    mobile: { width: 375, height: 667, label: '375×667' },
    mobileNarrow: { width: 320, height: 568, label: '320×568' },
    tabletPortrait: { width: 768, height: 1024, label: '768×1024' },
    tabletLandscape: { width: 1024, height: 600, label: '1024×600' },
    desktop: { width: 1280, height: 800, label: '1280×800' },
};

export async function setViewport(page, key) {
    const vp = VIEWPORTS[key];
    await page.setViewportSize({ width: vp.width, height: vp.height });
    return vp;
}
