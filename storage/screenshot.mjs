import puppeteer from 'puppeteer-core';

const browser = await puppeteer.launch({
  executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
  headless: true,
  args: ['--no-sandbox', '--disable-gpu', '--hide-scrollbars']
});

const sizes = [
  ['mobile', 390, 844],
  ['mobile-small', 360, 640],
  ['tablet', 768, 1024],
  ['desktop', 1440, 900],
  ['desktop-wide', 1920, 1080]
];

for (const [name, width, height] of sizes) {
  const page = await browser.newPage();
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  await page.goto('http://127.0.0.1:8080/', { waitUntil: 'networkidle0' });
  await page.evaluate(() => document.fonts.ready);
  await new Promise((resolve) => setTimeout(resolve, 300));
  await page.screenshot({ path: `storage/${name}.png` });

  const info = await page.evaluate(() => {
    const rect = (selector) => {
      const node = document.querySelector(selector);
      if (!node) return null;
      const r = node.getBoundingClientRect();
      return { left: Math.round(r.left), right: Math.round(r.right), top: Math.round(r.top), bottom: Math.round(r.bottom), width: Math.round(r.width), height: Math.round(r.height) };
    };
    return {
      viewport: [window.innerWidth, window.innerHeight],
      body: [document.body.scrollWidth, document.body.scrollHeight],
      shell: rect('.app-shell'),
      hero: rect('#heroCard'),
      runners: rect('.runner-grid'),
      dock: rect('.dock'),
      side: rect('.side-panel'),
      stats: rect('.stats-panel')
    };
  });

  console.log(name, JSON.stringify(info));
  await page.close();
}

await browser.close();
