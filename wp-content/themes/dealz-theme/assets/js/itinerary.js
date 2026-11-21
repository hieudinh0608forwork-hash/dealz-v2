(function () {
	'use strict';

	const config = window.dealzApp || {};
	const workspace = document.getElementById('itinerary-workspace');
	const tripList = document.getElementById('trip-list');
	const sectionHeading = document.querySelector('.trips-section .section-heading');
	const createTripPanel = document.getElementById('create-trip');
	const versionsList = document.getElementById('itinerary-versions');
	const editor = document.getElementById('itinerary-editor');
	const daysList = document.getElementById('itinerary-days');
	const itemsPanel = document.getElementById('day-items-panel');
	const itemsList = document.getElementById('day-items-list');
	const dayForm = document.getElementById('create-day-form');
	const itemForm = document.getElementById('create-item-form');
	const feedback = document.getElementById('workspace-feedback');

	if (!workspace || !config.loggedIn) {
		return;
	}

	let activeTrip = null;
	let activeItinerary = null;
	let activeDay = null;
	let requestSequence = 0;

	const baseUrl = String(config.restUrl || '').replace(/\/?$/, '/');

	async function api(path, method, payload) {
		const options = {
			method: method || 'GET',
			credentials: 'same-origin',
			headers: { 'X-WP-Nonce': config.nonce || '' }
		};
		if (typeof payload !== 'undefined') {
			options.headers['Content-Type'] = 'application/json';
			options.body = JSON.stringify(payload);
		}

		const response = await fetch(baseUrl + path.replace(/^\/+/, ''), options);
		let data = null;
		try {
			data = await response.json();
		} catch (error) {
			data = null;
		}
		if (!response.ok) {
			throw new Error((data && data.message) || 'Không thể thực hiện thao tác. Vui lòng thử lại.');
		}
		return data;
	}

	function showFeedback(message, isError) {
		if (!feedback) return;
		feedback.hidden = false;
		feedback.textContent = message;
		feedback.classList.toggle('notice--error', Boolean(isError));
	}

	function clearFeedback() {
		if (!feedback) return;
		feedback.hidden = true;
		feedback.textContent = '';
		feedback.classList.remove('notice--error');
	}

	function showWorkspace() {
		workspace.hidden = false;
		if (tripList) tripList.hidden = true;
		if (sectionHeading) sectionHeading.hidden = true;
		if (createTripPanel) createTripPanel.hidden = true;
	}

	function hideWorkspace() {
		workspace.hidden = true;
		if (tripList) tripList.hidden = false;
		if (sectionHeading) sectionHeading.hidden = false;
		if (createTripPanel) createTripPanel.hidden = false;
		activeTrip = null;
		activeItinerary = null;
		activeDay = null;
	}

	function formatDate(value) {
		if (!value) return '';
		const parts = value.split('-');
		return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : value;
	}

	function makeEmpty(message) {
		const empty = document.createElement('div');
		empty.className = 'workspace-empty';
		const text = document.createElement('p');
		text.textContent = message;
		empty.append(text);
		return empty;
	}

	async function openTrip(publicId) {
		if (!publicId) return;
		if (window.location.hash !== '#trip/' + publicId) {
			window.location.hash = 'trip/' + publicId;
			return;
		}
		await loadTrip(publicId);
	}

	async function loadTrip(publicId) {
		const sequence = ++requestSequence;
		showWorkspace();
		clearFeedback();
		versionsList.replaceChildren(makeEmpty('Đang tải các bản lịch trình…'));
		editor.hidden = true;
		itemsPanel.hidden = true;
		activeTrip = publicId;
		activeItinerary = null;
		activeDay = null;

		try {
			const trip = await api('trips/' + encodeURIComponent(publicId));
			if (sequence !== requestSequence) return;
			document.getElementById('itinerary-workspace-title').textContent = trip.title || 'Chuyến đi';
			await loadItineraries();
		} catch (error) {
			showFeedback(error.message, true);
			versionsList.replaceChildren(makeEmpty('Không tải được chuyến đi này.'));
		}
	}

	async function loadItineraries(selectId) {
		if (!activeTrip) return;
		versionsList.replaceChildren(makeEmpty('Đang tải…'));
		try {
			const itineraries = await api('trips/' + encodeURIComponent(activeTrip) + '/itineraries');
			versionsList.replaceChildren();

			if (!Array.isArray(itineraries) || itineraries.length === 0) {
				versionsList.append(makeEmpty('Chưa có lịch trình. Tạo một bản thủ công để thêm ngày và điểm dừng.'));
				editor.hidden = true;
				itemsPanel.hidden = true;
				return;
			}

			itineraries.forEach(function (itinerary) {
				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'version-chip';
				button.dataset.itineraryId = itinerary.public_id;
				button.textContent = 'Bản ' + itinerary.version + ' · ' + (itinerary.source === 'manual' ? 'Thủ công' : itinerary.source);
				button.setAttribute('aria-pressed', String(itinerary.public_id === (selectId || itineraries[0].public_id)));
				button.addEventListener('click', function () {
					selectItinerary(itinerary.public_id);
				});
				versionsList.append(button);
			});

			await selectItinerary(selectId || itineraries[0].public_id);
		} catch (error) {
			showFeedback(error.message, true);
			versionsList.replaceChildren(makeEmpty('Không tải được lịch trình.'));
		}
	}

	async function selectItinerary(publicId) {
		activeItinerary = publicId;
		activeDay = null;
		editor.hidden = false;
		itemsPanel.hidden = true;
		versionsList.querySelectorAll('.version-chip').forEach(function (button) {
			button.setAttribute('aria-pressed', String(button.dataset.itineraryId === publicId));
		});
		await loadDays();
	}

	async function loadDays(preferredDayId) {
		if (!activeItinerary) return;
		daysList.replaceChildren(makeEmpty('Đang tải ngày…'));
		itemsPanel.hidden = true;
		try {
			const days = await api('itineraries/' + encodeURIComponent(activeItinerary) + '/days');
			daysList.replaceChildren();

			if (!Array.isArray(days) || days.length === 0) {
				daysList.append(makeEmpty('Chưa có ngày nào. Thêm ngày nằm trong khoảng thời gian chuyến đi.'));
				return;
			}

			days.forEach(function (day, index) {
				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'day-chip';
				button.textContent = 'Ngày ' + (index + 1) + ' · ' + formatDate(day.local_date);
				button.dataset.dayId = day.public_id;
				button.setAttribute('aria-pressed', 'false');
				button.addEventListener('click', function () {
					selectDay(day.public_id, day.local_date);
				});
				daysList.append(button);
			});

			const target = days.find(function (day) { return day.public_id === preferredDayId; }) || days[0];
			await selectDay(target.public_id, target.local_date);
		} catch (error) {
			showFeedback(error.message, true);
			daysList.replaceChildren(makeEmpty('Không tải được danh sách ngày.'));
		}
	}

	async function selectDay(publicId, date) {
		activeDay = publicId;
		daysList.querySelectorAll('.day-chip').forEach(function (button) {
			button.setAttribute('aria-pressed', String(button.dataset.dayId === publicId));
		});
		document.getElementById('day-items-title').textContent = 'Điểm dừng · ' + formatDate(date);
		itemsPanel.hidden = false;
		await loadItems();
	}


	async function validateDay() {
		if (!activeDay) return;
		const results = document.getElementById('validation-results');
		const button = document.getElementById('validate-day-btn');
		results.hidden = false;
		results.replaceChildren();
		results.textContent = 'Đang kiểm tra…';
		button.disabled = true;

		try {
			const result = await api('days/' + encodeURIComponent(activeDay) + '/validation');
			results.replaceChildren();
			const summary = document.createElement('p');
			summary.className = result.is_valid ? 'validation-results__success' : 'validation-results__warning';
			summary.textContent = result.is_valid
				? 'Chưa phát hiện chồng lấn trong các khung giờ đã nhập.'
				: 'Có ' + result.issues.length + ' vấn đề về khung giờ cần xem lại.';
			results.append(summary);

			(result.issues || []).forEach(function (issue) {
				const row = document.createElement('p');
				row.className = 'validation-results__issue';
				row.textContent = issue.message;
				results.append(row);
			});

			(result.limitations || []).forEach(function (limitation) {
				const row = document.createElement('p');
				row.className = 'validation-results__note';
				row.textContent = limitation;
				results.append(row);
			});
		} catch (error) {
			results.textContent = error.message || 'Không thể kiểm tra lịch trình.';
			results.classList.add('validation-results--error');
		} finally {
			button.disabled = false;
		}
	}

	function itemTypeLabel(type) {
		if (type === 'lunch') return 'Bữa trưa';
		if (type === 'dinner') return 'Bữa tối';
		return 'Điểm dừng chính';
	}

	function makeItemCard(item) {
		const card = document.createElement('article');
		card.className = 'itinerary-item-card' + (item.item_type === 'stop' ? '' : ' itinerary-item-card--meal');

		const top = document.createElement('div');
		top.className = 'itinerary-item-card__top';
		const type = document.createElement('span');
		type.className = 'item-type-chip';
		type.textContent = itemTypeLabel(item.item_type);
		const time = document.createElement('span');
		time.className = 'item-time';
		time.textContent = item.start_local ? item.start_local.slice(0, 5) : 'Chưa có giờ';
		top.append(type, time);

		const title = document.createElement('h4');
		title.textContent = item.title_snapshot;
		const details = document.createElement('p');
		details.className = 'itinerary-item-card__details';
		const detailParts = [];
		if (item.duration_minutes) detailParts.push(item.duration_minutes + ' phút');
		if (item.address_snapshot) detailParts.push(item.address_snapshot);
		details.textContent = detailParts.length ? detailParts.join(' · ') : 'Chưa có thời lượng hoặc địa chỉ.';

		const editForm = document.createElement('form');
		editForm.className = 'item-edit-form';
		const editInput = document.createElement('input');
		editInput.type = 'text';
		editInput.value = item.title_snapshot;
		editInput.maxLength = 190;
		editInput.setAttribute('aria-label', 'Tên điểm dừng');
		const save = document.createElement('button');
		save.type = 'submit';
		save.className = 'text-action';
		save.textContent = 'Lưu tên';
		editForm.append(editInput, save);
		editForm.addEventListener('submit', async function (event) {
			event.preventDefault();
			const titleValue = editInput.value.trim();
			if (!titleValue) {
				showFeedback('Tên điểm dừng không được để trống.', true);
				return;
			}
			try {
				await api('items/' + encodeURIComponent(item.public_id), 'PATCH', { title_snapshot: titleValue });
				showFeedback('Đã cập nhật điểm dừng.', false);
				await loadItems();
			} catch (error) {
				showFeedback(error.message, true);
			}
		});

		const archive = document.createElement('button');
		archive.type = 'button';
		archive.className = 'text-action text-action--danger';
		archive.textContent = 'Lưu trữ';
		archive.addEventListener('click', async function () {
			if (!window.confirm('Lưu trữ điểm dừng này?')) return;
			try {
				await api('items/' + encodeURIComponent(item.public_id), 'DELETE');
				showFeedback('Đã lưu trữ điểm dừng.', false);
				await loadItems();
			} catch (error) {
				showFeedback(error.message, true);
			}
		});

		const actions = document.createElement('div');
		actions.className = 'item-card-actions';
		actions.append(editForm, archive);
		card.append(top, title, details, actions);
		return card;
	}

	async function loadItems() {
		if (!activeDay) return;
		itemsList.replaceChildren(makeEmpty('Đang tải điểm dừng…'));
		try {
			const items = await api('days/' + encodeURIComponent(activeDay) + '/items');
			itemsList.replaceChildren();
			if (!Array.isArray(items) || items.length === 0) {
				itemsList.append(makeEmpty('Ngày này chưa có điểm dừng. Thêm điểm chính hoặc thẻ bữa ăn bên dưới.'));
				return;
			}
			items.forEach(function (item) {
				itemsList.append(makeItemCard(item));
			});
		} catch (error) {
			showFeedback(error.message, true);
			itemsList.replaceChildren(makeEmpty('Không tải được điểm dừng.'));
		}
	}

	document.getElementById('create-itinerary-btn').addEventListener('click', async function () {
		if (!activeTrip) return;
		const button = this;
		button.disabled = true;
		button.textContent = 'Đang tạo…';
		try {
			const itinerary = await api('trips/' + encodeURIComponent(activeTrip) + '/itineraries', 'POST');
			showFeedback('Đã tạo lịch trình thủ công.', false);
			await loadItineraries(itinerary.public_id);
		} catch (error) {
			showFeedback(error.message, true);
		} finally {
			button.disabled = false;
			button.textContent = 'Tạo lịch trình';
		}
	});

	const validateButton = document.getElementById('validate-day-btn');
	if (validateButton) validateButton.addEventListener('click', validateDay);

	if (dayForm) {
		dayForm.addEventListener('submit', async function (event) {
			event.preventDefault();
			if (!activeItinerary || !dayForm.reportValidity()) return;
			const button = dayForm.querySelector('button[type="submit"]');
			button.disabled = true;
			try {
				const date = document.getElementById('itinerary-day-date').value;
				const day = await api('itineraries/' + encodeURIComponent(activeItinerary) + '/days', 'POST', { local_date: date });
				dayForm.reset();
				showFeedback('Đã thêm ngày vào lịch trình.', false);
				await loadDays(day.public_id);
			} catch (error) {
				showFeedback(error.message, true);
			} finally {
				button.disabled = false;
			}
		});
	}

	if (itemForm) {
		itemForm.addEventListener('submit', async function (event) {
			event.preventDefault();
			if (!activeDay || !itemForm.reportValidity()) return;
			const values = new FormData(itemForm);
			const duration = String(values.get('duration_minutes') || '');
			const start = String(values.get('start_local') || '');
			const payload = {
				title_snapshot: String(values.get('title_snapshot') || '').trim(),
				item_type: String(values.get('item_type') || 'stop'),
				address_snapshot: String(values.get('address_snapshot') || '').trim(),
				start_local: start || null,
				duration_minutes: duration === '' ? null : Number(duration),
				currency: 'VND',
				data_status: 'unverified'
			};
			const button = document.getElementById('create-item-submit');
			button.disabled = true;
			button.textContent = 'Đang thêm…';
			try {
				await api('days/' + encodeURIComponent(activeDay) + '/items', 'POST', payload);
				itemForm.reset();
				showFeedback('Đã thêm nội dung vào ngày.', false);
				await loadItems();
			} catch (error) {
				showFeedback(error.message, true);
			} finally {
				button.disabled = false;
				button.textContent = 'Thêm vào ngày này';
			}
		});
	}

	if (tripList) {
		tripList.addEventListener('click', function (event) {
			const button = event.target.closest('[data-open-trip]');
			if (button) openTrip(button.dataset.openTrip);
		});
	}

	window.addEventListener('hashchange', function () {
		const match = window.location.hash.match(/^#trip\/([a-f0-9-]{36})$/i);
		if (match) {
			loadTrip(match[1]);
		} else {
			hideWorkspace();
		}
	});

	window.dealzItinerary = { openTrip: openTrip };

	const initialMatch = window.location.hash.match(/^#trip\/([a-f0-9-]{36})$/i);
	if (initialMatch) {
		loadTrip(initialMatch[1]);
	}
})();
