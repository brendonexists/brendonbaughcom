(function () {
	'use strict';

	var config = window.bbBibleStudyChat;
	var root = document.querySelector('[data-session-id]');

	if (!config || !root) {
		return;
	}

	var messagesEl = root.querySelector('[data-chat-messages]');
	var form = root.querySelector('[data-chat-form]');
	var input = root.querySelector('[data-chat-input]');
	var notice = root.querySelector('[data-chat-notice]');
	var lastId = 0;
	var hasLoaded = false;

	function setNotice(message) {
		if (notice) {
			notice.textContent = message || '';
		}
	}

	function post(action, data) {
		var body = new FormData();
		body.append('action', action);
		body.append('nonce', config.nonce);
		body.append('sessionId', config.sessionId);

		Object.keys(data || {}).forEach(function (key) {
			body.append(key, data[key]);
		});

		return fetch(config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		}).then(function (response) {
			return response.json().catch(function () {
				return {
					success: false,
					data: {
						message: 'Chat server returned an invalid response.'
					}
				};
			});
		});
	}

	function messageNode(message) {
		var item = document.createElement('article');
		item.className = 'bb-bible-study-chat__message';
		item.dataset.messageId = message.id;
		item.dataset.userId = message.userId;

		var avatar = document.createElement('img');
		avatar.className = 'bb-bible-study-chat__avatar';
		avatar.src = message.avatar;
		avatar.alt = '';

		var body = document.createElement('div');
		body.className = 'bb-bible-study-chat__body';

		var meta = document.createElement('div');
		meta.className = 'bb-bible-study-chat__meta';

		var author = document.createElement('strong');
		author.textContent = message.author;

		var time = document.createElement('span');
		time.textContent = message.createdAt;

		var text = document.createElement('p');
		text.textContent = message.message;

		meta.appendChild(author);
		meta.appendChild(time);
		body.appendChild(meta);
		body.appendChild(text);

		if (config.canModerate) {
			var tools = document.createElement('div');
			tools.className = 'bb-bible-study-chat__tools';

			var hide = document.createElement('button');
			hide.type = 'button';
			hide.textContent = 'Hide';
			hide.addEventListener('click', function () {
				moderate(message.id, 'hidden', item);
			});

			var mute = document.createElement('button');
			mute.type = 'button';
			mute.textContent = 'Mute 15m';
			mute.addEventListener('click', function () {
				muteUser(message.userId);
			});

			tools.appendChild(hide);
			tools.appendChild(mute);
			body.appendChild(tools);
		}

		item.appendChild(avatar);
		item.appendChild(body);

		return item;
	}

	function isNearBottom() {
		if (!messagesEl) {
			return false;
		}

		return messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight < 72;
	}

	function appendMessages(messages, shouldScroll) {
		if (!messagesEl || !messages.length) {
			return;
		}

		messages.forEach(function (message) {
			if (messagesEl.querySelector('[data-message-id="' + message.id + '"]')) {
				return;
			}

			messagesEl.appendChild(messageNode(message));
			lastId = Math.max(lastId, parseInt(message.id, 10));
		});

		if (shouldScroll) {
			messagesEl.scrollTop = messagesEl.scrollHeight;
		}
	}

	function fetchMessages() {
		var shouldScroll = hasLoaded && isNearBottom();

		post('bb_bible_study_fetch_messages', {
			afterId: lastId
		}).then(function (result) {
			if (!result.success) {
				setNotice(result.data && result.data.message ? result.data.message : 'Chat could not refresh.');
				return;
			}

			appendMessages(result.data.messages || [], shouldScroll);
			hasLoaded = true;

			if (result.data.muted && input) {
				input.disabled = true;
				setNotice('You are muted for this chat.');
			}
		}).catch(function () {
			setNotice('Chat could not refresh. Check that you are logged in and try again.');
		});
	}

	function moderate(messageId, status, item) {
		post('bb_bible_study_moderate_message', {
			messageId: messageId,
			status: status
		}).then(function (result) {
			if (result.success && item) {
				item.remove();
			}
		});
	}

	function muteUser(userId) {
		post('bb_bible_study_mute_user', {
			userId: userId,
			minutes: 15
		}).then(function (result) {
			setNotice(result.success ? 'User muted for 15 minutes.' : 'Could not mute user.');
		});
	}

	if (form && input) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var message = input.value.trim();
			if (!message) {
				return;
			}

			input.disabled = true;
			post('bb_bible_study_send_message', {
				message: message
			}).then(function (result) {
				input.disabled = false;

				if (!result.success) {
					setNotice(result.data && result.data.message ? result.data.message : 'Message could not send.');
					return;
				}

				input.value = '';
				setNotice('');
				appendMessages([result.data.message], true);
			}).catch(function () {
				input.disabled = false;
				setNotice('Message could not send. Check that the session is live and try again.');
			});
		});
	}

	fetchMessages();
	if (config.mode === 'live') {
		window.setInterval(fetchMessages, parseInt(config.pollMs, 10) || 3000);
	}
})();
