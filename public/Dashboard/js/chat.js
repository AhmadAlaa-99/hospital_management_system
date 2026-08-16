$(function() {
	'use strict';

	function scrollChatToBottom(smooth) {
		var $body = $('#ChatBody');
		if (!$body.length) return;
		var el = $body.get(0);
		if (smooth && el.scrollTo) {
			el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
		} else {
			$body.scrollTop($body.prop('scrollHeight'));
		}
	}

	if ($('#chatActiveContacts').length) {
		$('#chatActiveContacts').lightSlider({
			autoWidth: true,
			controls: false,
			pager: false,
			slideMargin: 12
		});
	}

	if (window.matchMedia('(min-width: 992px)').matches) {
		if ($('#ChatList').length && typeof PerfectScrollbar !== 'undefined' && !$('#ChatList').data('ps')) {
			new PerfectScrollbar('#ChatList', { suppressScrollX: true });
			$('#ChatList').data('ps', true);
		}
	}

	scrollChatToBottom(false);

	$(document).on('click touch', '.hms-chat-list-item.media', function() {
		$(this).addClass('selected').removeClass('new');
		$(this).siblings().removeClass('selected');
		if (window.matchMedia('(max-width: 991px)').matches) {
			$('body').addClass('main-content-body-show');
		}
		setTimeout(function () { scrollChatToBottom(true); }, 200);
	});

	$('[data-toggle="tooltip"]').tooltip();

	$('#ChatBodyHide').on('click touch', function(e) {
		e.preventDefault();
		$('body').removeClass('main-content-body-show');
	});

	if (typeof Livewire !== 'undefined') {
		document.addEventListener('livewire:load', function () {
			Livewire.hook('message.processed', function () {
				setTimeout(function () { scrollChatToBottom(true); }, 100);
			});
		});
	}

	var chatBody = document.getElementById('ChatBody');
	if (chatBody && typeof MutationObserver !== 'undefined') {
		var observer = new MutationObserver(function () {
			scrollChatToBottom(true);
		});
		observer.observe(chatBody, { childList: true, subtree: true });
	}
});
