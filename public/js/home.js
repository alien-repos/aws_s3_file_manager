// update buckets
$.get("/bucket/all", function(data, status) {
    var html = '';
    data = JSON.parse(data);
    // console.log(data.all_buckets);
    $.each(data.all_buckets, function(key, val) {
        html += '<option value ="' + val.bucket + '">' + val.bucket + '</option>';
    });
    // console.log(html);

    $('#bucket_container').html(html);
});

$('#load_bucket').click(function() {
    var bucket = $('#bucket_container').val();
    getAllObjectsFromBucket(bucket, 'dev_box/local/');
});

function getAllObjectsFromBucket(bucket, filePath) {
    alert('dfsdfsf');
    $.post("/files", {
        bucket: bucket,
        path: filePath
    }, function(data, status) {
        console.log(data);
        var html = '<ul class="list_view">';
        data = JSON.parse(data);
        // console.log(data.all_keys);
        $.each(data.urls.files, function(key, val) {
            html += '<li><span class="glyphicon glyphicon-folder-close" style="color:#e6c66a !important"></span> <a ondblclick="onFileClick()" href="#" data-bucket="' + bucket + '" data-url="' + data.urls.curernt_path + val + '" style="color:#000 !important" id="file" >' + val + '</a></li>';
        });
        html += '</ul>';
        $('#file_container').html(html);
    });
}


function onFileClick() {
    // event.preventDefault();
    var bucket = $('#file').data("bucket");
    var filePath = $('#file').data("url");
    getAllObjectsFromBucket(bucket, filePath);
}