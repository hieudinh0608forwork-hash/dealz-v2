(function () {
	'use strict';

	const config = window.dealzApp || {};
	const list = document.getElementById('trip-list');
	const form = document.getElementById('create-trip-form');
	const feedback = document.getElementById('trip-feedback');
	const submitButton = document.getElementById('create-trip-submit');

	if (!list || !config.loggedIn) {
		return;
	}

	const endpoint = String(config.restUrl || '').replace(/\/?$/, '/');

	function showFeedback(message, isError) {
		if (!feedback) return;
		feedback.hidden = false;
		feedback.textContent = message;
		feedback.classList.toggle('notice--error', Boolean(isError));
	}

	function formatDate(value) {
		if (!value) return 'Chưa xác định ngày';
		const parts = value.split('-');
		if (parts.length !== 3) return value;
		return parts[2] + '/' + parts[1] + '/' + parts[0];
	}

	function makeTripCard(trip) {
		const card = document.createElement('article');
		card.className = 'trip-card';

		const top = document.createElement('div');
		top.className = 'trip-card__top';
		const icon = document.createElement('span');
		icon.className = 'trip-card__icon';
		icon.setAttribute('aria-hidden', 'true');
		icon.textContent = '⌁';
		const status = document.createElement('span');
		status.className = 'status-chip';
		status.textContent = trip.status === 'draft' ? 'Bản nháp' : trip.status;
		top.append(icon, status);

		const title = document.createElement('h3');
		title.textContent = trip.title || 'Chuyến đi chưa đặt tên';
		const destination = document.createElement('p');
		destination.className = 'trip-card__destination';
		destination.textContent = trip.destination_name || 'Chưa thêm điểm đến';

		const meta = document.createElement('div');
		meta.className = 'trip-card__meta';
		const dates = document.createElement('span');
		dates.textContent = formatDate(trip.start_date) + ' – ' + formatDate(trip.end_date);
		const travelers = document.createElement('span');
		travelers.textContent = String(trip.traveler_count || 1) + ' người';
		meta.append(dates, travelers);

		const action = document.createElement('a');
		action.className = 'trip-card__action';
		action.href = '#create-trip';
		action.textContent = 'Cập nhật thông tin →';

		card.append(top, title, destination, meta, action);
		return card;
	}

	function renderEmpty() {
		const empty = document.createElement('div');
		empty.className = 'empty-state';
		const symbol = document.createElement('div');
		symbol.className = 'empty-state__symbol';
		symbol.setAttribute('aria-hidden', 'true');
		symbol.textContent = '✦';
		const heading = document.createElement('h3');
		heading.textContent = 'Bạn chưa có chuyến đi nào';
		const message = document.createElement('p');
		message.textContent = 'Tạo chuyến đi đầu tiên để lưu điểm xuất phát, điểm đến và thời gian dự kiến.';
		const action = document.createElement('a');
		action.className = 'button button--primary';
		action.href = '#create-trip';
		action.textContent = 'Tạo chuyến đi';
		empty.append(symbol, heading, message, action);
		list.replaceChildren(empty);
	}

	async function loadTrips() {
		list.setAttribute('aria-busy', 'true');
		try {
			const response = await fetch(endpoint + 'trips', {
				method: 'GET',
				credentials: 'same-origin',
				headers: { 'X-WP-Nonce': config.nonce || '' }
			});
			const data = await response.json();
			if (!response.ok) {
				throw new Error(data.message || 'Không thể tải hành trình.');
			}
			if (!Array.isArray(data) || data.length === 0) {
				renderEmpty();
				return;
			}
			const cards = data.map(makeTripCard);
			list.replaceChildren.apply(list, cards);
		} catch (error) {
			const empty = document.createElement('div');
			empty.className = 'empty-state';
			const heading = document.createElement('h3');
			heading.textContent = 'Chưa tải được hành trình';
			const message = document.createElement('p');
			message.textContent = error.message || 'Đã có lỗi xảy ra.';
			const retry = document.createElement('button');
			retry.className = 'button button--primary';
			retry.type = 'button';
			retry.textContent = 'Thử lại';
			retry.addEventListener('click', loadTrips);
			empty.append(heading, message, retry);
			list.replaceChildren(empty);
		} finally {
			list.removeAttribute('aria-busy');
		}
	}

	if (form) {
		form.addEventListener('submit', async function (event) {
			event.preventDefault();
			if (!form.reportValidity()) return;

			const values = new FormData(form);
			const payload = {
				title: String(values.get('title') || '').trim(),
				origin_text: String(values.get('origin_text') || '').trim(),
				destination_name: String(values.get('destination_name') || '').trim(),
				start_date: String(values.get('start_date') || '') || null,
				end_date: String(values.get('end_date') || '') || null,
				traveler_count: Number(values.get('traveler_count') || 1),
				budget_amount_minor: String(values.get('budget_amount_minor') || '') === '' ? null : Number(values.get('budget_amount_minor')),
				currency: 'VND',
				destination_timezone: 'Asia/Ho_Chi_Minh'
			};

			if (payload.start_date && payload.end_date && payload.end_date < payload.start_date) {
				showFeedback('Ngày kết thúc không được trước ngày bắt đầu.', true);
				return;
			}

			submitButton.disabled = true;
			submitButton.textContent = 'Đang lưu…';
			showFeedback('Đang lưu chuyến đi của bạn.', false);

			try {
				const response = await fetch(endpoint + 'trips', {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': config.nonce || ''
					},
					body: JSON.stringify(payload)
				});
				const data = await response.json();
				if (!response.ok) {
					throw new Error(data.message || 'Không thể lưu chuyến đi.');
				}
				form.reset();
				const travelerInput = document.getElementById('trip-travelers');
				if (travelerInput) travelerInput.value = '1';
				showFeedback('Đã lưu chuyến đi. Bạn có thể tiếp tục cập nhật thông tin.', false);
				await loadTrips();
				document.getElementById('trips').scrollIntoView({ behavior: 'smooth', block: 'start' });
			} catch (error) {
				showFeedback(error.message || 'Đã có lỗi xảy ra. Vui lòng thử lại.', true);
			} finally {
				submitButton.disabled = false;
				submitButton.textContent = 'Lưu chuyến đi';
			}
		});
	}

	loadTrips();
})();
