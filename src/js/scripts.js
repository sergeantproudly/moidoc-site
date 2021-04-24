// ON DOCUMENT READY
(function ($) {
	$.fn.lightTabs = function() {
		var showTab = function(tab, saveHash) {
			if (!$(tab).hasClass('tab-act')) {
				var tabs = $(tab).closest('.tabs');

				var target_id = $(tab).attr('href');
		        var old_target_id = $(tabs).find('.tab-act').attr('href');
		        $(target_id).show();
		        $(old_target_id).hide();
		        $(tabs).find('.tab-act').removeClass('tab-act');
		        $(tab).addClass('tab-act');

		        if (typeof(saveHash) != 'undefined' && saveHash) history.pushState(null, null, target_id);
			}
		}

		var initTabs = function() {
            var tabs = this;
            var hasAct = $(tabs).find('.tab-act').length;
            
            $(tabs).find('a').each(function(i, tab){
                $(tab).click(function(e) {
                	e.preventDefault();

                	showTab(this, true);
                	fadeoutInit();

                	return false;
                });
                if ((!hasAct && i == 0) || (hasAct && $(tab).hasClass('tab-act'))) showTab(tab);             
                else $($(tab).attr('href')).hide();
            });	

            $(tabs).swipe({
				swipeStatus: function(event, phase, direction, distance) {
					var offset = distance;

					if (phase === $.fn.swipe.phases.PHASE_START) {
						var origPos = $(this).scrollLeft();
						$(this).data('origPos', origPos);

					} else if (phase === $.fn.swipe.phases.PHASE_MOVE) {
						var origPos = $(this).data('origPos');

						if (direction == 'left') {
							var scroll_max = $(this).prop('scrollWidth') - $(this).width();
							var scroll_value_new = origPos - 0 + offset;
							$(this).scrollLeft(scroll_value_new);
							if (scroll_value_new >= scroll_max) $(this).addClass('scrolled-full');
							else $(this).removeClass('scrolled-full');

						} else if (direction == 'right') {
							var scroll_value_new = origPos - offset;
							$(this).scrollLeft(scroll_value_new);
							$(this).removeClass('scrolled-full');
						}

					} else if (phase === $.fn.swipe.phases.PHASE_CANCEL) {
						var origPos = $(this).data('origPos');
						$(this).scrollLeft(origPos);

					} else if (phase === $.fn.swipe.phases.PHASE_END) {
						$(this).data('origPos', $(this).scrollLeft());
					}
				},
				threshold: 70
			});	
        };

        return this.each(initTabs);
    };

	$(function () {
		initElements();

		// BURGER
		$('#mn-main').click(function() {
			if (__isMobileSmall && !$('body').hasClass('mobile-opened')) {
				var $btn = $('#btn-feedback').parent('a');

				// clear animations to fix blink bug on close
				$('#mn-main').removeClass('animated fadeInDownSmall');

				if (!$('#mn-main>.close').length) {
					$('#mn-main').append('<div class="close"></div>');
					$('#mn-main').children('.close').click(function(e) {
						e.stopPropagation();
						
						$('#layout').animate({scrollLeft: 0}, __animationSpeed, function() {
							var st = $('#layout').scrollTop();
							$(this).height('auto')
								.removeClass('js-modal-overflow');
							$('html').removeClass('html-mobile-long');
							$('body').removeClass('mobile-opened');
							$btn.appendTo($('header>.holder'));
							$('#layout').scrollTop(0);
							$('html, body').scrollTop(st);
							$('header').removeAttr('style');							
							$('#mn-main').removeAttr('style');
							
							// fix blink bug on close
							$('#mn-main').hide();
							setTimeout(function() {
								$('#mn-main').show();
							}, 50)
						});
					});
				}

				var st = $(window).scrollTop();
				var offset = 64;
				var menuWidth = $(window).width() - offset;

				$btn.appendTo($('#mn-main'));
				$('body').addClass('mobile-opened');
				$('#mn-main').width(menuWidth - parseInt($('#mn-main').css('padding-left'))*2)
					.css('left', $(window).width() - parseFloat($('header>.holder').css('padding-right')));

				$('html').toggleClass('html-mobile-long', $('#mn-main').height() > $(window).height());
				$('#layout').addClass('js-modal-overflow').height($('#mn-main').height() + 11);
				$('#layout').scrollTop(st);
				$('header').css('top', st);

				// iphone bottom navigation temp fix
				if (isiPhone()) {
					$('#mn-main').addClass('iphone');
				}

				$('#layout').stop()
					.animate({scrollLeft: menuWidth}, __animationSpeed);
			}
		});

		// FIXING HEADER
		scrollCallbacks.push(function(st) {
			var offset1 = __isMobile ? 60 : 140;
			var offset2 = $(window).height();
			var thid = null;
			
			if (!__isMobileSmall || !$('body').hasClass('mobile-opened')) {
				if (st > offset1) {
					if (!$('header').hasClass('sticky')) {
						$('header').addClass('sticky')
							.addClass('hidden');
						if (thid) clearTimeout(thid);
						thid = setTimeout(function() {
							$('header').removeClass('hidden');
						});
					}
				} else {
					if ($('header').hasClass('sticky')) {
						$('header').removeClass('sticky')
							.removeClass('hidden');
					}
				}

				if (st > offset2) {
					$('header').removeClass('hidden')
						.addClass('shown');
				} else {
					$('header').removeClass('shown');
				}
			}
		});

		// WOW ANIMATION
		if (typeof(WOW) != 'undefined') {
			new WOW().init({
				live: true
			});
		}

		// ANCHOR LINKS
		$('a.js-anchor').click(function(e) {
			e.preventDefault();

			_scrollTo($(this).attr('href'));
		});

		// SLICKS
		$('.js-slider').each(function(i, slider) {
			var mobile = $(slider).attr('data-mobile');
			var adaptive = $(slider).attr('data-adaptive');
			var dots = $(slider).attr('data-dots') === 'false' ? false : true;
			var arrows = $(slider).attr('data-arrows') === 'true' ? true : false;
			var infinite = $(slider).attr('data-infinite') === 'true' ? true : false;
			var autoplay = $(slider).attr('data-autoplay') ? $(slider).attr('data-autoplay') : false;
			var slidesToShow = $(slider).attr('data-slides-to-show') ? $(slider).attr('data-slides-to-show') : (adaptive ? Math.floor($(slider).outerWidth() / $(slider).children('li').outerWidth()) : 1);

			if (mobile) {
				if ((mobile === 'true' && __isMobile) ||
					(mobile === 'middle' && __isMobileTabletMiddle) ||
					(mobile === 'small' && __isMobileTabletSmall) ||
					(mobile === 'mobile' && __isMobileSmall)) {		

					$(slider).slick({
						slidesToShow: slidesToShow,
						slidesToScroll: slidesToShow,
						dots: dots,
						arrows: arrows,
						infinite: infinite,
						autoplay: autoplay
					});
				}
			} else {
				$(slider).slick({
					slidesToShow: slidesToShow,
					slidesToScroll: slidesToShow,
					dots: dots,
					arrows: arrows,
					autoplay: autoplay,
					infinite: infinite
				});
			}
		});

		$(window).scroll();
		$(window).resize();

		// FEEDBACK BUTTON
		$('#btn-feedback').parent('a').click(function(e) {
			e.preventDefault();

			var to = $(this).attr('href');

			if ($(to).hasClass('hidden')) {
				$(to).fadeIn(__animationSpeed, function() {
					$(this).removeClass('hidden');
				});
			}

			if ($('body').hasClass('mobile-opened')) {
				$('#mn-main .close').click();
				setTimeout(function() {
					_scrollTo(to);
				}, __animationSpeed + 50);

			} else {
				_scrollTo(to);
			}
		});

		// LANG LINKS
		$('#mn-lang li>a').click(function(e) {
			e.preventDefault();

			var domain = $(this).attr('href');
			var link = domain + '/set-lang';
			/*
			$.ajax({
			    url: 'https://' + domain + '/ajax--act-SetLangSelected/',
			    type: 'POST',
			    success: function(response) {
			        if (response == 'OK') {
			        	window.location.href = 'https://' + domain;
			        }
			    },
			    error: function(response) {
			    	console.log(response);
			    }
			});
			*/
			//$.getJSON(link);
			/*
			var script   = document.createElement('script');
		    script.type  = 'text/javascript';
		    script.async = true;
		    script.src   = link;
		    document.body.appendChild(script);
		    */

		    window.location.href = link;
		});

		// FEEDBACK FORM
		$('#bl-feedback form').find('input, textarea').on('input', function() {
			$(this).removeClass('invalid');
		});
		$('#bl-feedback form').find('button, input:submit').click(function(e) {
			$form = $(this).closest('form');
			$name = $form.find('input[name="name"]');
			$email = $form.find('input[name="email"]');
			$tel = $form.find('input[name="tel"]');
			$text = $form.find('textarea[name="text"]');
			
			$name.removeClass('invalid');
			$email.removeClass('invalid');
			$tel.removeClass('invalid');
			$text.removeClass('invalid');

			$form.find('[required]').addClass('attempted');
			var err = false;
			if (!$name.val()) {
				err = true;
				$name.addClass('invalid');
			}
			if (!$email.val() || !checkEmail($email.get(0))) {
				err = true;
				$email.addClass('invalid');
			}
			if (!$tel.val()) {
				err = true;
				$tel.addClass('invalid');
			}
			if (!$text.val()) {
				err = true;
				$text.addClass('invalid');
			}
			if (err) {
				e.preventDefault();
				e.stopPropagation();

			}
		});

		$('#bl-feedback form').submit(function(e) {
			e.preventDefault();

			var portalId = '7649479';
			var formGuid = '578467ba-6053-4891-b620-b5d44ccc3238';
			var url = 'https://api.hsforms.com/submissions/v3/integration/submit/' + portalId + '/' + formGuid;
			var fields = [
				{
					'name': 'firstname',
					'value': $name.val()
				},
				{
					'name': 'email',
					'value': $email.val()
				},
				{
					'name': 'phone',
					'value': $tel.val()
				},
				{
					'name': 'text',
					'value': $text.val()
				}
			];
			var json = {
				'submittedAt': Date.now(),
				'fields': fields
			};

			$.ajax({
			    url: url,
			    type: 'POST',
			    data: JSON.stringify(json),
			    contentType: 'application/json',
			    dataType: 'json',
			    success: function(response) {
			        $form.get(0).reset();
					showModal('modal-done');
			    },
			    error: function(response) {
			    	console.log(response);
			    }
			});
		});

		// SUBSCRIBE FORM
		$('#bl-subscribe form').find('input').on('input', function() {
			$(this).removeClass('invalid');
		});
		$('#bl-subscribe form').find('button, input:submit').click(function(e) {
			$form = $(this).closest('form');
			$name = $form.find('input[name="name"]');
			$email = $form.find('input[name="email"]');
			
			$name.removeClass('invalid');
			$email.removeClass('invalid');

			$form.find('[required]').addClass('attempted');
			var err = false;
			if (!$name.val()) {
				err = true;
				$name.addClass('invalid');
			}
			if (!$email.val() || !checkEmail($email.get(0))) {
				err = true;
				$email.addClass('invalid');
			}
			if (err) {
				e.preventDefault();
				e.stopPropagation();
			}
		});

		$('#bl-subscribe form').submit(function(e) {
			e.preventDefault();
		});

		// BLOG
		if ($('#blog').length) {
			$('#blog .filter>ul>li>a').click(function(e) {
				e.preventDefault();

				$li = $(this).closest('li');
				if (!$li.hasClass('act')) {
					var tag = $(this).attr('href').substring(1);

					$('#statues-list>ul>li').each(function(index, li) {
						if ($(li).attr('data-tag') == tag || tag == 'all') {
							$(li).stop().fadeIn(__animationSpeed);
						} else {
							$(li).stop().fadeOut(__animationSpeed);
						}
					});

					$li.addClass('act')
						.siblings('.act')
						.removeClass('act');

					// FIXME DEMO
					if (tag == 'all') {
						$('#statues-list>.btn-line').stop().slideDown(__animationSpeed);
					} else {
						$('#statues-list>.btn-line').stop().slideUp(__animationSpeed);
					}
				}
			});
		}

		// FAQ
		$('#bl-faq ul>li>.question').click(function() {
			var $answer = $(this).siblings('.answer');
			if (!$(this).closest('li').hasClass('opened')) {
				$answer.height(0).show();
				$(this).closest('li').addClass('opened');
				var h = $answer.height('auto').outerHeight();
				$answer.height(0).stop().animate({
					'height': h,
					'margin-top': __isMobileSmall ? '8vw' : '3.75vw',
					'margin-bottom': __isMobileSmall ? '3.8vw' : '1vw'
				}, __animationSpeed);

			} else {
				$answer.stop().animate({
					'height': 0,
					'margin-top': 0,
					'margin-bottom': 0
				}, __animationSpeed, function() {
					$(this).hide();
				});
				$(this).closest('li').removeClass('opened');
			}
		});

		// BOOK BUTTON
		$('#btn-book').parent('a').click(function(e) {
			e.preventDefault();

			var to = $(this).attr('href');
			_scrollTo(to);
		});

		// BOOK FORM
		$('#bl-book form').find('input, textarea').on('input', function() {
			$(this).removeClass('invalid');
		});
		$('#bl-book form').find('button, input:submit').click(function(e) {
			$form = $(this).closest('form');
			$name = $form.find('input[name="name"]');
			$email = $form.find('input[name="email"]');
			$tel = $form.find('input[name="tel"]');
			$text = $form.find('textarea[name="text"]');
			
			$name.removeClass('invalid');
			$email.removeClass('invalid');
			$tel.removeClass('invalid');
			$text.removeClass('invalid');

			$form.find('[required]').addClass('attempted');
			var err = false;
			if (!$name.val()) {
				err = true;
				$name.addClass('invalid');
			}
			if (!$email.val() || !checkEmail($email.get(0))) {
				err = true;
				$email.addClass('invalid');
			}
			if (!$tel.val()) {
				err = true;
				$tel.addClass('invalid');
			}
			if (!$text.val()) {
				err = true;
				$text.addClass('invalid');
			}
			if (err) {
				e.preventDefault();
				e.stopPropagation();

			}
		});

		$('#bl-book form').submit(function(e) {
			e.preventDefault();

			var portalId = '7649479';
			var formGuid = 'd77bce82-a24f-4d5a-9b14-17d71d598649';
			var url = 'https://api.hsforms.com/submissions/v3/integration/submit/' + portalId + '/' + formGuid;
			var fields = [
				{
					'name': 'firstname',
					'value': $name.val()
				},
				{
					'name': 'email',
					'value': $email.val()
				},
				{
					'name': 'phone',
					'value': $tel.val()
				},
				{
					'name': 'text',
					'value': $text.val()
				}
			];
			var json = {
				'submittedAt': Date.now(),
				'fields': fields
			};

			$.ajax({
			    url: url,
			    type: 'POST',
			    data: JSON.stringify(json),
			    contentType: 'application/json',
			    dataType: 'json',
			    success: function(response) {
			        $form.get(0).reset();
					showModal('modal-done');
			    },
			    error: function(response) {
			    	console.log(response);
			    }
			});
		});

	})
})(jQuery)