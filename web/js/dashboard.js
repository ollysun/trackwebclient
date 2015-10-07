(function($){
	// We need to trigger blur on the control after select box closes
	// This is to remove focus styles

	// event namespace
	var namespace = '.CP.transparent-control';
	$('select.form-control-transparent')
		// default value of counter attribute
		// 0 means closed
		// 1 means open
		.prop('counter', 0)

		.on('click'+namespace, function() {
			var counter = $(this).prop('counter');

			if(counter === 1) { // select box is open at click
				$(this).trigger('blur');
			}
			else {
				counter++;
				$(this).prop('counter', counter);
			}
		})

		.on('blur'+namespace, function(){
			$(this).prop('counter', 0);
		});

})(jQuery);


(function($) {
	var dateFilterForm = $('#date-filter-form');
	var date = $(dateFilterForm.find('[name="date"]')),
	    from = $(dateFilterForm.find('[name="from"]')),
	    to = $(dateFilterForm.find('[name="to"]')),
	    textWrap = $('#relative-date-text');

	var fromtodate = $('.dashboard-stats-title-custom-date-wrap');

	buildDateOptions(filter_date);
	textWrap.html(relativeDateTextHtml());

	date.change(function() {
		var value = $(this).val();

		fromtodate.removeClass('open');
		if (value === 'custom') {
			//show from and to display boxes
			fromtodate.addClass('open');
		}
		else if (value !== '') {
			from.val( getDateString(getRelativeDate(value)) );
			to.val( getDateString(new Date()) );
			dateFilterForm.trigger('submit');
		}
	});


	function buildDateOptions(value) {
		var dateArray = ['0d', '-1w', '-2w', '-1m', '-2m', '-3m', '-6m', '-1y'];
		var html = '<option value="">Change</option>';
		for (var i = 0; i < dateArray.length; i++) {
			var item = dateArray[i];
			var disabled = (value === item) ? ' disabled="disabled"' : '';
			html += '<option value="'+item+'"'+disabled+'>'+getRelativeDateText(item)+'</option>';
		}
		html += '<option value="custom">Custom</option>';
		date.html(html);
	}

	function relativeDateTextHtml() {
		var date = filter_date;
		if (!isValidString(date)) {
			// 'custom' date
			if (date === 'custom') {
				return filter_from.toDateString() + ' &mdash; ' + filter_to.toDateString();
			}
			// Empty and possibly other values
			return '';
		}
		// For valid dates
		return getRelativeDateText(date);

	}


	/**
	 * Get date string according to the format: yyyy/mm/dd
	 *
	 * @method  getDateString
	 *
	 * @param   {Date}       date   Date object to be converted
	 *
	 * @return  {string}            date string in the required format
	 */
	function getDateString (date) {
		var m = date.getMonth(),
		    d = date.getDate();
		m++;

		m = (m < 10) ? '0'+m : m;
		d = (d < 10) ? '0'+d : d;

		return date.getFullYear() + '/' + m + '/' + d;
	}
})(jQuery);


/**
 * Get the relative date from today based on the value passed in a specific format
 *
 * @method  getRelativeDate
 *
 * @param   {string}   string          describes the relative date in a specific format
 *
 * @return  {Date}                     Date object representing the relative date
 */
function getRelativeDate(string) {
	var today = new Date();

	// Check if string is valid. If it isn't, return false.
	if( !isValidString(string) ) {
		console.error('Invalid relative date string');
		return false;
	}

	// Find the number of days to make adjustment, string
	var days = getDaysAdjustment(today, string);

	var relativeDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + days);
	return relativeDate;

}


/**
 * Get the number of days to adjust, based on the date and relative date string
 *
 * @method  getDaysAdjustment
 *
 * @param   {Date}           date      Date object describing the start date to be used
 * @param   {string}         string    the string representing the relative date
 *
 * @return  {integer}                  the number of days to be adjusted from the date used and the relative date string
 */
function getDaysAdjustment(date, string) {
	// Get the different parts (integer and letter) from the string
	var parts = getRelativeParts(string);

	var integer = parts[0],
	    letter = parts[1],
	    isPositive = parts[2];

	// Get year and month
	var month = date.getMonth(),
	    year = date.getFullYear();


	var days;

	switch (letter) {
		default:
			days = integer;
			break;
		case 'd':
			days = integer * 1;
			break;
		case 'w':
			days = integer * 7;
			break;
		case 'm':
			days = 0;
			for (var i = Math.abs(integer); i > 0 ; i--) {
				if (month > 11 || month < 0) {
					month = 0;
					year = (isPositive) ? year + 1: year - 1;
				}
				if(isPositive) {
					days += getMonthDays(month, year);
					month++;
				}
				else {
					month--;
					var m = month; // month -1;
					if (m < 0) {
						m = 11;
					}
					days -= getMonthDays(m, year);
				}
			}
			break;
		case 'y':
			days = Math.ceil(integer * 365.25);
			break;
	}

	return days;
}


/**
 * Get the different parts (integer and letter) from the relative date string
 * Please validate string using isValidString() before calling this function
 *
 * @method  getRelativeParts
 *
 * @param   {string}              string  the relative date string
 *
 * @return  {array}                        an array containing the integer, letter, and isPositive (for integer)
 */
function getRelativeParts(string) {
	var length = string.length;
	var integer = parseInt(string.substring(0, length - 1)),
	    letter = string.substring(length -1);

	var isPositive = false;
	if (integer > -1) {
		isPositive = true;

		// Eliminate -0
		integer = Math.abs(integer);
	}

	return [integer, letter, isPositive];
}


/**
 * Checks if relative date string is valid or not
 * Valid format is a string containing an integer, followed by a specific letter eg '0d', '-1w'
 *
 * @method  isValidString
 *
 * @return  {Boolean}               validity of date string based on format
 */
function isValidString(string) {
	var re = new RegExp(/^\-?\d+(d|w|m|y)$/);
	return re.test(string);
}


/**
 * Get the number of days in a particular month / year combination
 *
 * @method  getMonthDays
 *
 * @param   {integer}      month       the month index (0 is January and so on)
 * @param   {integer}      year        the year
 *
 * @return  {integer}                  the number of days in the month
 */
var getMonthDays = function (month, year) {
	// Number of days in each month index
	var days = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	if (year % 4 === 0) { //leap year
		days[1]++;
	}
	return days[month];
};


/**
 * Get the appropriate text for the relative date string
 *
 * @method  getRelativeDateText
 *
 * @param   {string}             string  the relative date string
 *
 * @return  {string}                     the corresponding text to the relative date string
 */
function getRelativeDateText(string) {
	var full_texts = {
		d: 'day',
		w: 'week',
		m: 'month',
		y: 'year'
	};
	var text = '';

	// Get the different parts (integer and letter) from the string
	var parts = getRelativeParts(string);

	var integer = parts[0],
	    letter = parts[1],
	    isPositive = parts[2];

	if (integer === 0) {
		text = 'Today ';
	}
	else {
		integer = Math.abs(integer);

		text += (isPositive) ? 'Next ' : 'Last ';

		text += (integer > 1) ? integer+' '+full_texts[letter]+'s' : full_texts[letter]

	}
	return text;
}