///// GLOBAL CONFIG /////////////////////
var baseUrl = window.location.origin + '/';
var param = {};
/////////////////////////////////////////

/**
 * [ajaxOps description]
 * @param  {[type]} routeToCall [description]
 * @return {[type]}             [description]
 */
function ajaxOps(routeToCall, recievedValues) {
console.log(routeToCall, recievedValues);
    param.values = recievedValues;
    $.ajax({
        url: baseUrl + routeToCall,
        type: "POST",
        async: false,
        cache: false,
        data: param,
        success: function(response) {
            showMessage(response.message);
            return {
                state: true,
                responseData: response
            };
        },
        error: function(e) {
            showMessage(e);
            return {
                state: false,
                responseData: e
            };
        },
        complete: function() {
            // return {status:'completed', responseData:e};
            // CompleteOps;
        }
    });
}

/**
 * [showMessage description]
 * @param  {[type]} response [description]
 * @return {[type]}          [description]
 */
function showMessage(response) {
    if (response.state) {
        alert(response.message);
    } else {
        alert(response.message);
    }
}

/**
 * [basename description]
 * @param  {[type]} path [description]
 * @return {[type]}      [description]
 */
function basename(path) {
    temp = path.split('/');
    fileName = temp[temp.length - 1];
    if (fileName.length == 0) fileName = temp[temp.length - 2];
    return fileName;
}

/**
 * [isDir description]
 * @param  {[type]}  path [description]
 * @return {Boolean}      [description]
 */
function isDir(path) {
    result = path.search(".");
    // alert(result);
    if (result < 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * [stripTrailingSlash description]
 * @param  {[type]} str [description]
 * @return {[type]}     [description]
 */
function stripTrailingSlash(str) {
    if (str.endsWith('/')) {
        return str.slice(0, -1);
    }
    return str;
}