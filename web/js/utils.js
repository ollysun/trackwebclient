/**
 * Track Plus utils
 */
var TrackPlusUtil = {

    /**
     * render html content into an element
     * @param target
     * @param url
     * @param data
     * @param successCallBack
     * @param failureCallBack
     */
    renderPartial: function (target, url, data, successCallBack, failureCallBack) {

        if (this.isUndefined(data)) {
            data = {};
        }
        target.html('Loading...');
        $.get(url, data, function (response) {
                if (!TrackPlusUtil.isUndefined(successCallBack)) {
                    successCallBack();
                }
                target.html(response);
            }
            , 'html').fail(function () {
            if (!TrackPlusUtil.isUndefined(failureCallBack)) {
                failureCallBack();
            }
        });
    },

    /**
     * Check if a value is undefined
     * @param value
     * @returns {boolean}
     */
    isUndefined: function (value) {
        return (typeof value == 'undefined');
    },

    ResponseConstants: {
        success: 'success',
        fail: 'fail',
        error: 'error'
    },

    showMessage: function (target, message, is_error) {
        this.removeMessage(target);
        if (!target.hasClass('alert')) {
            target.addClass('alert');
        }
        if (this.isUndefined(is_error) || is_error) {
            target.addClass('alert-danger');
        } else {
            target.addClass('alert-success');
        }
        target.html(message);
        target.show();
    },

    removeMessage: function (target) {
        target.html('');
        target.removeClass('alert-danger');
        target.removeClass('alert-success');
        target.hide();
    },

    openInNewWindow: function (url) {
        var win = window.open(url, '_blank');
        if (win) {
            win.focus();
        }
    },

    toTitleCase: function (str) {
        return str.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }
};

