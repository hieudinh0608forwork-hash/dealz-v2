const { test, expect } = require('@playwright/test');
const fs = require('node:fs');
const AxeBuilder = require('@axe-core/playwright').default;

const BASE_URL = 'http://127.0.0.1:8080';
const USERNAME = 'dealz-ci-admin';
const PASSWORD = 'ci-only-password-not-for-reuse';

async function signIn(page) {
  await page.goto(BASE_URL + '/wp-login.php');
  await page.locator('#user_login').fill(USERNAME);
  await page.locator('#user_pass').fill(PASSWORD);
  await page.locator('#wp-submit').click();
  await expect(page).toHaveURL(/wp-admin/);
  await page.goto(BASE_URL + '/');
}

function captureBrowserErrors(page) {
  const consoleErrors = [];
  const pageErrors = [];
  page.on('console', message => {
    if (message.type() === 'error') consoleErrors.push(message.text());
  });
  page.on('pageerror', error => pageErrors.push(error.message));
  return { consoleErrors, pageErrors };
}

function assertNoUnexpectedBrowserErrors(errors, allowExpectedApiAbort = false) {
  const consoleErrors = allowExpectedApiAbort
    ? errors.consoleErrors.filter(message => !message.includes('net::ERR_FAILED') && !message.includes('Failed to load resource'))
    : errors.consoleErrors;
  expect(consoleErrors, 'Unexpected browser console errors').toEqual([]);
  expect(errors.pageErrors, 'Uncaught browser page errors').toEqual([]);
}

async function addFinanceFixtures(page) {
  await page.locator('.finance-card').first().locator('summary').click();
  await page.locator('#budget-description').fill('Ngân sách xe máy fixture');
  await page.locator('#budget-unit-amount').fill('450000');
  await page.locator('#create-budget-submit').click();
  await expect(page.locator('#budget-lines-list .finance-row')).toHaveCount(1);

  await page.locator('.finance-card').nth(1).locator('summary').click();
  for (const expense of [
    { amount: '180000', note: 'Bữa trưa fixture', time: '2027-04-01T12:00' },
    { amount: '95000', note: 'Nước uống fixture', time: '2027-04-01T14:00' }
  ]) {
    await page.locator('#expense-amount').fill(expense.amount);
    await page.locator('#expense-occurred-at').fill(expense.time);
    await page.locator('#expense-note').fill(expense.note);
    await page.locator('#create-expense-submit').click();
    await expect(page.locator('#expenses-list .finance-row')).toHaveCount(
      expense.note.startsWith('Bữa') ? 1 : 2
    );
  }
  await page.locator('.finance-card').nth(1).locator('summary').click();
  await page.locator('.finance-card').first().locator('summary').click();
}

async function expectScreenState(page, visibleSelector, hiddenSelectors) {
  await expect(page.locator(visibleSelector)).toBeVisible();
  for (const selector of hiddenSelectors) {
    const root = page.locator(selector);
    if (await root.count()) {
      await expect(root).toBeHidden();
      expect(await root.evaluate(element => element.inert), 'Hidden route must be inert: ' + selector).toBe(true);
    }
  }
  const focusableInHiddenScreens = await page.evaluate(selectors => {
    const focused = [];
    for (const selector of selectors) {
      const root = document.querySelector(selector);
      if (!root || (!root.hidden && !root.inert)) continue;
      const candidates = Array.from(root.querySelectorAll('a[href], button, input, select, textarea, summary, [tabindex]:not([tabindex="-1"])'));
      for (const element of candidates) {
        if (element.disabled) continue;
        element.focus({ preventScroll: true });
        if (document.activeElement === element) {
          focused.push({ root: selector, tag: element.tagName, id: element.id });
        }
      }
    }
    if (document.activeElement && document.activeElement !== document.body) document.activeElement.blur();
    return focused;
  }, hiddenSelectors);
  expect(focusableInHiddenScreens, 'Hidden routes must not retain focusable controls').toEqual([]);
}

