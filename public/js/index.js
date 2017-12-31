// /* Javascript file for handling 
//  *
//  * AWS S3 operations
//  * Folder Structure for s3 manager
// GLOBAL VARIABLES /////////////////////
var notSelected = true;
var viewType = 'genLists';
/////////////////////////////////////////

// DOCUMENT READY EVENTS
$(document).ready(function() {
    // Slide menu
    $('#upload_link').click(function() {
        $("#upload-menu").slideToggle();
    });
    // Delete
    $('#delete-btn').click(function() {
        downloadDeleteOps('delete');
    });
    // Download
    $('#download').click(function() {
        downloadDeleteOps('download');
    });
    // Upload files
    $('#upload-s3-files').on('submit', function(event) {
        event.preventDefault();
        upload(this);
    });
    // View files
    createView();
    // Refresh
    $('#refresh').click(function() {
        refresh();
    });
    $('#all').click(function() {
        toggleSelect();
    });
});

/**
 * [initUtilOps description]
 * @return {[type]} [description]
 */
function initUtilOps() {
    // Select De-select feature
    $('.item').click(function() {
        $(this).toggleClass('selected');
        // Count the no. of selected items
        selectCount();
    });
}