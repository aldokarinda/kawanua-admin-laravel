const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    // Ensure screenshots directory exists
    if (!fs.existsSync('./public/screenshots')) {
        fs.mkdirSync('./public/screenshots', { recursive: true });
    }

    const browser = await puppeteer.launch({ headless: 'new' });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    try {
        console.log('Navigating to login page...');
        await page.goto('http://localhost:8080/login', { waitUntil: 'networkidle0' });

        // Fill in login credentials (superadmin@example.com / password)
        await page.type('#email', 'superadmin@example.com');
        await page.type('#password', 'password');
        
        const delay = ms => new Promise(res => setTimeout(res, ms));

        console.log('Logging in...');
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.click('button[type="submit"]'),
        ]);

        await delay(2000);
        console.log('Taking screenshot: Dashboard...');
        await page.screenshot({ path: './public/screenshots/dashboard.png' });

        console.log('Taking screenshot: User Management...');
        await page.goto('http://localhost:8080/admin/users', { waitUntil: 'networkidle0' });
        await delay(2000);
        await page.screenshot({ path: './public/screenshots/users.png' });

        console.log('Taking screenshot: Role Management...');
        await page.goto('http://localhost:8080/admin/roles', { waitUntil: 'networkidle0' });
        await delay(2000);
        await page.screenshot({ path: './public/screenshots/roles.png' });

        console.log('Taking screenshot: Security Dashboard...');
        await page.goto('http://localhost:8080/admin/security', { waitUntil: 'networkidle0' });
        await delay(2000);
        await page.screenshot({ path: './public/screenshots/security.png' });

        console.log('Taking screenshot: Master Data - Categories...');
        await page.goto('http://localhost:8080/admin/categories', { waitUntil: 'networkidle0' });
        await delay(2000);
        await page.screenshot({ path: './public/screenshots/categories.png' });

        console.log('Screenshots captured successfully.');
    } catch (err) {
        console.error('Error during screenshots:', err);
    } finally {
        await browser.close();
    }
})();