async function createTripAndDay(page, title) {
  await signIn(page);
  await page.goto(BASE_URL + '/#trips');
  await expect(page.locator('#create-trip')).toBeVisible();
  await page.locator('#trip-title').fill(title);
  await page.locator('#trip-origin').fill('Đà Nẵng');
  await page.locator('#trip-destination').fill('Hội An');
  await page.locator('#trip-start').fill('2027-04-01');
  await page.locator('#trip-end').fill('2027-04-03');
  await page.locator('#create-trip-submit').click();
  const tripCard = page.locator('#trip-list .trip-card').filter({ hasText: title });
  await expect(tripCard).toHaveCount(1);
  const tripButton = tripCard.locator('[data-open-trip]');
  await expect(tripButton).toBeVisible();
  await tripButton.click();
  await expect(page.locator('#itinerary-workspace')).toBeVisible();
  await expect(page).toHaveURL(/#route\?trip=/);
  await expectScreenState(page, '#itinerary-workspace', ['.hero-section', '.feature-bento', '.next-step-section.page-container', '.section-heading', '#trip-list', '#create-trip', '#rental-workspace']);
  const selectedTripId = new URLSearchParams(new URL(page.url()).hash.split('?')[1] || '').get('trip');
  await page.goto(BASE_URL + '/#route');
  await expect(page.locator('#itinerary-workspace')).toBeVisible();
  await expect(page).toHaveURL(/#route$/);
  await page.goto(BASE_URL + '/#route?trip=' + encodeURIComponent(selectedTripId));
  await expect(page.locator('#itinerary-workspace')).toBeVisible();
  await expect(page.locator('#itinerary-workspace-title')).toHaveText(title);
  await expect(page.locator('#itinerary-versions')).toContainText('Chưa có lịch trình. Tạo một bản thủ công để thêm ngày và điểm dừng.');
  await page.locator('#create-itinerary-btn').click();
  await expect(page.locator('#itinerary-editor')).toBeVisible();
  await page.locator('#itinerary-day-date').fill('2027-04-01');
  await page.locator('#create-day-form button[type="submit"]').click();
  await expect(page.locator('#day-items-panel')).toBeVisible();
  await expect(page.locator('#day-items-list')).not.toContainText('Đang tải điểm dừng…');
  await expect(page.locator('#day-items-list')).not.toHaveAttribute('aria-busy', 'true');
}

async function addStop(page, title, address) {
  await page.locator('#item-title').fill(title);
  await page.locator('#item-type').selectOption('stop');
  await page.locator('#item-address').fill(address || '');
  await page.locator('#item-start').fill('09:00');
  await page.locator('#item-duration').fill('90');
  await page.locator('#create-item-submit').click();
  await expect(page.locator('.itinerary-item-card--stop').filter({ hasText: title })).toBeVisible();
  await expect(page.locator('#day-items-list')).not.toContainText('Đang tải điểm dừng…');
  await expect(page.locator('#day-items-list')).not.toHaveAttribute('aria-busy', 'true');
}

test('desktop: loading/error, reorder, edit, archive, rental handoff and keyboard', async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 1000 });
  const browserErrors = captureBrowserErrors(page);
  await createTripAndDay(page, 'QA desktop DealZ');

  let delayFirstRead = true;
  let failNextRead = false;
  let releaseFirstRead;
  let signalFirstRead;
  const firstReadStarted = new Promise(resolve => { signalFirstRead = resolve; });
  const firstReadGate = new Promise(resolve => { releaseFirstRead = resolve; });
  await page.route('**/items**', async route => {
    if (route.request().method() !== 'GET') return route.continue();
    if (delayFirstRead) {
      delayFirstRead = false;
      signalFirstRead();
      await firstReadGate;
      return route.continue();
    }
    if (failNextRead) {
      failNextRead = false;
      return route.abort();
    }
    return route.continue();
  });

  // Creating a second day selects it and triggers a deliberately held read.
  await page.locator('#itinerary-day-date').fill('2027-04-02');
  await page.locator('#create-day-form button[type="submit"]').click();
  await expect(page.locator('.day-chip')).toHaveCount(2);
  await firstReadStarted;
  await expect(page.locator('#day-items-list')).toHaveAttribute('aria-busy', 'true');
  await expect(page.locator('#day-items-list')).toContainText('Đang tải điểm dừng…');
  await page.locator('#day-items-list').scrollIntoViewIfNeeded();
  await page.screenshot({ path: 'artifacts/state-loading-items.png', fullPage: false });
  releaseFirstRead();
  await expect(page.locator('#day-items-list')).not.toHaveAttribute('aria-busy', 'true');

  // Switch to the first day to force a failing read; retry must recover.
  failNextRead = true;
  await page.locator('.day-chip').first().click();
  await expect(page.locator('#day-items-list')).toContainText('Thử tải lại');
  await page.locator('#day-items-list').scrollIntoViewIfNeeded();
  await page.screenshot({ path: 'artifacts/state-error-items.png', fullPage: false });
  await page.getByRole('button', { name: 'Thử tải lại' }).click();
  await expect(page.locator('#day-items-list')).not.toContainText('Thử tải lại');
  await page.screenshot({ path: 'artifacts/state-retry-success-items.png', fullPage: false });
  await expect(page.locator('#day-items-list')).not.toHaveAttribute('aria-busy', 'true');

  await addStop(page, 'Điểm A', 'Cầu Rồng, Đà Nẵng');
  await addStop(page, 'Điểm B', 'Phố cổ Hội An');
  await expect(page.locator('.itinerary-item-card--stop').filter({ hasText: 'Điểm A' }).locator('.item-directions')).toHaveAttribute('href', /google\.com\/maps\/dir/);
  await expect(page.locator('.route-connector').first()).toContainText('Chưa có đủ dữ liệu tuyến đường');
  await expect(page.locator('.route-connector__number, .route-connector__badge, .route-number')).toHaveCount(0);

  const cards = page.locator('.itinerary-item-card--stop');
  await expect(cards).toHaveCount(2);
  await cards.nth(1).getByRole('button', { name: 'Di chuyển lên' }).click();
  await expect(cards.nth(0).locator('h3')).toHaveText('Điểm B');
  await expect(cards.nth(1).locator('h3')).toHaveText('Điểm A');

  const firstCard = cards.nth(0);
  const details = firstCard.locator('.item-edit-details');
  const summary = details.locator('summary');
  await summary.focus();
  await page.keyboard.press('Enter');
  await expect(details).toHaveAttribute('open', '');
  await details.getByRole('textbox', { name: 'Tên điểm dừng' }).fill('Điểm B đã sửa');
  await details.locator('button[type="submit"]').click();
  await expect(cards.nth(0).locator('h3')).toHaveText('Điểm B đã sửa');

  await cards.nth(0).scrollIntoViewIfNeeded();
  await page.screenshot({ path: 'artifacts/desktop-stop-viewport.png', fullPage: false });
  await page.screenshot({ path: 'artifacts/desktop-itinerary.png', fullPage: true });

  const rentalLink = cards.nth(0).getByRole('link', { name: 'Mở mục thuê đồ cho Điểm B đã sửa' });
  await rentalLink.click();
  await expect(page).toHaveURL(/#shop\?/);
  await expect(page.locator('#rental-workspace')).toBeVisible();
  await expectScreenState(page, '#rental-workspace', ['.hero-section', '.feature-bento', '.next-step-section.page-container', '#trips', '#itinerary-workspace']);
  await expect(page.locator('.rental-next-step')).toBeVisible();
  await expect(page.locator('#rental-workspace-title')).toHaveText('Thuê đồ cho Điểm B đã sửa');
  await expect(page.locator('#rental-workspace')).toContainText('Danh mục thuê đồ chưa sẵn sàng');
  await page.locator('#rental-workspace').scrollIntoViewIfNeeded();
  await page.screenshot({ path: 'artifacts/desktop-rental-viewport.png', fullPage: false });
  await page.screenshot({ path: 'artifacts/desktop-rental-context.png', fullPage: true });
  await page.locator('#rental-back-link').click();
  await expect(page).toHaveURL(/#route\?trip=/);
  await expect(page.locator('#itinerary-workspace')).toBeVisible();
  await expectScreenState(page, '#itinerary-workspace', ['.hero-section', '.feature-bento', '.next-step-section.page-container', '#rental-workspace']);

  await page.locator('.itinerary-item-card--stop').filter({ hasText: 'Điểm A' }).locator('.item-archive-action').click();
  const archiveDialog = page.locator('.archive-dialog');
  await expect(archiveDialog).toBeVisible();
  expect(await archiveDialog.evaluate(dialog => dialog.parentElement === document.body), 'Archive dialog must be a top-level modal, not nested inside a stop card').toBe(true);
  await archiveDialog.getByRole('button', { name: 'Lưu trữ' }).click();
  await expect(page.locator('.itinerary-item-card--stop')).toHaveCount(1);
  await expect(page.locator('.itinerary-item-card--stop')).not.toContainText('Điểm A');
  await addStop(page, 'Điểm chưa có địa chỉ', '');
  const noAddressCard = page.locator('.itinerary-item-card--stop').filter({ hasText: 'Điểm chưa có địa chỉ' });
  await expect(noAddressCard.locator('.item-directions')).toHaveCount(0);
  assertNoUnexpectedBrowserErrors(browserErrors, true);
});

test('mobile: responsive stop card, CTA and keyboard focus', async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  const browserErrors = captureBrowserErrors(page);
  await createTripAndDay(page, 'QA mobile DealZ');
  await addStop(page, 'Điểm dừng mobile', 'Biển Mỹ Khê, Đà Nẵng');
  await addStop(page, 'Điểm kế tiếp chưa có địa chỉ', '');

  const card = page.locator('.itinerary-item-card--stop').first();
  const cardContentWidth = await card.evaluate(element => { const style = getComputedStyle(element); return element.getBoundingClientRect().width - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight) - parseFloat(style.borderLeftWidth) - parseFloat(style.borderRightWidth); });
  expect(cardContentWidth, 'Stop-card content width at 390px').toBeGreaterThanOrEqual(320);
  const imageSlot = card.locator('.itinerary-item-card__media');
  await expect(imageSlot).toBeVisible();
  await expect(imageSlot).toHaveAttribute('role', 'img');
  await expect(imageSlot).toHaveAttribute('aria-label', 'Chưa có ảnh cho Điểm dừng mobile');
  await expect(imageSlot.locator('.image-slot__title')).toHaveText('Điểm dừng mobile');
  await expect(imageSlot.locator('.image-slot__status')).toHaveText('Chưa có ảnh đã xác minh');
  await expect(imageSlot.locator('.image-slot__pin')).toBeVisible();
  expect(await imageSlot.evaluate(element => parseFloat(getComputedStyle(element).minHeight))).toBeGreaterThanOrEqual(112);
  await expect(card.getByRole('button', { name: 'Di chuyển lên' })).toBeDisabled();
  await expect(card.getByRole('button', { name: 'Di chuyển xuống' })).toBeEnabled();
  await expect(card.getByRole('link', { name: 'Mở mục thuê đồ cho Điểm dừng mobile' })).toBeVisible();
  await expect(card.locator('.item-directions')).toHaveAttribute('href', /google\.com\/maps\/dir/);
  await expect(page.locator('.route-connector').first()).toContainText('Chưa có đủ dữ liệu tuyến đường');
  await expect(page.locator('.route-connector__number, .route-connector__badge, .route-number')).toHaveCount(0);

  const dimensions = await page.evaluate(() => ({
    viewport: document.documentElement.clientWidth,
    content: document.documentElement.scrollWidth
  }));
  expect(dimensions.content).toBeLessThanOrEqual(dimensions.viewport);

  await card.locator('summary').focus();
  await expect(card.locator('summary')).toBeFocused();
  await page.keyboard.press('Enter');
  await expect(card.locator('.item-edit-details')).toHaveAttribute('open', '');

  await card.scrollIntoViewIfNeeded();
  await page.screenshot({ path: 'artifacts/mobile-stop-viewport.png', fullPage: false });
  await page.screenshot({ path: 'artifacts/mobile-itinerary.png', fullPage: true });

  await card.getByRole('link', { name: 'Mở mục thuê đồ cho Điểm dừng mobile' }).click();
  await expect(page).toHaveURL(/#shop\?stop=/);
  await expect(page.locator('#rental-workspace')).toBeVisible();
  await expect(page.locator('#rental-workspace-title')).toHaveText('Thuê đồ cho Điểm dừng mobile');
  await expect(page.locator('#rental-empty-state, .rental-empty-state')).toBeVisible();
  const rentalDimensions = await page.evaluate(() => ({ viewport: document.documentElement.clientWidth, content: document.documentElement.scrollWidth }));
  expect(rentalDimensions.content).toBeLessThanOrEqual(rentalDimensions.viewport);
  await page.screenshot({ path: 'artifacts/mobile-rental-viewport.png', fullPage: false });
  await page.screenshot({ path: 'artifacts/mobile-rental-context.png', fullPage: true });
  await page.locator('#rental-back-link').click();
  await expect(page).toHaveURL(/#route\?trip=/);
  await expect(page.locator('#itinerary-workspace')).toBeVisible();
  await expectScreenState(page, '#itinerary-workspace', ['.hero-section', '.feature-bento', '.next-step-section.page-container', '#rental-workspace']);

  await page.goto(BASE_URL + '/#trips');
  await expectScreenState(page, '#trip-list', ['.hero-section', '.feature-bento', '.next-step-section.page-container', '#itinerary-workspace', '#rental-workspace']);
  await page.goto(BASE_URL + '/#explore');
  await expectScreenState(page, '.hero-section', ['#trips', '#itinerary-workspace', '#rental-workspace']);

  await page.goto(BASE_URL + '/#shop');
  await expect(page.locator('#rental-workspace')).toBeVisible();
  await expect(page.locator('#rental-workspace-title')).toHaveText('Thuê đồ cho điểm dừng đã chọn');
  await expect(page.locator('#rental-back-link')).toHaveAttribute('href', '#trips');
  assertNoUnexpectedBrowserErrors(browserErrors);
});

test('local Vietnamese fonts load with glyph coverage and no external origin requests', async ({ page }) => {
  const externalRequests = [];
  page.on('request', request => {
    const requestUrl = new URL(request.url());
    if (requestUrl.origin !== new URL(BASE_URL).origin) externalRequests.push(request.url());
  });
  await signIn(page);
  const fontResult = await page.evaluate(async () => {
    const sample = 'Ướ ặ ẫ ỡ ọ';
    await document.fonts.load('800 40px "Bricolage Grotesque"', sample);
    await document.fonts.load('400 17px "Be Vietnam Pro"', sample);
    await document.fonts.ready;
    const heading = document.querySelector('h1');
    const body = document.querySelector('main p, .hero-section p');
    const input = document.querySelector('#trip-title');
    if (heading) heading.textContent = sample;
    if (body) body.textContent = sample;
    if (input) input.value = sample;
    return {
      bricolage: document.fonts.check('800 40px "Bricolage Grotesque"', sample),
      beVietnam: document.fonts.check('400 17px "Be Vietnam Pro"', sample),
      headingText: heading ? heading.textContent : '',
      bodyText: body ? body.textContent : '',
      inputValue: input ? input.value : '',
      headingFamily: heading ? getComputedStyle(heading).fontFamily : '',
      bodyFamily: body ? getComputedStyle(body).fontFamily : '',
      inputFamily: input ? getComputedStyle(input).fontFamily : ''
    };
  });
  expect(fontResult.bricolage, 'Bricolage Grotesque 800 must load Vietnamese glyphs').toBe(true);
  expect(fontResult.beVietnam, 'Be Vietnam Pro 400 must load Vietnamese glyphs').toBe(true);
  expect(fontResult.headingText).toBe('Ướ ặ ẫ ỡ ọ');
  expect(fontResult.bodyText).toBe('Ướ ặ ẫ ỡ ọ');
  expect(fontResult.inputValue).toBe('Ướ ặ ẫ ỡ ọ');
  expect(fontResult.headingFamily).toContain('Bricolage Grotesque');
  expect(fontResult.bodyFamily).toContain('Be Vietnam Pro');
  expect(fontResult.inputFamily).toContain('Be Vietnam Pro');
  expect(externalRequests, 'Site must not request fonts or other assets from external origins').toEqual([]);
  const proof = await page.evaluate(async () => {
    const response = await fetch('/wp-content/themes/dealz-theme/assets/fonts/font-proof.json');
    return response.json();
  });
  expect(proof.total_bytes).toBe(201432);
  expect(proof.total_kib).toBe(196.71);
  expect(proof.total_bytes).toBeLessThanOrEqual(300 * 1024);
  expect(proof.files.some(file => file.subset === 'latin-ext')).toBe(false);
  expect(proof.be_vietnam_pro_tnum).toBe(false);
  expect(proof.be_vietnam_pro_400_units_per_em).toBe(1000);
  expect(proof.be_vietnam_pro_400_digit_advance_widths).toEqual({ '0': 676, '1': 385, '2': 651, '3': 660, '4': 710, '5': 660, '6': 680, '7': 594, '8': 634, '9': 680 });
  expect(proof.be_vietnam_pro_400_tabular_by_advance_width).toBe(false);
});


test('admin-only styleguide is noindex and exposes the component states', async ({ page }) => {
  await signIn(page);
  const response = await page.goto(BASE_URL + '/styleguide/');
  expect(response.status()).toBe(200);
  await expect(page.getByRole('heading', { name: 'Trang styleguide', level: 1 })).toBeVisible();
  await expect(page.locator('meta[name="robots"]').last()).toHaveAttribute('content', /noindex/);
  await expect(page.locator('[data-btn="primary"]')).toBeVisible();
  await expect(page.locator('.dz-chip--selected')).toBeVisible();
  await expect(page.locator('.dz-card--budget')).toBeVisible();
  await expect(page.locator('.dz-toast--error')).toBeVisible();
  await expect(page.locator('.dz-skeleton[aria-busy="true"]')).toBeVisible();
  await expect(page.locator('.route-connector')).toContainText('Chưa có đủ dữ liệu tuyến đường');
});


test('legal draft routes are public noindex and do not invent operative terms', async ({ page }) => {
  const routes = [
    ['/chinh-sach-coc-va-huy/', 'Chính sách cọc và hủy'],
    ['/dieu-khoan/', 'Điều khoản sử dụng'],
    ['/bao-mat/', 'Chính sách bảo mật']
  ];
  for (const [path, title] of routes) {
    const response = await page.goto(BASE_URL + path);
    expect(response.status()).toBe(200);
    await expect(page.locator('h1')).toHaveText(title);
    await expect(page.locator('meta[name="robots"]').last()).toHaveAttribute('content', /noindex/);
    await expect(page.locator('.notice--warning')).toContainText('Bản nháp, chưa có hiệu lực');
    await expect(page.locator('main')).not.toContainText(/\d+\s?%/);
    await expect(page.locator('main')).not.toContainText(/mã số thuế|địa chỉ pháp lý|tỷ lệ cọc|giờ hủy/i);
  }
  await page.goto(BASE_URL + '/');
  await expect(page.locator('.zalo-float')).toHaveCount(0);
  await expect(page.locator('.site-footer__support')).toHaveCount(0);
  await expect(page.locator('.site-footer__legal a')).toHaveCount(3);
  await expect(page.locator('body')).not.toContainText(/cấu hình|QA/i);
});

test('styleguide is not accessible without administrator capability', async ({ page }) => {
  const response = await page.goto(BASE_URL + '/styleguide/');
  expect(response.status()).toBe(404);
  await expect(page.getByRole('heading', { name: 'Không tìm thấy trang', level: 1 })).toBeVisible();
});


test('journey badge counts only unarchived main stops across the selected trip', async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await createTripAndDay(page, 'QA badge DealZ');
  const badge = page.locator('#mobile-trip-badge');
  await addStop(page, 'Điểm badge 1', '');
  await addStop(page, 'Điểm badge 2', '');
  await expect(badge).toHaveText('2');
  await expect(badge).toHaveAttribute('aria-label', '2 điểm dừng chưa lưu trữ');
  await page.locator('#item-title').fill('Bữa trưa không tính vào badge');
  await page.locator('#item-type').selectOption('lunch');
  await page.locator('#item-start').fill('12:00');
  await page.locator('#item-duration').fill('60');
  await page.locator('#create-item-submit').click();
  await expect(page.locator('.itinerary-item-card--meal')).toHaveCount(1);
  await expect(badge).toHaveText('2');
  await addStop(page, 'Điểm badge 3', '');
  await addStop(page, 'Điểm badge 4', '');
  await expect(badge).toHaveText('4');
  await expect(page.locator('#item-type option[value="stop"]')).toBeDisabled();
  const archive = page.locator('.itinerary-item-card--stop').first().locator('.item-archive-action');
  await archive.click();
  await page.locator('.archive-dialog').getByRole('button', { name: 'Lưu trữ' }).click();
  await expect(badge).toHaveText('3');
});

