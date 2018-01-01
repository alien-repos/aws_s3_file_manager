<?php


<<<<<<< HEAD

/*****************************
    MODULE:1 - CRUD
*****************************/
// // create Operation
// Route::get('/create', 'CRUDController@create');
// // Read Operation
// Route::get('/read', 'CRUDController@read');
// // Update Operation
// Route::get('/update', 'CRUDController@update');
// // Delete Operation
// Route::get('/delete', 'CRUDController@delete');

/*****************************
    MODULE:2 - DASHBOARDS
*****************************/
// File Manager
Route::get('/', function () {
     echo "Hello world";
});

//Admin Panel
Route::get('/admin', function () {
    return view('admin-panel');
});

/*****************************
    MODULE:3 - FILE MANAGER
*****************************/
// Upload
<<<<<<< HEAD
Route::get('/upload', 'S3Controller@upload');

// Download
Route::get('/download', 'S3Controller@download');
=======
Route::post('/upload', 'S3Controller@upload');

// Download
Route::post('/download', 'S3Controller@download');
>>>>>>> eb1892b... copied files to new repo
// Downloading trhough get request
Route::get('/downloadItem/{urlKey}', 'S3Controller@downloadStream');

// Delete
<<<<<<< HEAD
Route::get('/delete', 'S3Controller@delete');

// Thumbnail files generation
Route::get('/genThumbs', 'S3Controller@genThumbs');
// List files generation
Route::get('/genLists', 'S3Controller@genLists');

///////// ADMIN PANEL CONFIGURATION CANCEL ///////
Route::get('/{updateBucket}', 'S3Controller@updateConfig');
Route::get('/{updateAccessKey}', 'S3Controller@updateConfig');
Route::get('/{updatesecretAccessKey}', 'S3Controller@updateConfig');
Route::get('/{updaterootFolderName}', 'S3Controller@updateConfig');
=======
Route::post('/delete', 'S3Controller@delete');

// Thumbnail files generation
Route::post('/genThumbs', 'S3Controller@genThumbs');
// List files generation
Route::post('/genLists', 'S3Controller@genLists');

///////// ADMIN PANEL CONFIGURATION CANCEL ///////
Route::post('/{updateBucket}',          'S3Controller@updateConfig');
Route::post('/{updateAccessKey}',       'S3Controller@updateConfig');
Route::post('/{updatesecretAccessKey}', 'S3Controller@updateConfig');
Route::post('/{updaterootFolderName}',  'S3Controller@updateConfig');
//////////////////////////////////////////////////


Route::GET('/laravelRequest', 'RequestController@laravelRequest');
>>>>>>> eb1892b... copied files to new repo
=======
Route::get('/', 'DashboardController@index');

Route::get('bucket/all', 'BucketController@getAllBuckets');

Route::post('/files', 'KeyController@getAllKeysInPath');
>>>>>>> b1dc9dc... added choosing and loading buckets and items, corrected one way file navigation in list view, removed junk code
