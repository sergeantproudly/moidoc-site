$(document).ready(function() {
	function getCookie(name) {
		  var matches = document.cookie.match(new RegExp(
		    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
		  ));
		  return matches ? decodeURIComponent(matches[1]) : undefined;
		}
		
		function setCookie(name, value, options) {
		  options = options || {};		
		  var expires = options.expires;		
		  if (typeof expires == "number" && expires) {
		    var d = new Date();
		    d.setTime(d.getTime() + expires * 1000);
		    expires = options.expires = d;
		  }
		  if (expires && expires.toUTCString) {
		    options.expires = expires.toUTCString();
		  }		
		  value = encodeURIComponent(value);		
		  var updatedCookie = name + "=" + value;		
		  for (var propName in options) {
		    updatedCookie += "; " + propName;
		    var propValue = options[propName];
		    if (propValue !== true) {
		      updatedCookie += "=" + propValue;
		    }
		  }		
		  document.cookie = updatedCookie;
		}
		
		function deleteCookie(name) {
		  setCookie(name, "", {
		    expires: -1
		  })
		}

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