test('axe-core WCAG 2.1 AA scan on home, itinerary, rental and admin styleguide', async ({ page }) => {
  const scan = async label => {
    const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    expect(results.violations.map(v => ({ id: v.id, impact: v.impact, nodes: v.nodes.map(n => n.target) })), label).toEqual([]);
  };
  await page.goto(BASE_URL + '/');
  await scan('Home page accessibility violations');
  await createTripAndDay(page, 'QA axe DealZ');
  await addStop(page, 'Điểm dừng axe', 'Đà Nẵng');
  await scan('Itinerary accessibility violations');
  await page.locator('.item-rental-cta').click();
  await expect(page.locator('#rental-workspace')).toBeVisible();
  await scan('Rental empty state accessibility violations');
  await page.goto(BASE_URL + '/styleguide/');
  await scan('Admin styleguide accessibility violations');
});

test('six viewport matrix has no horizontal overflow or undersized controls', async ({ page }) => {
  await signIn(page);
  for (const width of [360, 390, 768, 1024, 1280, 1536]) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto(BASE_URL + '/');
    const metrics = await page.evaluate(() => {
      const visible = element => { const r = element.getBoundingClientRect(); const s = getComputedStyle(element); return r.width > 0 && r.height > 0 && s.visibility !== 'hidden' && s.display !== 'none'; };
      const collectUndersized = () => Array.from(document.querySelectorAll('button, .button, input, select, textarea')).filter(visible).filter(element => !element.closest('#wpadminbar')).filter(element => { const r = element.getBoundingClientRect(); return r.width < 44 || r.height < 44; }).map(element => ({ id: element.id, tag: element.tagName, className: element.className, width: Math.round(element.getBoundingClientRect().width), height: Math.round(element.getBoundingClientRect().height) }));
      const probe = document.createElement('button');
      probe.id = 'qa-undersized-control-probe';
      probe.textContent = 'QA';
      probe.style.cssText = 'position:fixed!important;left:0!important;top:0!important;width:43px!important;height:43px!important;min-width:0!important;min-height:0!important;padding:0!important;z-index:999999!important;';
      document.body.appendChild(probe);
      const probeDetected = collectUndersized().some(control => control.id === probe.id);
      probe.remove();
      const undersized = collectUndersized();
      const rentalCard = document.querySelector('.feature-card--rental');
      const rentalOverflow = rentalCard ? [rentalCard, ...rentalCard.querySelectorAll('*')].filter(element => element.scrollWidth > element.clientWidth).map(element => ({ tag: element.tagName, className: element.className, scrollWidth: element.scrollWidth, clientWidth: element.clientWidth })) : ['missing rental card'];
      const hero = document.querySelector('.hero-section h1');
      let heroLastLineWords = 0;
      if (hero && hero.firstChild && hero.firstChild.nodeType === Node.TEXT_NODE) {
        const text = hero.firstChild.textContent;
        const words = Array.from(text.matchAll(/\S+/g));
        const tops = words.map(match => { const range = document.createRange(); range.setStart(hero.firstChild, match.index); range.setEnd(hero.firstChild, match.index + match[0].length); return range.getBoundingClientRect().top; });
        const lastTop = Math.max(...tops);
        heroLastLineWords = tops.filter(top => Math.abs(top - lastTop) < 1).length;
      }
      return { viewport: document.documentElement.clientWidth, scroll: document.documentElement.scrollWidth, probeDetected, undersized, rentalOverflow, heroLastLineWords };
    });
    expect(metrics.scroll, 'Horizontal overflow at viewport ' + width).toBeLessThanOrEqual(metrics.viewport);
    expect(metrics.probeDetected, 'The 44px gate must detect a real 43px control at viewport ' + width).toBe(true);
    expect(metrics.undersized, 'Controls smaller than 44px outside WordPress admin bar at viewport ' + width).toEqual([]);
    if (width === 360 || width === 390) expect(metrics.rentalOverflow, 'Rental bento content overflow at viewport ' + width).toEqual([]);
    expect(metrics.heroLastLineWords, 'Hero heading must not leave a one-word orphan at viewport ' + width).toBeGreaterThanOrEqual(2);
  }
});

