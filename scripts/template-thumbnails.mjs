/**
 * Screenshots template previews (desktop 1440×900 and mobile 390×780).
 * Used by: php artisan templates:thumbnails
 *
 *   node scripts/template-thumbnails.mjs --base=http://localhost:8000 --out=storage/app/public/template-thumbnails slug-a slug-b
 *
 * Requires Playwright (npm i -D playwright && npx playwright install chromium).
 */
import { mkdirSync } from 'node:fs';
import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const args = Object.fromEntries(process.argv.slice(2).filter((a) => a.startsWith('--')).map((a) => a.slice(2).split('=')));
const slugs = process.argv.slice(2).filter((a) => !a.startsWith('--'));
const base = (args.base || 'http://localhost:8000').replace(/\/$/, '');
const out = args.out || 'storage/app/public/template-thumbnails';

let playwright;
try {
    playwright = require('playwright');
} catch {
    console.error('Playwright is not installed. Run: npm i -D playwright && npx playwright install chromium');
    process.exit(1);
}

mkdirSync(out, { recursive: true });
const browser = await playwright.chromium.launch();

for (const slug of slugs) {
    for (const [suffix, width, height] of [['', 1440, 900], ['-mobile', 390, 780]]) {
        const page = await browser.newPage({ viewport: { width, height }, deviceScaleFactor: 1 });
        await page.goto(`${base}/templates/${slug}/render`, { waitUntil: 'networkidle', timeout: 60000 });
        await page.evaluate(() => document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible')));
        await page.waitForTimeout(900);
        await page.screenshot({ path: `${out}/${slug}${suffix}.jpg`, type: 'jpeg', quality: 78 });
        await page.close();
        console.log(`✓ ${slug}${suffix}`);
    }
}

await browser.close();
