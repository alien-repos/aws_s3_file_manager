//////////////////// VIEW TYPES ///////////////////////
/**
 * [createView description]
 * @return {[type]} [description]
 */
function createView() {
    $('#loading').show();
    if ($("#view-type").prop('checked') == true) {
        var viewType = 'genThumbs'
    } else {
        var viewType = 'genLists'
    }
    s3path = $('#s3-url-bar').val();
    console.log(s3path);
    response = ajaxOps(viewType, s3path); // AJAX ops
    // Success operations
    console.log(response);
    if (response.state === true) {
        urlKeys = response;
        if (viewType == 'genThumbs') {
            createThumbnails(urlKeys);
        } else {
            createLists(urlKeys);
        }
        initUtilOps();
    }
}

/**
 * [createThumbnails description]
 * @param  {[type]} urlKeys [description]
 * @return {[type]}         [description]
 */
function createThumbnails(urlKeys) {
    var thumbsHtml = '';
    var count = 0;
    thumbsHtml += '<ul class="image_holder_list">';
    $.each(urlKeys, function(key, sourceUrl) {
        console.log(sourceUrl);
        fileName = sourceUrl.split('/');
        fileName = fileName[fileName.length - 1];
        altName = '';
        thumbsHtml += '<li class="item"><img src="' + sourceUrl + '" title="' + fileName + '" class="thumb_image" />';
        thumbsHtml += '<div><p>' + fileName + '</p></div></li>';
        count++;
    });
    thumbsHtml += '</ul>';

    $('#count').html('Items: ' + count);
    if (count == 0) thumbsHtml = '<h3 style="text-align:center">Directory is empty</h3>';
    $('#files-holder').html(thumbsHtml);
}

/**
 * [createLists description]
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
function createLists(data) {
    var thumbsHtml = '';
    var count = 0;
    thumbsHtml += '<ul class="list-view">';
    $.each(data, function(key, sourceUrl) {
        console.log(sourceUrl);
        ///////////// file chkr //////////
        fileName = basename(sourceUrl);
        iconUrl = '<span class="glyphicon glyphicon-file">';
        if (isDir(fileName)) {
            iconUrl = '<span class="glyphicon glyphicon-folder-close">';
        }
        /////////////////////////////////
        altName = '';
        thumbsHtml += '<li>' + iconUrl + ' <a class="s3-key" href="#" data-s3-key="' + sourceUrl + '">' + fileName + '</a></li>';
        // thumbsHtml += '<div><p>'+fileName+'</p></div></li>';
        count++;
    });
    thumbsHtml += '</ul>';

    $('#count').html('Items: ' + count);
    if (count == 0) thumbsHtml = '<h3 style="text-align:center">Directory is empty</h3>';
    $('#files-holder').html(thumbsHtml);
    gotoPath();
    goBack();
}

/**
 * [progressBar description]
 * @param  {[type]} index    [description]
 * @param  {[type]} response [description]
 * @param  {[type]} progress [description]
 * @return {[type]}          [description]
 */
function progressBar(index, response, progress) {
    $('#file-name').html('Uploaded file : ' + index + ' : ' + response)
    $('#s3-upload-progress').attr('style', 'width: ' + progress + '%');
    $('#s3-upload-progress').attr('aria-valuenow', Math.round(progress));
    $('#s3-upload-progress').html(Math.round(progress) + '%');
}

function dynamicLoadImages() {

}

/**
 * [refresh description]
 * @return {[type]} [description]
 */
function refresh() {
    createView();
}

/**
 * [selectCount description]
 * @return {[type]} [description]
 */
function selectCount() {
    selectItems = $('.selected').length;
    $('#select-count').html('Selected : ' + selectItems);
}

/**
 * [toggleSelect description]
 * @return {[type]} [description]
 */
function toggleSelect() {
    if (notSelected) {
        // Select
        $('.item').addClass('selected');
        notSelected = false;
    } else {
        // Deselect
        $('.item').removeClass('selected');
        notSelected = true;
    }
    // Count the no. of selected items
    selectCount();
}

/**
 * [gotoPath description]
 * @return {[type]} [description]
 */
function gotoPath() {
    $('.s3-key').click(function() {
        path = $(this).data('s3-key');
        $('#s3-url-bar').val(path);
        createView();
    });
}

/**
 * [goBack description]
 * @return {[type]} [description]
 */
function goBack() {
    $('#back').click(function() {
        path = $('#s3-url-bar').val();
        path = stripTrailingSlash(path);
        path = path.replace(basename(path), '');
        $('#s3-url-bar').val(path);
        createView();
    });
}