test('reduced motion, 24-hour time, keyboard focus and admin-bar offset', async ({ page }) => {
  await signIn(page);
  await page.setViewportSize({ width: 390, height: 844 });
  await createTripAndDay(page, 'QA locale and motion DealZ');
  await expect(page.locator('#create-item-submit')).toBeEnabled();
  await page.locator('#create-item-submit').click();
  await expect(page.locator('#item-title')).toHaveAttribute('aria-invalid', 'true');
  await expect(page.locator('#item-title-error')).toBeVisible();
  await expect(page.locator('#create-item-submit')).toBeEnabled();
  await page.emulateMedia({ reducedMotion: 'reduce' });
  const reduced = await page.evaluate(() => { const el = document.querySelector('.dz-skeleton'); return el ? getComputedStyle(el).animationDuration : '0s'; });
  expect(['0s', '0.01ms']).toContain(reduced);
  const time = page.locator('#item-start');
  await expect(time).toHaveAttribute('type', 'text');
  await expect(time).toHaveAttribute('inputmode', 'numeric');
  await expect(time).toHaveAttribute('pattern', '(?:[01][0-9]|2[0-3]):[0-5][0-9]');
  await time.fill('09:30');
  await expect(time).toHaveValue('09:30');
  await time.fill('');
  await page.locator('#item-title').fill('Điểm chưa đặt giờ');
  await page.locator('#create-item-submit').click();
  const unsetTimeCard = page.locator('.itinerary-item-card--stop').filter({ hasText: 'Điểm chưa đặt giờ' });
  await expect(unsetTimeCard.locator('.item-time')).toHaveText('Chưa đặt giờ');
  const browser = page.context().browser();
  for (const locale of ['en-US', 'vi-VN']) {
    const localeContext = await browser.newContext({ locale });
    const localePage = await localeContext.newPage();
    await signIn(localePage);
    await createTripAndDay(localePage, 'QA locale ' + locale);
    const localeTime = localePage.locator('#item-start');
    await expect(localeTime).toHaveAttribute('type', 'text');
    await localeTime.fill('09:30');
    await expect(localeTime).toHaveValue('09:30');
    await localeContext.close();
  }
  await page.keyboard.press('Tab');
  await page.locator('.finance-card').first().locator('summary').click();
  await page.locator('.finance-card').nth(1).locator('summary').click();
  const focusable = page.locator('#item-title, #item-type, #item-address, #item-start, #item-end, #item-duration, #create-item-submit, #budget-description, #budget-quantity, #budget-unit-amount, #budget-cost-type, #create-budget-submit, #expense-amount, #expense-occurred-at, #expense-note, #create-expense-submit, .finance-card summary');
  for (let index = 0; index < await focusable.count(); index++) {
    const control = focusable.nth(index);
    await control.focus();
    const controlId = await control.getAttribute('id');

    if (controlId === 'item-title') {
      const diagnosticSnapshot = element => {
        const computed = getComputedStyle(element);
        const matchingRules = Array.from(document.styleSheets).flatMap(sheet => {
          try {
            const collect = (rules, conditions = []) => {
              const found = [];
              for (const rule of Array.from(rules)) {
                if (rule.selectorText && rule.style && element.matches(rule.selectorText) && /(outline|transition)/i.test(rule.cssText)) {
                  const variables = Array.from(rule.cssText.matchAll(/var\(\s*(--[\w-]+)/g), match => match[1]);
                  found.push({
                    source: sheet.href || '<inline style element>',
                    selector: rule.selectorText,
                    cssText: rule.cssText,
                    conditions,
                    variables
                  });
                }
                if (rule.cssRules) found.push(...collect(rule.cssRules, conditions.concat(rule.conditionText || rule.constructor.name)));
              }
              return found;
            };
            return collect(sheet.cssRules);
          } catch (error) {
            return [{ source: sheet.href || '<inline style element>', inaccessible: String(error), variables: [] }];
          }
        });
        const customPropertyNames = [...new Set(matchingRules.flatMap(rule => rule.variables || []))];
        return {
          matchesFocusVisible: element.matches(':focus-visible'),
          outlineStyle: computed.outlineStyle,
          outlineWidth: computed.outlineWidth,
          outlineColor: computed.outlineColor,
          outlineOffset: computed.outlineOffset,
          transitionProperty: computed.transitionProperty,
          transitionDuration: computed.transitionDuration,
          animationCount: element.getAnimations().length,
          customProperties: Object.fromEntries(customPropertyNames.map(name => [name, computed.getPropertyValue(name).trim()])),
          matchingRules,
          inlineStyle: element.getAttribute('style')
        };
      };

      const diagnosticStartedAt = Date.now();
      const initialDiagnostic = await control.evaluate(diagnosticSnapshot);
      console.log('[FOCUS_DIAGNOSTIC_INITIAL] ' + JSON.stringify(initialDiagnostic));

      let widthReachedAfterMs = null;
      let pollError = null;
      try {
        await expect.poll(async () => {
          const width = await control.evaluate(element => parseFloat(getComputedStyle(element).outlineWidth));
          if (width >= 3 && widthReachedAfterMs === null) widthReachedAfterMs = Date.now() - diagnosticStartedAt;
          return width;
        }, { timeout: 500, intervals: [10, 25, 50, 100] }).toBeGreaterThanOrEqual(3);
      } catch (error) {
        pollError = String(error.message).split('\\n')[0];
      }

      const afterPollDiagnostic = await control.evaluate(diagnosticSnapshot);
      console.log('[FOCUS_DIAGNOSTIC_AFTER_POLL] ' + JSON.stringify({
        elapsedMs: Date.now() - diagnosticStartedAt,
        widthReachedAfterMs,
        pollError,
        ...afterPollDiagnostic
      }));
    }

    await page.keyboard.press('Tab');
    await page.keyboard.press('Shift+Tab');
    await expect(control).toBeFocused();
    await expect(control).toHaveCSS('outline-width', '3px');
    await expect(control).toHaveCSS('transition-property', 'none');
    if (controlId === 'item-title') await expect(control).toHaveCSS('outline-color', 'rgb(214, 63, 0)');
    const focus = await control.evaluate(element => {
      const style = getComputedStyle(element);
      const matchingRules = Array.from(document.styleSheets).flatMap(sheet => {
        try {
          const collect = (rules, conditions = []) => {
            const found = [];
            for (const rule of Array.from(rules)) {
              if (rule.selectorText && rule.style && element.matches(rule.selectorText) && /(outline|transition)/i.test(rule.cssText)) {
                found.push({ source: sheet.href || '<inline style element>', selector: rule.selectorText, cssText: rule.cssText, conditions });
              }
              if (rule.cssRules) found.push(...collect(rule.cssRules, conditions.concat(rule.conditionText || rule.constructor.name)));
            }
            return found;
          };
          return collect(sheet.cssRules);
        } catch (error) {
          return [{ source: sheet.href || '<inline style element>', inaccessible: String(error) }];
        }
      });
      return {
        visible: element.matches(':focus-visible'),
        style: style.outlineStyle,
        width: style.outlineWidth,
        color: style.outlineColor,
        offset: style.outlineOffset,
        transitionProperty: style.transitionProperty,
        transitionDuration: style.transitionDuration,
        animationCount: element.getAnimations().length,
        matchingRules,
        inlineStyle: element.getAttribute('style')
      };
    });
    expect(focus.visible, 'Control must match :focus-visible: ' + controlId).toBe(true);
    expect(focus.transitionProperty, 'Focus transition must be disabled: ' + controlId).toBe('none');
    expect(focus.style, 'Focus outline style: ' + controlId).not.toBe('none');
    expect(parseFloat(focus.width), 'Focus outline width: ' + controlId + ' ' + JSON.stringify(focus)).toBeGreaterThanOrEqual(3);
    if (controlId === 'item-title') expect(focus.color, 'Input focus color: ' + JSON.stringify(focus)).toBe('rgb(214, 63, 0)');
    expect(parseFloat(focus.offset), 'Focus outline offset: ' + controlId).toBeGreaterThanOrEqual(3);
  }
  const backLink = page.locator('#back-to-trips');
  await backLink.focus();
  await page.keyboard.press('Tab');
  await page.keyboard.press('Shift+Tab');
  await expect(backLink).toBeFocused();
  await expect(backLink).toHaveCSS('transition-property', 'none');
  const backLinkFocus = await backLink.evaluate(element => ({
    visible: element.matches(':focus-visible'),
    style: getComputedStyle(element).outlineStyle,
    width: getComputedStyle(element).outlineWidth,
    offset: getComputedStyle(element).outlineOffset
  }));
  expect(backLinkFocus.visible, 'Back link must be reached with keyboard modality').toBe(true);
  expect(backLinkFocus.style).not.toBe('none');
  expect(parseFloat(backLinkFocus.width)).toBeGreaterThanOrEqual(3);
  expect(parseFloat(backLinkFocus.offset)).toBeGreaterThanOrEqual(3);
  const expenseForm = page.locator('#create-expense-form');
  const expenseDetails = expenseForm.locator('xpath=..');
  if (!(await expenseDetails.evaluate(element => element.open))) await expenseDetails.locator('summary').click();
  await expect(expenseDetails).toHaveAttribute('open', '');
  const financeInput = page.locator('#expense-amount');
  await expect(financeInput).toBeVisible();
  await financeInput.focus();
  await expect(financeInput).toBeFocused();
  await expect(financeInput).toHaveCSS('border-color', 'rgb(253, 85, 10)');
  await expect(financeInput).toHaveCSS('box-shadow', /0px 0px 0px 4px/);
  const financeFocus = await financeInput.evaluate(element => ({ border: getComputedStyle(element).borderColor, shadow: getComputedStyle(element).boxShadow }));
  expect(financeFocus.border).toBe('rgb(253, 85, 10)');
  expect(financeFocus.shadow).toContain('4px');
  const headerLink = page.locator('.site-header a').first();
  await headerLink.focus();
  await page.keyboard.press('Tab');
  await page.keyboard.press('Shift+Tab');
  await expect(headerLink).toBeFocused();
  await expect(headerLink).toHaveCSS('transition-property', 'none');
  await expect(headerLink).toHaveCSS('outline-width', '3px');
  await expect(headerLink).toHaveCSS('outline-offset', '3px');
  await expect(headerLink).toHaveCSS('outline-color', 'rgb(214, 63, 0)');

  const footerLink = page.locator('.site-footer a').first();
  await footerLink.focus();
  await page.keyboard.press('Tab');
  await page.keyboard.press('Shift+Tab');
  await expect(footerLink).toBeFocused();
  await expect(footerLink).toHaveCSS('transition-property', 'none');
  await expect(footerLink).toHaveCSS('outline-width', '3px');
  await expect(footerLink).toHaveCSS('outline-offset', '3px');
  await expect(footerLink).toHaveCSS('outline-color', 'rgb(224, 243, 84)');
  await page.evaluate(() => window.scrollTo(0, 600));
  const top = await page.locator('.site-header').evaluate(el => el.getBoundingClientRect().top);
  expect(top).toBeGreaterThanOrEqual(0);
});
test('one primary action per active screen and CLS budget', async ({ page }) => {
  await signIn(page);
  await page.goto(BASE_URL + '/');
  await expect(page.locator('.button--primary:visible')).toHaveCount(1);
  await createTripAndDay(page, 'QA primary DealZ');
  await expect(page.locator('.button--primary:visible')).toHaveCount(1);
  await addStop(page, 'Điểm CTA', 'Đà Nẵng');
  await page.locator('.item-rental-cta').click();
  await expect(page.locator('.button--primary:visible')).toHaveCount(1);
  await page.goto(BASE_URL + '/');
  await page.waitForLoadState('networkidle');
  const cls = await page.evaluate(() => performance.getEntriesByType('layout-shift').filter(entry => !entry.hadRecentInput).reduce((sum, entry) => sum + entry.value, 0));
  expect(cls).toBeLessThan(0.1);
});

test('capture home, itinerary, edit panel, rental and styleguide viewport screenshots at required widths', async ({ page }) => {
  test.setTimeout(120000);
  await signIn(page);
  const widths = [360, 390, 768, 1024, 1280, 1536];
  const scrollTo = async (top = 0) => {
    await page.evaluate(target => {
      const root = document.scrollingElement || document.documentElement;
      const previousBehavior = root.style.scrollBehavior;
      root.style.scrollBehavior = 'auto';
      root.scrollTop = target;
      document.body.scrollTop = target;
      window.scrollTo(0, target);
      root.style.scrollBehavior = previousBehavior;
    }, top);
  };
  const expectFullyInViewport = async locator => {
    await expect.poll(() => locator.evaluate(element => {
      const rect = element.getBoundingClientRect();
      return rect.top >= 0 && rect.bottom <= window.innerHeight;
    })).toBe(true);
  };
  const scrollBelowHeader = async locator => {
    const targetTop = await locator.evaluate(element => {
      const header = document.querySelector('.site-header');
      const headerBottom = header ? header.getBoundingClientRect().bottom : 0;
      return Math.max(0, window.scrollY + element.getBoundingClientRect().top - headerBottom - 16);
    });
    await scrollTo(targetTop);
    await expectFullyInViewport(locator);
  };
  for (const width of widths) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto(BASE_URL + '/#explore');
    await scrollTo(0);
    await expect(page.locator('.site-header')).toBeInViewport({ ratio: 0.9 });
    await expectFullyInViewport(page.locator('.hero-section h1'));
    await page.screenshot({ path: 'artifacts/home-' + width + '-viewport-default.png', fullPage: false });
  }

  for (const width of [360, 390]) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto(BASE_URL + '/#explore');
    const rentalCard = page.locator('.feature-card--rental');
    await rentalCard.scrollIntoViewIfNeeded();
    await expectFullyInViewport(rentalCard.locator('h2'));
    await expectFullyInViewport(rentalCard.locator('.dz-chip'));
    await page.screenshot({ path: 'artifacts/home-' + width + '-viewport-bento.png', fullPage: false });
  }

  await createTripAndDay(page, 'QA responsive screenshots DealZ');
  await addStop(page, 'Điểm dừng ảnh QA A', 'Đà Nẵng');
  await addStop(page, 'Điểm dừng ảnh QA B', 'Hội An');
  await addFinanceFixtures(page);
  await expect(page.locator('.itinerary-item-card--stop')).toHaveCount(2);
  await expect(page.locator('#expenses-list .finance-row')).toHaveCount(2);

  for (const width of widths) {
    await page.setViewportSize({ width, height: 900 });
    await scrollTo(0);
    await expect(page.locator('.site-header')).toBeInViewport({ ratio: 0.9 });
    await expectFullyInViewport(page.locator('#itinerary-workspace-title'));
    if (width === 1280) {
      const dayRail = await page.locator('.itinerary-rail__days').evaluate(element => {
        const selected = element.querySelector('.day-chip[aria-pressed="true"]');
        return {
          panelBackground: getComputedStyle(element).backgroundColor,
          headingColor: getComputedStyle(element.querySelector('h3')).color,
          selectedBackground: selected ? getComputedStyle(selected).backgroundColor : null,
          selectedColor: selected ? getComputedStyle(selected).color : null
        };
      });
      expect(dayRail).toEqual({
        panelBackground: 'rgb(1, 93, 71)',
        headingColor: 'rgb(255, 255, 255)',
        selectedBackground: 'rgb(224, 243, 84)',
        selectedColor: 'rgb(1, 93, 71)'
      });
    }
    await page.screenshot({ path: 'artifacts/itinerary-' + width + '-viewport-default.png', fullPage: false });

    const card = page.locator('.itinerary-item-card--stop').first();
    await card.locator('summary').click();
    await expect(card.locator('.item-edit-details')).toHaveAttribute('open', '');
    await scrollBelowHeader(card.locator('summary'));
    await expect(page.locator('.site-header')).toBeInViewport({ ratio: 0.9 });
    await page.screenshot({ path: 'artifacts/itinerary-' + width + '-viewport-edit-open.png', fullPage: false });
    await card.locator('summary').click();

    await card.locator('.item-rental-cta').click();
    await expect(page.locator('#rental-workspace')).toBeVisible();
    await scrollBelowHeader(page.locator('#rental-context-chip'));
    await expect(page.locator('.site-header')).toBeInViewport({ ratio: 0.9 });
    await expectFullyInViewport(page.locator('#rental-context-chip'));
    await expectFullyInViewport(page.locator('#rental-workspace-title'));
    if (width >= 1024) {
      const layout = await page.locator('.rental-layout').evaluate(element => ({
        width: element.getBoundingClientRect().width,
        cardWidth: element.querySelector('.rental-empty-state').getBoundingClientRect().width
      }));
      expect(layout.cardWidth).toBeGreaterThanOrEqual(layout.width - 2);
      await expect(page.locator('.rental-empty-state__intro')).toBeVisible();
      await expect(page.locator('.rental-empty-state__content')).toBeVisible();
    }
    if (width === 1280) {
      await expect.poll(() => page.locator('.site-header').evaluate(element => getComputedStyle(element).backgroundColor))
        .toBe('rgb(251, 247, 238)');
    }
    await page.screenshot({ path: 'artifacts/rental-' + width + '-viewport-empty.png', fullPage: false });
    if (width === 1280) {
      const diagnostic = await page.evaluate(() => {
        const describe = element => {
          if (!element) return null;
          const style = getComputedStyle(element);
          const rect = element.getBoundingClientRect();
          return {
            tag: element.tagName.toLowerCase(),
            id: element.id || null,
            className: typeof element.className === 'string' ? element.className : null,
            text: (element.innerText || element.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 100),
            rect: { x: rect.x, y: rect.y, top: rect.top, right: rect.right, bottom: rect.bottom, left: rect.left, width: rect.width, height: rect.height },
            position: style.position,
            zIndex: style.zIndex,
            opacity: style.opacity,
            backgroundColor: style.backgroundColor,
            backgroundImage: style.backgroundImage,
            boxShadow: style.boxShadow,
            filter: style.filter,
            backdropFilter: style.backdropFilter,
            transform: style.transform,
            overflow: style.overflow,
            pointerEvents: style.pointerEvents
          };
        };
        const header = document.querySelector('.site-header');
        const headerRect = header ? header.getBoundingClientRect() : null;
        const pseudo = element => {
          if (!element) return null;
          return ['::before', '::after'].map(selector => {
            const style = getComputedStyle(element, selector);
            return {
              selector,
              content: style.content,
              display: style.display,
              position: style.position,
              zIndex: style.zIndex,
              opacity: style.opacity,
              backgroundColor: style.backgroundColor,
              backgroundImage: style.backgroundImage,
              boxShadow: style.boxShadow,
              filter: style.filter,
              backdropFilter: style.backdropFilter,
              transform: style.transform
            };
          });
        };
        const points = [[100, 85], [200, 85], [1130, 85], [1200, 85]].map(([x, y]) => ({
          x, y, stack: document.elementsFromPoint(x, y).slice(0, 8).map(describe)
        }));
        const overlappingLayers = [...document.querySelectorAll('body *')].map(element => {
          const rect = element.getBoundingClientRect();
          const style = getComputedStyle(element);
          const overlaps = headerRect && rect.bottom >= headerRect.top - 8 && rect.top <= headerRect.bottom + 8 && rect.right > 0 && rect.left < innerWidth;
          const hasVisualEffect = style.boxShadow !== 'none' || style.filter !== 'none' || style.backdropFilter !== 'none';
          return overlaps && (hasVisualEffect || ['fixed', 'sticky', 'absolute'].includes(style.position))
            ? { element: describe(element), pseudo: pseudo(element) }
            : null;
        }).filter(Boolean).slice(0, 80);
        return {
          viewport: { width: innerWidth, height: innerHeight, scrollY: window.scrollY },
          bodyClass: document.body.className,
          header: describe(header),
          headerPseudo: pseudo(header),
          samplePoints: points,
          overlappingLayers
        };
      });
      fs.mkdirSync('artifacts', { recursive: true });
      expect(diagnostic.header.backgroundColor, 'Rental header must be opaque so the toolbar beneath it cannot bleed through').toBe('rgb(251, 247, 238)');
      expect(diagnostic.header.backdropFilter, 'Rental header must not blur underlying toolbar content').toBe('none');
      fs.writeFileSync('artifacts/rental-shadow-diagnostic.json', JSON.stringify(diagnostic, null, 2));
      console.log('[rental-shadow-diagnostic] ' + JSON.stringify(diagnostic));
    }
    await page.locator('#rental-back-link').click();
    await expect(page.locator('#itinerary-workspace')).toBeVisible();
  }

  await page.goto(BASE_URL + '/styleguide/');
  await expect(page.getByRole('heading', { name: 'Trang styleguide', level: 1 })).toBeVisible();
  for (const width of widths) {
    await page.setViewportSize({ width, height: 900 });
    await scrollTo(0);
    await expect(page.locator('.site-header')).toBeInViewport({ ratio: 0.9 });
    await expectFullyInViewport(page.getByRole('heading', { name: 'Trang styleguide', level: 1 }));
    await page.screenshot({ path: 'artifacts/styleguide-' + width + '-viewport-default.png', fullPage: false });
  }
});

test.describe('T8 motion evidence', () => {
  test.use({ video: 'on' });

  test('records the primary CTA hover transition', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 900 });
    await signIn(page);
    const cta = page.locator('.hero-section__cta');
    await expect(cta).toBeVisible();

    const transitionDuration = await cta.evaluate(element => getComputedStyle(element).transitionDuration);
    expect(transitionDuration).toContain('0.16s');
    const initialTransform = await cta.evaluate(element => getComputedStyle(element).transform);
    await page.screenshot({ path: 'artifacts/motion-primary-default.png', fullPage: false });

    await cta.hover();
    await expect.poll(() => cta.evaluate(element => getComputedStyle(element).transform))
      .not.toBe(initialTransform);
    await page.waitForTimeout(180);
    await expect(cta).toHaveCSS('transform', /matrix/);
    await page.screenshot({ path: 'artifacts/motion-primary-hover.png', fullPage: false });

    await cta.click();
    await expect(page).toHaveURL(/#create-trip/);
  });
});

