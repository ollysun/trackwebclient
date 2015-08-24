$(function($) {
	setTimeout(function() {
		$('#content-wrapper > .row').css({
			opacity: 1
		});
	}, 200);

	$('#sidebar-nav,#nav-col-submenu').on('click', '.dropdown-toggle', function (e) {
		e.preventDefault();

		var $item = $(this).parent();

		if (!$item.hasClass('open')) {
			$item.parent().find('.open .submenu').slideUp('fast');
			$item.parent().find('.open').toggleClass('open');
		}

		$item.toggleClass('open');

		if ($item.hasClass('open')) {
			$item.children('.submenu').slideDown('fast');
		}
		else {
			$item.children('.submenu').slideUp('fast');
		}
	});

	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav .dropdown-toggle', function (e) {
		if ($( document ).width() >= 992) {
			var $item = $(this).parent();

			if ($('body').hasClass('fixed-leftmenu')) {
				var topPosition = $item.position().top;

				if ((topPosition + 4*$(this).outerHeight()) >= $(window).height()) {
					topPosition -= 6*$(this).outerHeight();
				}

				$('#nav-col-submenu').html($item.children('.submenu').clone());
				$('#nav-col-submenu > .submenu').css({'top' : topPosition});
			}

			$item.addClass('open');
			$item.children('.submenu').slideDown('fast');
		}
	});

	$('body').on('mouseleave', '#page-wrapper.nav-small #sidebar-nav > .nav-pills > li', function (e) {
		if ($( document ).width() >= 992) {
			var $item = $(this);

			if ($item.hasClass('open')) {
				$item.find('.open .submenu').slideUp('fast');
				$item.find('.open').removeClass('open');
				$item.children('.submenu').slideUp('fast');
			}

			$item.removeClass('open');
		}
	});
	$('body').on('mouseenter', '#page-wrapper.nav-small #sidebar-nav a:not(.dropdown-toggle)', function (e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});
	$('body').on('mouseleave', '#page-wrapper.nav-small #nav-col', function (e) {
		if ($('body').hasClass('fixed-leftmenu')) {
			$('#nav-col-submenu').html('');
		}
	});

	$('#make-small-nav').click(function (e) {
		$('#page-wrapper').toggleClass('nav-small');
	});

	$(window).smartresize(function(){
		if ($( document ).width() <= 991) {
			$('#page-wrapper').removeClass('nav-small');
		}
	});

	$('.mobile-search').click(function(e) {
		e.preventDefault();

		$('.mobile-search').addClass('active');
		$('.mobile-search form input.form-control').focus();
	});
	$(document).mouseup(function (e) {
		var container = $('.mobile-search');

		if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
		{
			container.removeClass('active');
		}
	});

	$('.fixed-leftmenu #col-left').nanoScroller({
    	alwaysVisible: false,
    	iOSNativeScrolling: false,
    	preventPageScrolling: true,
    	contentClass: 'col-left-nano-content'
    });

	// build all tooltips from data-attributes
	$("[data-toggle='tooltip']").each(function (index, el) {
		$(el).tooltip({
			placement: $(this).data("placement") || 'top'
		});
	});

	// CourierPlus: Disable backspace button on all pages
	$(document).on("keydown", function (e) {
		if (e.which === 8 && !$(e.target).is("input:not([readonly]):not([type=radio]):not([type=checkbox]), textarea, [contentEditable], [contentEditable=true]")) {
			e.preventDefault();
		}
	});


  // activate tooltip // collapse/expand
  $('[data-toggle="tooltip"]').tooltip()

});

$.fn.removeClassPrefix = function(prefix) {
    this.each(function(i, el) {
        var classes = el.className.split(" ").filter(function(c) {
            return c.lastIndexOf(prefix, 0) !== 0;
        });
        el.className = classes.join(" ");
    });
    return this;
};

(function($,sr){
	// debouncing function from John Hann
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	var debounce = function (func, threshold, execAsap) {
		var timeout;

		return function debounced () {
			var obj = this, args = arguments;
			function delayed () {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			};

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = setTimeout(delayed, threshold || 100);
		};
	}
	// smartresize
	jQuery.fn[sr] = function(fn){	return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');

(function($){
	// CourierPlus: Disable form submit button on form submit
	var options = {
		/**
		 * namespace for all events
		 *
		 * @type {String}
		 */
		evtNamespace: '.CP.form.submitButton',
		/**
		 * timeout till btn is enabled on form submit
		 * A value of 0 (or false) will disable completely
		 *
		 * @type {Number}
		 */
		btnTimeout: 4000,
	};
	var events = {
		disable: jQuery.Event( np("disable") ),
		disabling: jQuery.Event( np("disabling") ),
		disabled: jQuery.Event( np("disabled") ),
		enable: jQuery.Event( np("enable") ),
		enabling: jQuery.Event( np("enabling") ),
		enabled: jQuery.Event( np("enabled") ),
	}

	/**
	 * Enable submit button and fire before/after events
	 *
	 * @param  {object} form The form DOM object
	 */
	function enable(form) {
		form = $(form);
		var btns = form.find('[type=submit]');
		form.trigger(events.enabling);
		btns.prop('disabled', false);
		form.trigger(events.enabled);
	}

	/**
	 * Disable submit button and fire before/after events
	 *
	 * @param  {object} form The form DOM object
	 */
	function disable(form) {
		form = $(form);
		var btns = form.find('[type=submit]');
		form.trigger(events.disabling);
		btns.prop('disabled', true);
		form.trigger(events.disabled);
	}

	/**
	 * Apply namespace to event names
	 *
	 * @param  {string} e event name
	 *
	 * @return {string}   namspaced event name
	 */
	function np(e) {
		return e+options.evtNamespace;
	}


	$('form').on(np('enable'), function(){
		enable(this);
	}).on(np('disable'), function(){
		disable(this);
	}).on(np('submit'),function(){
		var form = this;
		disable(this);

		if (options.btnTimeout) {
			window.setTimeout(function(){
				enable(form);
			}, options.btnTimeout);
		}
	});

	// Register as a jQuery function
	$.fn.formSubmitButton = function(action) {
		var options = {
			defaultFxn: enable,
		},
		fxn;

		switch (action) {
			case 'disable':
			case false:
				fxn = disable;
				break;
			case 'enable':
			case true:
				fxn = enable;
				break;
			default:
				fxn = options.defaultFxn;
				break;
		}

		return this.each(function(){
			fxn(this);
		});
	};
})(jQuery)