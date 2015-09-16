(function($){
	var elementSelectors = 'select[name]:not([disabled]), textarea[name], input[name]:not([type="hidden"])',
	    hasChanges = false;

	var checkForChanges = function(form) {
		var elements = $(form).find(elementSelectors);
		elements.each(function(){
			var element = $(this);
			var type = element.prop('type'),
			    tagName = element.prop('tagName');
			if (tagName === 'SELECT' && this.options[0]) { // not an empty select
				var options = this.options,
				    selectedIndex = this.selectedIndex;
				var defaultSelectedIndex = -1;
				for (var i = 0; i < options.length; i++) {
					if (options[i].defaultSelected)
						defaultSelectedIndex = i;
				}

				if ((defaultSelectedIndex > -1 && !options[selectedIndex].defaultSelected) || (defaultSelectedIndex === -1 && selectedIndex !== 0)) {
					hasChanges = true;
				}
			}
			else if ((type === 'radio' || type === 'checkbox') && this.defaultChecked != this.checked) {
				hasChanges = true;
			}
			else if (this.defaultValue != this.value) {
				hasChanges = true;
			}
		});
	};

	// Register as a jQuery plugin
	$.fn.checkForChanges = function(action) {
		var self = this,
		    $window = $(window),
		    nsp = '.CP.Form.watchChanges', //namespace for events
		    duration = 2000; // duration for momentary disable on form submit
		var evt = {
			bu: 'beforeunload'+nsp,
			s: 'submit'+nsp,
		};

		switch (action) {
			case 'off':
			case 'disable':
				disable();
				break;
			default:
			case 'on':
			case 'enable':
				enable();
				break;
		}

		function disable() {
			$window.off(evt.bu);
		}
		function enable() {
			$window.on(evt.bu, function() {
				hasChanges = false;
				self.each(function(){
					checkForChanges(this);
				});
				if (hasChanges) {
					return "You have unsaved changes in your form." ;
				}
			});
			// hack to disable momentarily on form submit for beforeunload to pass through
			$window.one(evt.s, function(){
				disable();
				window.setTimeout(function(){
					enable();
				}, duration);
			});
		}
		return this;
	};

	//Autoload functionality via form-attributes.
	$('form[data-watch-changes]').checkForChanges();

 })(jQuery);