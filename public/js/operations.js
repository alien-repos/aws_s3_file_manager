//////////////////// OPERATIONS ///////////////////////
/// UPLOAD
/// DOWNLOAD
/// DELETE
/**
 * [upload description]
 * @param  {[type]} formScope [description]
 * @return {[type]}           [description]
 */
function upload(formScope) {
    // Retrive input fields files to variables
    var uploadFiles = $('#choosen-files')[0].files;
    // Create a form instance
    var formData = new FormData(document.getElementsByName('uploadS3Files')[0]);
    var count = total = uploadFiles.length;
    index = 0;
    multiUploadAjax(count, total, index, formData, uploadFiles);
}

/**
 * [multiUploadAjax description]
 * @param  {[type]} count       [description]
 * @param  {[type]} total       [description]
 * @param  {[type]} index       [description]
 * @param  {[type]} formData    [description]
 * @param  {[type]} uploadFiles [description]
 * @return {[type]}             [description]
 */
function multiUploadAjax(count, total, index, formData, uploadFiles) {
    formData.append('image', uploadFiles[index]);
    progress = (index / total) * 100;
    s3path = '';
    response = ajaxOps('upload', s3path);
    if (response.state === true) {
        progressBar(index, response['responseData'], progress);
        // if(count == 0) return true;
        if (index == total) {
            createView(); // Refresh
            return true;
        }
        index++;
        count--;
        // Function recursion
        multiUploadAjax(count, total, index, formData, uploadFiles);
    }
}

// (Download/Delete) Files in S3
/**
 * [downloadDeleteOps description]
 * @param  {[type]} routeToCall [description]
 * @return {[type]}             [description]
 */
function downloadDeleteOps(routeToCall) {
    // Collect all filenames to be deleted/download
    urlsArray = [];
    $('.selected > img').each(function() {
        sourceUrl = $(this).attr('src');
        urlsArray.push(sourceUrl);
    });
    // Make an AJAX call with list of file names
    response = ajaxOps('upload', JSON.stringify(urlsArray));
    if (response.state === true) {
        if (routeToCall === 'download') downloadAsBlob(JSON.parse(response[responseData]));
        if (routeToCall === 'delete') refresh();
    }
}

/**
 * [downloadAsBlob description]
 * @param  {[type]} urlArray [description]
 * @return {[type]}          [description]
 */
function downloadAsBlob(urlArray) {
    // var xhr = new XMLHttpRequest();  
    // Create route parameter from http url
    $.each(urlArray, function(key, url) {
        encodedUrl = url.replace(/\//g, ";");
        routeUrl = baseUrl + '/downloadItem/' + encodedUrl;
        window.open(routeUrl, '_blank');
    });
}