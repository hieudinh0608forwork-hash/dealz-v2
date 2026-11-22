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
	const itemFormPanel = document.getElementById('create-item-panel');
	const stopAnchorList = document.getElementById('stop-anchor-list');
	const feedback = document.getElementById('workspace-feedback');
	const financeFeedback = document.getElementById('finance-feedback');
	const budgetSummary = document.getElementById('budget-summary');
	const budgetLinesList = document.getElementById('budget-lines-list');
	const expensesList = document.getElementById('expenses-list');
	const budgetForm = document.getElementById('create-budget-form');
	const expenseForm = document.getElementById('create-expense-form');

	if (!workspace || !config.loggedIn) {
		return;
	}

	let activeTrip = null;
	let activeTripData = null;
	let activeItinerary = null;
	let activeDay = null;
	let activeDayNumber = null;
	let activeDays = [];
	const dayItemsCache = new Map();
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
		document.body.classList.add('is-trip-view');
		workspace.hidden = false;
		if (tripList) tripList.hidden = true;
		if (sectionHeading) sectionHeading.hidden = true;
		if (createTripPanel) createTripPanel.hidden = true;
	}

	function hideWorkspace() {
		document.body.classList.remove('is-trip-view');
		workspace.hidden = true;
		const rentalWorkspace = document.getElementById('rental-workspace');
		if (rentalWorkspace) rentalWorkspace.hidden = true;
		document.body.classList.remove('is-rental-view');
		if (tripList) tripList.hidden = false;
		if (sectionHeading) sectionHeading.hidden = false;
		if (createTripPanel) createTripPanel.hidden = false;
		activeTrip = null;
		activeTripData = null;
		activeDays = [];
		dayItemsCache.clear();
		updateBadgeDisplay(0);
		activeItinerary = null;
		activeDay = null;
	}

	function updateBadgeDisplay(count) {
		const badge = document.getElementById('mobile-trip-badge');
		if (!badge) return;
		const safeCount = Number.isFinite(count) ? Math.max(0, count) : 0;
		badge.textContent = String(safeCount);
		badge.hidden = safeCount === 0;
		badge.setAttribute('aria-label', safeCount + ' điểm dừng chưa lưu trữ');
	}

	async function updateTripStopBadge(days) {
		if (!activeTrip || !Array.isArray(days) || days.length === 0) {
			updateBadgeDisplay(0);
			return;
		}
		try {
			const tripAtRequestStart = activeTrip;
			const missingDays = days.filter(function (day) {
				return !dayItemsCache.has(day.public_id);
			});
			const itemLists = await Promise.all(missingDays.map(function (day) {
				return api('days/' + encodeURIComponent(day.public_id) + '/items');
			}));
			if (tripAtRequestStart !== activeTrip) return;
			missingDays.forEach(function (day, index) {
				dayItemsCache.set(day.public_id, itemLists[index]);
			});
			let count = 0;
			days.forEach(function (day) {
				const items = dayItemsCache.get(day.public_id);
				if (!Array.isArray(items)) return;
				count += items.filter(function (item) {
					return item.item_type === 'stop' && item.status !== 'archived';
				}).length;
			});
			updateBadgeDisplay(count);
		} catch (error) {
			// Keep the last confirmed count; a transient read failure must not invent a new count.
		}
	}
	function formatDate(value) {
		if (!value) return '';
		const parts = value.split('-');
		return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : value;
	}

	function renderTripMeta() {
		const meta = document.getElementById('itinerary-workspace-meta');
		if (!meta || !activeTripData) return;
		const parts = [];
		if (activeTripData.start_date && activeTripData.end_date) {
			parts.push(formatDate(activeTripData.start_date) + ' – ' + formatDate(activeTripData.end_date));
		}
		if (activeDays.length > 0) {
			parts.push(activeDays.length + ' ngày');
		}
		meta.textContent = parts.join(' · ');
		meta.hidden = parts.length === 0;
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
		activeDays = [];
		dayItemsCache.clear();
		updateBadgeDisplay(0);
		activeItinerary = null;
		activeDay = null;

		try {
			const trip = await api('trips/' + encodeURIComponent(publicId));
			if (sequence !== requestSequence) return;
			activeTripData = trip;
			renderTripMeta();
			const dayDateInput = document.getElementById('itinerary-day-date');
			if (dayDateInput) {
				dayDateInput.min = trip.start_date || '';
				dayDateInput.max = trip.end_date || '';
				if ( ! dayDateInput.value && trip.start_date ) {
					dayDateInput.value = trip.start_date;
				}
			}
			const pilotWarning = document.getElementById('pilot-warning');
			if (pilotWarning) {
				const destination = String(trip.destination_name || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
				pilotWarning.hidden = !destination || destination.includes('da nang') || destination.includes('hoi an');
			}
			document.getElementById('itinerary-workspace-title').textContent = trip.title || 'Chuyến đi';
			await loadItineraries();
			await loadFinance();
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

			const createItineraryButton = document.getElementById('create-itinerary-btn');
			if (createItineraryButton) {
				createItineraryButton.classList.toggle('button--primary', !Array.isArray(itineraries) || itineraries.length === 0);
				createItineraryButton.classList.toggle('button--secondary', Array.isArray(itineraries) && itineraries.length > 0);
			}
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
		activeDays = [];
		updateBadgeDisplay(0);
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
			if (itemFormPanel) itemFormPanel.hidden = true;
		try {
			const days = await api('itineraries/' + encodeURIComponent(activeItinerary) + '/days');
			activeDays = Array.isArray(days) ? days : [];
			if (activeDays.length === 0) updateBadgeDisplay(0);
			renderTripMeta();
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
				button.setAttribute('aria-label', 'Chọn ngày ' + (index + 1) + ', ' + formatDate(day.local_date));
				button.dataset.dayId = day.public_id;
				button.setAttribute('aria-pressed', 'false');
				button.addEventListener('click', function () {
					selectDay(day.public_id, day.local_date, index + 1);
				});
				daysList.append(button);
			});

			const daySubmit = document.querySelector('#create-day-form button[type="submit"]');
			if (daySubmit) {
				daySubmit.classList.toggle('button--primary', days.length === 0);
				daySubmit.classList.toggle('button--secondary', days.length > 0);
			}
			const target = days.find(function (day) { return day.public_id === preferredDayId; }) || days[0];
			const targetIndex = days.findIndex(function (day) { return day.public_id === target.public_id; });
			await selectDay(target.public_id, target.local_date, targetIndex + 1);
		} catch (error) {
			showFeedback(error.message, true);
			daysList.replaceChildren(makeEmpty('Không tải được danh sách ngày.'));
		}
	}

	async function selectDay(publicId, date, dayNumber) {
		activeDay = publicId;
		activeDayNumber = dayNumber || null;
		const validationResults = document.getElementById('validation-results');
		if (validationResults) { validationResults.hidden = true; validationResults.classList.remove('validation-results--error'); }
		daysList.querySelectorAll('.day-chip').forEach(function (button) {
			button.setAttribute('aria-pressed', String(button.dataset.dayId === publicId));
		});
		document.getElementById('day-items-title').textContent = 'Điểm dừng · ' + formatDate(date);
		itemsPanel.hidden = false;
		if (itemFormPanel) itemFormPanel.hidden = false;
		await loadItems();
	}


	async function validateDay() {
		if (!activeDay) return;
		const results = document.getElementById('validation-results');
		const button = document.getElementById('validate-day-btn');
		results.hidden = false;
		results.classList.remove('validation-results--error');
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

	function makeItemCard(item, itemIndex, allItems) {
		const isMainStop = item.item_type === 'stop';
		const card = document.createElement('article');
		card.className = 'itinerary-item-card ' + (isMainStop ? 'itinerary-item-card--stop' : 'itinerary-item-card--meal');
		card.id = 'itinerary-item-' + item.public_id;

		if (isMainStop) {
			const media = document.createElement('div');
			media.className = 'itinerary-item-card__media';
			media.setAttribute('role', 'img');
			media.setAttribute('aria-label', 'Chưa có ảnh đã xác minh cho ' + item.title_snapshot);
			const mediaLabel = document.createElement('span');
			mediaLabel.className = 'itinerary-item-card__media-label';
			mediaLabel.textContent = item.title_snapshot + ' · Chưa có ảnh đã xác minh';
			media.append(mediaLabel);
			card.append(media);
		}

		const top = document.createElement('div');
		top.className = 'itinerary-item-card__top';
		const type = document.createElement('span');
		type.className = 'item-type-chip';
		type.textContent = itemTypeLabel(item.item_type);
		const time = document.createElement('span');
		time.className = 'item-time';
		time.textContent = item.start_local
			? item.start_local.slice(0, 5) + (item.end_local ? ' – ' + item.end_local.slice(0, 5) : '')
			: 'Chưa đặt giờ';
		top.append(type, time);

		const title = document.createElement('h3');
		title.textContent = item.title_snapshot;
		const details = document.createElement('p');
		details.className = 'itinerary-item-card__details';
		const detailParts = [];
		if (item.duration_minutes) detailParts.push(item.duration_minutes + ' phút');
		if (item.address_snapshot) detailParts.push(item.address_snapshot);
		details.textContent = detailParts.length ? detailParts.join(' · ') : 'Chưa có thời lượng hoặc địa chỉ.';
		const photoHint = document.createElement('p');
		photoHint.className = 'itinerary-item-card__photo-hint';
		photoHint.textContent = 'Góc chụp · Gợi ý chung: ưu tiên toàn cảnh, ánh sáng tự nhiên và giữ chủ thể chính rõ ràng.';
		const gearHint = document.createElement('p');
		gearHint.className = 'itinerary-item-card__gear-hint';
		gearHint.textContent = 'Đồ gợi ý · Chưa có dữ liệu sản phẩm được xác minh.';

		const editDetails = document.createElement('details');
		editDetails.className = 'item-edit-details';
		const editSummary = document.createElement('summary');
		editSummary.textContent = 'Chỉnh sửa thông tin';
		const editForm = document.createElement('form');
		editForm.className = 'item-edit-form';
		const editInput = document.createElement('input');
		editInput.type = 'text';
		editInput.value = item.title_snapshot;
		editInput.maxLength = 190;
		editInput.required = true;
		editInput.setAttribute('aria-label', 'Tên điểm dừng');

		const startInput = document.createElement('input');
		startInput.type = 'time';
		startInput.value = item.start_local ? item.start_local.slice(0, 5) : '';
		startInput.setAttribute('aria-label', 'Giờ đến');

		const endInput = document.createElement('input');
		endInput.type = 'time';
		endInput.value = item.end_local ? item.end_local.slice(0, 5) : '';
		endInput.setAttribute('aria-label', 'Giờ kết thúc');

		const durationInput = document.createElement('input');
		durationInput.type = 'number';
		durationInput.min = '1';
		durationInput.max = '1440';
		durationInput.step = '1';
		durationInput.placeholder = 'Phút';
		durationInput.value = item.duration_minutes || '';
		durationInput.setAttribute('aria-label', 'Thời lượng gợi ý, phút');

		const save = document.createElement('button');
		save.type = 'submit';
		save.className = 'text-action';
		save.textContent = 'Lưu thay đổi';
		function addEditField(labelText, input) {
			const label = document.createElement('label');
			label.className = 'item-edit-field';
			const caption = document.createElement('span');
			caption.textContent = labelText;
			label.append(caption, input);
			editForm.append(label);
		}
		addEditField('Tên điểm dừng', editInput);
		addEditField('Giờ đến', startInput);
		addEditField('Giờ kết thúc', endInput);
		addEditField('Thời lượng (phút)', durationInput);
		save.className = 'button button--secondary item-edit-save';
		editForm.append(save);
		editDetails.append(editSummary, editForm);
		editForm.addEventListener('submit', async function (event) {
			event.preventDefault();
			const titleValue = editInput.value.trim();
			if (!titleValue) {
				showFeedback('Tên điểm dừng không được để trống.', true);
				editInput.focus();
				return;
			}
			const durationValue = durationInput.value.trim();
			save.disabled = true;
			save.textContent = 'Đang lưu…';
			try {
				await api('items/' + encodeURIComponent(item.public_id), 'PATCH', {
					title_snapshot: titleValue,
					start_local: startInput.value || null,
					end_local: endInput.value || null,
					duration_minutes: durationValue === '' ? null : Number(durationValue)
				});
				showFeedback('Đã cập nhật thông tin điểm dừng.', false);
				await loadItems();
			} catch (error) {
				showFeedback(error.message, true);
			} finally {
				save.disabled = false;
				save.textContent = 'Lưu thay đổi';
			}
		});

		const actions = document.createElement('div');
		actions.className = 'item-card-actions';

		const moveControls = document.createElement('div');
		moveControls.className = 'item-move-controls';
		const moveUp = document.createElement('button');
		moveUp.type = 'button';
		moveUp.className = 'button button--icon';
		moveUp.textContent = '↑';
		moveUp.setAttribute('aria-label', 'Di chuyển lên');
		moveUp.disabled = itemIndex <= 0;
		const moveDown = document.createElement('button');
		moveDown.type = 'button';
		moveDown.className = 'button button--icon';
		moveDown.textContent = '↓';
		moveDown.setAttribute('aria-label', 'Di chuyển xuống');
		moveDown.disabled = itemIndex >= allItems.length - 1;

		async function moveItem(direction) {
			const targetIndex = itemIndex + direction;
			if (targetIndex < 0 || targetIndex >= allItems.length) return;
			const orderedIds = allItems.map(function (entry) { return entry.public_id; });
			const currentId = orderedIds[itemIndex];
			orderedIds[itemIndex] = orderedIds[targetIndex];
			orderedIds[targetIndex] = currentId;
			moveUp.disabled = true;
			moveDown.disabled = true;
			try {
				await api('days/' + encodeURIComponent(activeDay) + '/reorder', 'POST', {
					item_public_ids: orderedIds
				});
				showFeedback('Đã sắp xếp lại lịch trình.', false);
				await loadItems();
			} catch (error) {
				showFeedback(error.message, true);
				moveUp.disabled = itemIndex <= 0;
				moveDown.disabled = itemIndex >= allItems.length - 1;
			}
		}
		moveUp.addEventListener('click', function () { moveItem(-1); });
		moveDown.addEventListener('click', function () { moveItem(1); });
		moveControls.append(moveUp, moveDown);

		if (isMainStop) {
			const rental = document.createElement('a');
			rental.className = 'button button--tonal item-rental-cta';
			rental.href = '#shop';
			rental.textContent = 'Thuê đồ cho điểm này';
			rental.setAttribute('aria-label', 'Mở mục thuê đồ cho ' + item.title_snapshot);
			rental.addEventListener('click', function (event) {
				event.preventDefault();
				const params = new URLSearchParams({
					stop: item.public_id,
					name: item.title_snapshot,
					return: window.location.hash,
					day: activeDayNumber ? 'Ngày ' + activeDayNumber : '',
					time: item.start_local ? item.start_local.slice(0, 5) : ''
				});
				window.location.hash = 'shop?' + params.toString();
			});
			actions.append(rental);
		}

		if (isMainStop && item.address_snapshot) {
			const directions = document.createElement('a');
			directions.className = 'button button--tertiary item-directions';
			directions.href = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(item.address_snapshot);
			directions.target = '_blank';
			directions.rel = 'noopener noreferrer';
			directions.textContent = 'Chỉ đường';
			directions.setAttribute('aria-label', 'Mở chỉ đường đến ' + item.title_snapshot + ' trên Google Maps');
			actions.append(moveControls, directions, editDetails);
		} else {
			actions.append(moveControls, editDetails);
		}

		const archive = document.createElement('button');
		archive.type = 'button';
		archive.className = 'text-action item-archive-action';
		archive.textContent = 'Lưu trữ';
		const archiveDialog = document.createElement('dialog');
		archiveDialog.className = 'archive-dialog';
		const archiveTitle = document.createElement('h3');
		archiveTitle.textContent = 'Lưu trữ nội dung này?';
		const archiveDescription = document.createElement('p');
		archiveDescription.textContent = 'Nội dung sẽ chuyển sang trạng thái lưu trữ và không còn xuất hiện trong danh sách đang hoạt động. Giao diện hiện không có thao tác khôi phục.';
		const archiveActions = document.createElement('div');
		archiveActions.className = 'archive-dialog__actions';
		const cancelArchive = document.createElement('button');
		cancelArchive.type = 'button';
		cancelArchive.className = 'button button--secondary';
		cancelArchive.textContent = 'Giữ lại';
		const confirmArchive = document.createElement('button');
		confirmArchive.type = 'button';
		confirmArchive.className = 'button button--tertiary';
		confirmArchive.textContent = 'Lưu trữ';
		cancelArchive.addEventListener('click', function () { archiveDialog.close(); });
		confirmArchive.addEventListener('click', async function () {
			confirmArchive.disabled = true;
			confirmArchive.textContent = 'Đang lưu trữ…';
			try {
				await api('items/' + encodeURIComponent(item.public_id), 'DELETE');
				archiveDialog.close();
				showFeedback('Đã lưu trữ nội dung.', false);
				await loadItems();
			} catch (error) {
				showFeedback(error.message, true);
				confirmArchive.disabled = false;
				confirmArchive.textContent = 'Lưu trữ';
			}
		});
		archiveActions.append(cancelArchive, confirmArchive);
		archiveDialog.append(archiveTitle, archiveDescription, archiveActions);
		archive.addEventListener('click', function () {
			document.body.append(archiveDialog);
			archiveDialog.addEventListener('close', function () { archiveDialog.remove(); }, { once: true });
			archiveDialog.showModal();
		});
		actions.append(archive);
		card.append(top, title, details);
		if (isMainStop) card.append(photoHint, gearHint);
		card.append(actions);
		return card;
	}

	function makeRoutePlaceholder() {
		const connector = document.createElement('div');
		connector.className = 'route-connector';
		connector.setAttribute('role', 'note');
		const icon = document.createElement('span');
		icon.className = 'route-connector__icon';
		icon.setAttribute('aria-hidden', 'true');
		icon.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M5 17h2l2-6h6l2 6h2M9 11l-1-3h5l2 3M5 17a2 2 0 1 0 4 0m7 0a2 2 0 1 0 4 0" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		const label = document.createElement('span');
		label.textContent = 'Chưa có đủ dữ liệu tuyến đường';
		connector.append(icon, label);
		return connector;
	}

	async function loadItems() {
		if (!activeDay) return;
		itemsList.setAttribute('aria-busy', 'true');
		itemsList.replaceChildren(makeEmpty('Đang tải điểm dừng…'));
		try {
			const items = await api('days/' + encodeURIComponent(activeDay) + '/items');
			dayItemsCache.set(activeDay, items);
			await updateTripStopBadge(activeDays);
			itemsList.replaceChildren();
			if (stopAnchorList) stopAnchorList.replaceChildren();
			const activeMainStops = Array.isArray(items) ? items.filter(function (item) { return item.item_type === 'stop' && item.status !== 'archived'; }).length : 0;
			const mainStopOption = document.querySelector('#item-type option[value="stop"]');
			if (mainStopOption) {
				mainStopOption.disabled = activeMainStops >= 4;
				mainStopOption.title = activeMainStops >= 4 ? 'Đã đủ 4 điểm dừng chính trong ngày.' : '';
			}
			if (activeMainStops >= 4) {
				const limitNotice = document.createElement('p');
				limitNotice.className = 'notice notice--warning';
				limitNotice.setAttribute('role', 'status');
				limitNotice.textContent = 'Ngày này đã đủ 4 điểm dừng chính. Bạn vẫn có thể thêm thẻ bữa trưa hoặc bữa tối.';
				itemsList.append(limitNotice);
			}
			if (!Array.isArray(items) || items.length === 0) {
				const empty = makeEmpty('Ngày này chưa có điểm dừng. Thêm điểm chính hoặc thẻ bữa ăn bên dưới.');
				const action = document.createElement('button');
				action.type = 'button';
				action.className = 'button button--secondary button--compact';
				action.textContent = 'Thêm nội dung đầu tiên';
				action.addEventListener('click', function () {
					itemForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
					const titleInput = document.getElementById('item-title');
					if (titleInput) titleInput.focus();
				});
				empty.append(action);
				itemsList.append(empty);
				return;
			}
			let previousMainStop = false;
			items.forEach(function (item, index) {
				if (item.item_type === 'stop' && previousMainStop) {
					itemsList.append(makeRoutePlaceholder());
				}
				const card = makeItemCard(item, index, items);
				itemsList.append(card);
				if (stopAnchorList && item.item_type === 'stop') {
					const anchor = document.createElement('button');
					anchor.type = 'button';
					anchor.className = 'stop-anchor-chip';
					anchor.textContent = item.title_snapshot;
					anchor.addEventListener('click', function () {
						card.scrollIntoView({ behavior: 'smooth', block: 'start' });
					});
					stopAnchorList.append(anchor);
				}
				if (item.item_type === 'stop') previousMainStop = true;
			});
		} catch (error) {
			showFeedback(error.message, true);
			const retry = makeEmpty(error.message || 'Không tải được điểm dừng.');
			const button = document.createElement('button');
			button.type = 'button';
			button.className = 'button button--secondary button--compact';
			button.textContent = 'Thử tải lại';
			button.addEventListener('click', loadItems);
			retry.append(button);
			itemsList.replaceChildren(retry);
		} finally {
			itemsList.removeAttribute('aria-busy');
		}
	}


	function showFinanceFeedback(message, isError) {
		if (!financeFeedback) return;
		financeFeedback.hidden = false;
		financeFeedback.textContent = message;
		financeFeedback.classList.toggle('notice--error', Boolean(isError));
	}

	function formatMoney(amount, currency) {
		if (amount === null || typeof amount === 'undefined') return 'Chưa có số tiền';
		try {
			return new Intl.NumberFormat('vi-VN', {
				style: 'currency',
				currency: currency || 'VND',
				maximumFractionDigits: 2
			}).format(Number(amount));
		} catch (error) {
			return Number(amount).toLocaleString('vi-VN') + ' ' + (currency || 'VND');
		}
	}

	function categoryLabel(category) {
		const labels = {
			transport: 'Di chuyển',
			accommodation: 'Lưu trú',
			food: 'Ăn uống',
			rental: 'Thuê đồ',
			activities: 'Hoạt động',
			other: 'Khác'
		};
		return labels[category] || category || 'Chưa phân nhóm';
	}

	function costTypeLabel(type) {
		const labels = {
			estimate: 'Ước tính',
			quoted: 'Đã báo giá',
			confirmed: 'Đã xác nhận',
			actual: 'Thực tế'
		};
		return labels[type] || type;
	}

	function makeFinanceEmpty(message) {
		const empty = document.createElement('div');
		empty.className = 'workspace-empty';
		const text = document.createElement('p');
		text.textContent = message;
		empty.append(text);
		return empty;
	}

	function makeFinanceRow(titleText, detailText, amountText, archiveCallback) {
		const row = document.createElement('article');
		row.className = 'finance-row';
		const main = document.createElement('div');
		main.className = 'finance-row__main';
		const title = document.createElement('strong');
		title.textContent = titleText;
		const detail = document.createElement('p');
		detail.textContent = detailText || '';
		main.append(title, detail);

		const side = document.createElement('div');
		side.className = 'finance-row__side';
		const amount = document.createElement('strong');
		amount.textContent = amountText;
		side.append(amount);

		if (archiveCallback) {
			const archive = document.createElement('button');
			archive.type = 'button';
			archive.className = 'text-action text-action--danger';
			archive.textContent = 'Lưu trữ';
			archive.addEventListener('click', archiveCallback);
			side.append(archive);
		}
		row.append(main, side);
		return row;
	}

	function formatExpenseDate(value) {
		if (!value) return '';
		const date = new Date(String(value).replace(' ', 'T') + 'Z');
		if (Number.isNaN(date.getTime())) return value + ' UTC';
		try {
			return date.toLocaleString('vi-VN', {
				timeZone: activeTripData && activeTripData.destination_timezone
					? activeTripData.destination_timezone
					: 'Asia/Ho_Chi_Minh'
			});
		} catch (error) {
			return value + ' UTC';
		}
	}

	function localDateTimeToIso(value, timeZone) {
		const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/);
		if (!match) throw new Error('Thời điểm chi không hợp lệ.');

		const parts = match.slice(1).map(Number);
		const localAsUtc = Date.UTC(parts[0], parts[1] - 1, parts[2], parts[3], parts[4]);
		const formatter = new Intl.DateTimeFormat('en-CA', {
			timeZone: timeZone || 'Asia/Ho_Chi_Minh',
			year: 'numeric',
			month: '2-digit',
			day: '2-digit',
			hour: '2-digit',
			minute: '2-digit',
			hourCycle: 'h23'
		});

		function offsetAt(timestamp) {
			const formatted = Object.fromEntries(
				formatter.formatToParts(new Date(timestamp)).map(function (part) {
					return [part.type, part.value];
				})
			);
			const representedUtc = Date.UTC(
				Number(formatted.year),
				Number(formatted.month) - 1,
				Number(formatted.day),
				Number(formatted.hour),
				Number(formatted.minute)
			);
			return Math.round((representedUtc - timestamp) / 60000);
		}

		let offset = offsetAt(localAsUtc);
		offset = offsetAt(localAsUtc - offset * 60000);
		const sign = offset >= 0 ? '+' : '-';
		const absolute = Math.abs(offset);
		const hours = String(Math.floor(absolute / 60)).padStart(2, '0');
		const minutes = String(absolute % 60).padStart(2, '0');
		return value + ':00' + sign + hours + ':' + minutes;
	}

	async function loadFinance() {
		if (!activeTrip || !budgetSummary || !budgetLinesList || !expensesList) return;
		budgetSummary.replaceChildren(makeFinanceEmpty('Đang tải tổng hợp ngân sách…'));
		budgetLinesList.replaceChildren(makeFinanceEmpty('Đang tải khoản dự kiến…'));
		expensesList.replaceChildren(makeFinanceEmpty('Đang tải khoản đã chi…'));

		try {
			const results = await Promise.all([
				api('trips/' + encodeURIComponent(activeTrip) + '/budget'),
				api('trips/' + encodeURIComponent(activeTrip) + '/expenses')
			]);
			const budget = results[0] || { lines: [], summary: [] };
			const expenses = results[1] || [];
			budgetSummary.replaceChildren();

			if (!Array.isArray(budget.summary) || budget.summary.length === 0) {
				budgetSummary.append(makeFinanceEmpty('Chưa có khoản ngân sách nào được ghi nhận.'));
			} else {
				budget.summary.forEach(function (group) {
					const tile = document.createElement('div');
					tile.className = 'budget-summary__tile';
					const label = document.createElement('span');
					label.textContent = costTypeLabel(group.cost_type) + ' · ' + group.currency;
					const total = document.createElement('strong');
					total.textContent = formatMoney(group.total_amount_minor, group.currency);
					tile.append(label, total);
					budgetSummary.append(tile);
				});
			}

			budgetLinesList.replaceChildren();
			if (!Array.isArray(budget.lines) || budget.lines.length === 0) {
				budgetLinesList.append(makeFinanceEmpty('Chưa có khoản dự kiến.'));
			} else {
				budget.lines.forEach(function (line) {
					const detail = [
						categoryLabel(line.category),
						line.description,
						'Số lượng: ' + line.quantity,
						costTypeLabel(line.cost_type)
					].filter(Boolean).join(' · ');
					budgetLinesList.append(makeFinanceRow(
						line.description || categoryLabel(line.category),
						detail,
						formatMoney(line.total_amount_minor, line.currency),
						async function () {
							if (!window.confirm('Lưu trữ khoản ngân sách này?')) return;
							try {
								await api('budget/lines/' + encodeURIComponent(line.public_id), 'DELETE');
								showFinanceFeedback('Đã lưu trữ khoản ngân sách.', false);
								await loadFinance();
							} catch (error) {
								showFinanceFeedback(error.message, true);
							}
						}
					));
				});
			}

			expensesList.replaceChildren();
			if (!Array.isArray(expenses) || expenses.length === 0) {
				expensesList.append(makeFinanceEmpty('Chưa ghi nhận khoản chi thực tế.'));
			} else {
				expenses.forEach(function (expense) {
					const detail = [
						categoryLabel(expense.category),
						formatExpenseDate(expense.occurred_at),
						expense.note
					].filter(Boolean).join(' · ');
					expensesList.append(makeFinanceRow(
						categoryLabel(expense.category),
						detail,
						formatMoney(expense.amount_minor, expense.currency),
						async function () {
							if (!window.confirm('Lưu trữ khoản chi này?')) return;
							try {
								await api('expenses/' + encodeURIComponent(expense.public_id), 'DELETE');
								showFinanceFeedback('Đã lưu trữ khoản chi.', false);
								await loadFinance();
							} catch (error) {
								showFinanceFeedback(error.message, true);
							}
						}
					));
				});
			}
		} catch (error) {
			showFinanceFeedback(error.message || 'Không thể tải dữ liệu tài chính.', true);
			budgetSummary.replaceChildren(makeFinanceEmpty('Không tải được tổng hợp ngân sách.'));
			budgetLinesList.replaceChildren(makeFinanceEmpty('Không tải được khoản dự kiến.'));
			expensesList.replaceChildren(makeFinanceEmpty('Không tải được khoản đã chi.'));
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
			const end = String(values.get('end_local') || '');
			const payload = {
				title_snapshot: String(values.get('title_snapshot') || '').trim(),
				item_type: String(values.get('item_type') || 'stop'),
				address_snapshot: String(values.get('address_snapshot') || '').trim(),
				start_local: start || null,
				end_local: end || null,
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


	if (budgetForm) {
		budgetForm.addEventListener('submit', async function (event) {
			event.preventDefault();
			if (!activeTrip || !budgetForm.reportValidity()) return;
			const values = new FormData(budgetForm);
			const unitAmount = String(values.get('unit_amount_minor') || '');
			const button = document.getElementById('create-budget-submit');
			button.disabled = true;
			button.textContent = 'Đang lưu…';
			try {
				await api('trips/' + encodeURIComponent(activeTrip) + '/budget/lines', 'POST', {
					category: String(values.get('category') || ''),
					description: String(values.get('description') || '').trim(),
					quantity: String(values.get('quantity') || '1.00'),
					unit_amount_minor: unitAmount === '' ? null : Number(unitAmount),
					currency: 'VND',
					cost_type: String(values.get('cost_type') || 'estimate')
				});
				budgetForm.reset();
				document.getElementById('budget-quantity').value = '1.00';
				showFinanceFeedback('Đã thêm khoản ngân sách.', false);
				await loadFinance();
			} catch (error) {
				showFinanceFeedback(error.message, true);
			} finally {
				button.disabled = false;
				button.textContent = 'Thêm khoản dự kiến';
			}
		});
	}

	if (expenseForm) {
		expenseForm.addEventListener('submit', async function (event) {
			event.preventDefault();
			if (!activeTrip || !expenseForm.reportValidity()) return;
			const values = new FormData(expenseForm);
			const button = document.getElementById('create-expense-submit');
			button.disabled = true;
			button.textContent = 'Đang lưu…';
			try {
				const timezone = activeTripData && activeTripData.destination_timezone
					? activeTripData.destination_timezone
					: 'Asia/Ho_Chi_Minh';
				const occurredAt = localDateTimeToIso(String(values.get('occurred_at') || ''), timezone);
				await api('trips/' + encodeURIComponent(activeTrip) + '/expenses', 'POST', {
					category: String(values.get('category') || ''),
					amount_minor: Number(values.get('amount_minor')),
					currency: 'VND',
					occurred_at: occurredAt,
					note: String(values.get('note') || '').trim()
				});
				expenseForm.reset();
				showFinanceFeedback('Đã ghi nhận khoản chi.', false);
				await loadFinance();
			} catch (error) {
				showFinanceFeedback(error.message, true);
			} finally {
				button.disabled = false;
				button.textContent = 'Lưu khoản chi';
			}
		});
	}

	if (tripList) {
		tripList.addEventListener('click', function (event) {
			const button = event.target.closest('[data-open-trip]');
			if (button) openTrip(button.dataset.openTrip);
		});
	}

	function showRentalWorkspace(query) {
		const rentalWorkspace = document.getElementById('rental-workspace');
		if (!rentalWorkspace) return;
		hideWorkspace();
		document.body.classList.add('is-rental-view');
		if (tripList) tripList.hidden = true;
		if (sectionHeading) sectionHeading.hidden = true;
		if (createTripPanel) createTripPanel.hidden = true;
		rentalWorkspace.hidden = false;
		const params = new URLSearchParams(query || '');
		const stopName = params.get('name') || 'điểm dừng đã chọn';
		const heading = document.getElementById('rental-workspace-title');
		const context = document.getElementById('rental-stop-context');
		const contextChip = document.getElementById('rental-context-chip');
		const back = document.getElementById('rental-back-link');
		if (heading) heading.textContent = 'Thuê đồ cho ' + stopName;
		if (context) context.textContent = 'Điểm dừng đang chọn: ' + stopName;
		if (contextChip) {
			const contextParts = ['Từ hành trình'];
			if (params.get('day')) contextParts.push(params.get('day'));
			if (params.get('time')) contextParts.push(params.get('time'));
			contextChip.textContent = contextParts.join(' · ');
			contextChip.hidden = !params.get('stop');
		}
		if (back) {
			const returnTarget = params.get('return') || '#trips';
			back.href = returnTarget;
			back.textContent = params.has('stop') && params.has('return') ? '← Quay lại ' + stopName : '← Quay lại hành trình';
		}
		const nextAction = rentalWorkspace.querySelector('.rental-empty-state__back');
		if (nextAction && back) nextAction.href = back.href;
	}

	window.addEventListener('hashchange', function () {
		const match = window.location.hash.match(/^#trip\/([a-f0-9-]{36})$/i);
		const shopMatch = window.location.hash.match(/^#shop(?:\?(.*))?$/);
		if (shopMatch) {
			showRentalWorkspace(shopMatch[1] || '');
			return;
		}
		if (match) {
			loadTrip(match[1]);
		} else {
			hideWorkspace();
		}
	});

	window.dealzItinerary = { openTrip: openTrip };

	const initialShopMatch = window.location.hash.match(/^#shop(?:\?(.*))?$/);
	if (initialShopMatch) {
		showRentalWorkspace(initialShopMatch[1] || '');
	}

	const initialMatch = window.location.hash.match(/^#trip\/([a-f0-9-]{36})$/i);
	if (initialMatch) {
		loadTrip(initialMatch[1]);
	}
})();
