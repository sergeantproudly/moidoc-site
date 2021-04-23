$(document).ready(function() {
		var noticed = getCookie('cookie_notice');
		if (typeof(noticed) == 'undefined' || noticed != 1) {
			$('body').append('<div id="cookies-notice" style="display:none;"><div class="wrapper"><p>By continuing to use this website <br>you are agreeing to the use of Cookies</p><a href="#" target="_blank"><button class="btn type2">Cookies Policy</button></a><button class="btn js-ok">Accept and close</button><div class="close"></div></div></div>');
			$('#cookies-notice').stop().fadeIn(700);
			$('#cookies-notice .js-ok').click(function() {
				setCookie('cookie_notice', 1);
				$('#cookies-notice').stop().fadeOut(700, function() {
					$('#cookies-notice').remove();
				});
			});
			$('#cookies-notice .close').click(function() {
				$('#cookies-notice').stop().fadeOut(700, function() {
					$('#cookies-notice').remove();
				});
			});
		}
});