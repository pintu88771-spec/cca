import puppeteer from 'puppeteer-core';

const browser = await puppeteer.launch({
  executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
  headless: true,
  args: ['--no-sandbox', '--disable-gpu']
});

const page = await browser.newPage();
await page.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1 });
await page.goto('http://127.0.0.1:8080/', { waitUntil: 'networkidle0' });

const result = await page.evaluate(() => {
  const vh = window.innerHeight;
  const vw = window.innerWidth;
  const offenders = [];
  document.querySelectorAll('body *').forEach((el) => {
    const r = el.getBoundingClientRect();
    if (r.bottom > vh + 1 || r.right > vw + 1) {
      offenders.push({
        tag: el.tagName.toLowerCase(),
        cls: (el.className && typeof el.className === 'string' ? el.className : '').slice(0, 70),
        pos: getComputedStyle(el).position,
        top: Math.round(r.top),
        bottom: Math.round(r.bottom),
        right: Math.round(r.right),
        height: Math.round(r.height)
      });
    }
  });
  return { vh, vw, scrollHeight: document.body.scrollHeight, offenders: offenders.slice(0, 20) };
});

console.log(JSON.stringify(result, null, 2));
await browser.close();
