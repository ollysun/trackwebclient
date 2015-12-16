var ResponseHandler = function (response) {
    this.response = response;
};

ResponseHandler.prototype.isSuccess = function () {
    console.log(this.response);
    return typeof this.response != 'undefined' &&
        this.response.hasOwnProperty('status') &&
        this.response.status == 'success';
};

ResponseHandler.prototype.getData = function() {
    if(typeof this.response == 'undefined') {
        return null;
    }
    return this.response.data;
};