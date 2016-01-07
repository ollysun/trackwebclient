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

    showErrorMessage: function (target, message) {
        this.removeErrorMessage(target);
        if (!target.hasClass('alert')) {
            target.addClass('alert');
        }
        target.addClass('alert-danger');
        target.html(message);
        target.show();
    },

    removeErrorMessage: function (target) {
        target.html('');
        target.removeClass('alert-danger');
        target.hide();
    }
};

