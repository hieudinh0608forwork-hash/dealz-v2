const { test, expect } = require('@playwright/test');

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

async function createTripAndDay(page, title) {
  await signIn(page);
  await page.locator('#trip-title').fill(title);
  await page.locator('#trip-origin').fill('Đà Nẵng');
  await page.locator('#trip-destination').fill('Hội An');
  await page.locator('#trip-start').fill('2027-04-01');
  await page.locator('#trip-end').fill('2027-04-03');
  await page.locator('#create-trip-submit').click();
  const tripButton = page.locator('#trip-list [data-open-trip]').first();
  await expect(tripButton).toBeVisible();
  await tripButton.click();
  await expect(page.locator('#itinerary-workspace')).toBeVisible();
  await page.locator('#create-itinerary-btn').click();
  await expect(page.locator('#itinerary-editor')).toBeVisible();
  await page.locator('#itinerary-day-date').fill('2027-04-01');
  await page.locator('#create-day-form button[type="submit"]').click();
  await expect(page.locator('#day-items-panel')).toBeVisible();
}

async function addStop(page, title, address) {
  await page.locator('#item-title').fill(title);
  await page.locator('#item-type').selectOption('stop');
  await page.locator('#item-address').fill(address || '');
  await page.locator('#item-start').fill('09:00');
  await page.locator('#item-duration').fill('90');
  await page.locator('#create-item-submit').click();
  await expect(page.locator('.itinerary-item-card--stop').filter({ hasText: title })).toBeVisible();
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
  releaseFirstRead();
  await expect(page.locator('#day-items-list')).not.toHaveAttribute('aria-busy', 'true');

  // Switch to the first day to force a failing read; retry must recover.
  failNextRead = true;
  await page.locator('.day-chip').first().click();
  await expect(page.locator('#day-items-list')).toContainText('Thử tải lại');
  await page.getByRole('button', { name: 'Thử tải lại' }).click();
  await expect(page.locator('#day-items-list')).not.toHaveAttribute('aria-busy', 'true');

  await addStop(page, 'Điểm A', 'Cầu Rồng, Đà Nẵng');
  await addStop(page, 'Điểm B', 'Phố cổ Hội An');
  await expect(page.locator('.itinerary-item-card--stop').filter({ hasText: 'Điểm A' }).locator('.item-directions')).toHaveAttribute('href', /google\.com\/maps\/dir/);
  await expect(page.locator('.route-connector').first()).toContainText('Chưa có dữ liệu tuyến đường');
  await expect(page.locator('.route-connector__number, .route-connector__badge, .route-number')).toHaveCount(0);

  const cards = page.locator('.itinerary-item-card--stop');
  await expect(cards).toHaveCount(2);
  await cards.nth(1).getByRole('button', { name: 'Di chuyển lên' }).click();
  await expect(cards.nth(0).locator('h4')).toHaveText('Điểm B');
  await expect(cards.nth(1).locator('h4')).toHaveText('Điểm A');

  const firstCard = cards.nth(0);
  const details = firstCard.locator('.item-edit-details');
  const summary = details.locator('summary');
  await summary.focus();
  await page.keyboard.press('Enter');
  await expect(details).toHaveAttribute('open', '');
  await details.locator('input[type="text"]').fill('Điểm B đã sửa');
  await details.locator('button[type="submit"]').click();
  await expect(cards.nth(0).locator('h4')).toHaveText('Điểm B đã sửa');

  await cards.nth(0).scrollIntoViewIfNeeded();
  await page.screenshot({ path: 'artifacts/desktop-stop-viewport.png', fullPage: false });
  await page.screenshot({ path: 'artifacts/desktop-itinerary.png', fullPage: true });

  const rentalLink = cards.nth(0).getByRole('link', { name: 'Mở mục thuê đồ cho Điểm B đã sửa' });
  await rentalLink.click();
  await expect(page).toHaveURL(/#shop\?/);
  await expect(page.locator('#rental-workspace')).toBeVisible();
  await expect(page.locator('#rental-workspace-title')).toHaveText('Thuê đồ cho Điểm B đã sửa');
  await expect(page.locator('#rental-workspace')).toContainText('Danh mục thuê đồ chưa sẵn sàng');
  await page.locator('#rental-workspace').scrollIntoViewIfNeeded();
  await page.screenshot({ path: 'artifacts/desktop-rental-viewport.png', fullPage: false });
  await page.screenshot({ path: 'artifacts/desktop-rental-context.png', fullPage: true });
  await page.locator('#rental-back-link').click();
  await expect(page).toHaveURL(/#trip\//);
  await expect(page.locator('#itinerary-workspace')).toBeVisible();

  page.once('dialog', dialog => dialog.accept());
  await page.locator('.itinerary-item-card--stop').filter({ hasText: 'Điểm A' }).locator('.text-action--danger').click();
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
  await expect(card.locator('.itinerary-item-card__media')).toBeVisible();
  await expect(card.getByRole('button', { name: 'Di chuyển lên' })).toBeDisabled();
  await expect(card.getByRole('button', { name: 'Di chuyển xuống' })).toBeEnabled();
  await expect(card.getByRole('link', { name: 'Mở mục thuê đồ cho Điểm dừng mobile' })).toBeVisible();
  await expect(card.locator('.item-directions')).toHaveAttribute('href', /google\.com\/maps\/dir/);
  await expect(page.locator('.route-connector').first()).toContainText('Chưa có dữ liệu tuyến đường');
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
  await expect(page).toHaveURL(/#trip\//);
  await expect(page.locator('#itinerary-workspace')).toBeVisible();

  await page.goto(BASE_URL + '/#shop');
  await expect(page.locator('#rental-workspace')).toBeVisible();
  await expect(page.locator('#rental-workspace-title')).toHaveText('Thuê đồ cho điểm dừng đã chọn');
  await expect(page.locator('#rental-back-link')).toHaveAttribute('href', '#trips');
  assertNoUnexpectedBrowserErrors(browserErrors);
});
