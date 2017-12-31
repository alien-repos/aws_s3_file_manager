<!DOCTYPE html>
<html lang="en">

<head>
<title>S3 Module</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  
  <!-- Autoload Scripts -->
  <?php
    $handle = opendir("js");
    while (($file = readdir($handle))!==false) {
            if($file === '.' || $file === '..') continue;
            echo '<script type="text/javascript" src="'. URL::asset('js/'.$file) .'"></script>';
    }
    closedir($handle);
?>

<link rel="stylesheet" href="{{ URL::asset('css/s3-file-manager-styles.css') }}">
<style>
/* Set black background color, white text and some padding */
footer {
  background-color: #555;
  color: white;
  padding: 15px;
}
</style>
</head>

<body>
<div id="upload-menu">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="col-md-6">
<input type="file" name="file_list" class="ticket-image-upload-input" id="choosen-files" multiple>
    <form method="post" name="uploadS3Files" id="upload-s3-files" enctype='multipart/form-data'>

        <h3><span class="glyphicon glyphicon-upload"></span>&nbsp;Upload Files to S3</h3>
        <div class="form-group">
          <div class="progress">
    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id='s3-upload-progress' style="width:0%">
      0%
    </div>
  </div>
        </div>

        <div class="form-group" id='file-name'>

        </div>

        <div class="form-group">
        <input type="submit" class="btn btn-primary" id="s3-upload-files" value="Upload to S3 Bucket">&nbsp;&nbsp;
        <button type="reset" class="btn btn-default" id='clear'>Clear</button>
        </div>
    </form>
</div>

<div class="col-md-6">
    <div id="acl-form">
    <h3><span class="glyphicon glyphicon-lock"></span>&nbsp;Manage Permissions</h3>
    <p> Change Permissions for Edited Images</p>
    <label>
    <input type="radio" name="permission" value="public">&nbsp;Public
    </label><br>
    <label>
    <input type="radio" name="permission" value="private">&nbsp;Private
    </label><br>
    <button id="change_permission" class="btn btn-primary">Update</button>
    </div>
</div>

</div>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">
                    <span class="glyphicon glyphicon-folder-open"></span>&nbsp;
                    S3 Manager
                </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#" id="upload_link">Menu</a>
                    </li>
                    <li>
                        <a href="#" id='download'>Download</a>
                    </li>
                    <li>
                        <a href="#" id='delete-btn'>Delete</a>
                    </li>
                    <li>
                        <a href="#">Settings</a>
                    </li>
                    <li>
                        <a href="#" id="refresh">Refresh</a>
                    </li>
                    <li>
                        <a id='count'></a>
                    </li>
                    <li>
                        <a id='select-count'>Selected: 0</a>
                    </li>
                    <li>
                        <a href="#" id='all' class=''>All</a>
                    </li>
                    <li>
                         
                        <a href="#" class=''>Thumbs on <input type="checkbox" id='view-type' value="thumbs" /></a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
<div class="">
<ol class="breadcrumb" style="margin-bottom: 5px;">
  <li><a href="#">Home</a></li>
  <li><a href="#">Library</a></li>
  <li class="active">Data</li>
</ol>
    <div class="col-md-1">
        <div><button class="btn btn-primary" id='back'><span class='glyphicon glyphicon-arrow-left'> Back</span></button></div>
    </div>
    <div class="col-md-11">
        <input type="text" class="form-control" placeholder="S3 key" value="fosRajkot/12-2015/" id='s3-url-bar' />
    </div>
    <div class="" id='files-holder'>
<!--         <div class="col-xs-2">
            <a href="#" class="thumbnail">
            <img src="http://placehold.it/400x200" alt="..." />
            </a>
        </div> -->
    </div>
</div>
<div id='loading'>Loading...</div>
</body>

</html>
