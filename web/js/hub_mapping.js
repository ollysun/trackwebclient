/**
 * @credits http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values-in-javascript
 * @param name
 * @returns {string}
 */
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}


$(document).ready(function () {
    var to_id = getParameterByName('to');
    if (to_id != "") {
        $("tr[data-hub_id=" + "'" + to_id + "']").css('background-color', '#ff0');
    }
});
