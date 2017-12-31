
<!DOCTYPE html>
<html lang="en">

<head>
  <title>S3 Module</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="{{ URL::asset('js/util.js') }}"></script>
  <link rel="stylesheet" href="{{ URL::asset('css/admin-styles.css') }}">
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
                    <span class="glyphicon glyphicon-king"></span>&nbsp;
                    Admininstrator
                </a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#" id="upload_link">Config</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

<!-- <div class="container"> -->
<div class="row">
  <div class="col-md-2">
  <ul class="nav-list-admin">
  <li><button class="btn btn-primary">Cloud settings</button></li>
  <li><button class="btn btn-primary">Cloud settings</button></li>
  <li><button class="btn btn-primary">Cloud settings</button></li>
  <li><button class="btn btn-primary">Cloud settings</button></li>
  <li><button class="btn btn-primary">Cloud settings</button></li>
  <li><button class="btn btn-primary">Cloud settings</button></li>
  <li><button class="btn btn-primary">Cloud settings</button></li>
  </ul>
  </div>

  <div class="col-md-10">
    <div id="admin-panel">
      <h2>Admininstrator</h2>
      <p>Configure Settings<p>
      <hr>
       <p><img src="{{ URL::asset('images/cloud-icon.png') }}" class="cloud-icon" /><h2> Bucket Configuration</h2></p>
      <form>
        <div class="form-group">
        Bucket Name:
        <input type="text" id="bucket_name" class="form-control" value="{{ \Config::get('s3.bucketname') }}"><br>
        </div>
        <div class="form-group">
        <p><span class='glyphicon glyphicon-lock'></span> Access Key:</p>
        <input type="text" name="access_key" class="form-control" value="{{ \Config::get('s3.access_key') }}"><br>
        </div>
        <div class="form-group">
        <p><span class='glyphicon glyphicon-lock'></span> Secret Access Key:</p>
        <input type="text" name="bucket_name" class="form-control" value="{{ \Config::get('s3.secret_access_key') }}"><br>
        </div>
        <div class="form-group">
        <p><span class='glyphicon glyphicon-folder-open'></span> Root Folder Name:</p>
        <input type="text" name="bucket_name" class="form-control" value="{{ \Config::get('s3.root_folder') }}"><br><br>
        </div>
        <button id="update-bkt" class="btn btn-primary">Update</button>
      </form>
      <hr>
      <form>
      user settings: <br>
      <div class="form-group">
      Select User
      <select>
      <option value="volvo">User 1</option>
      <option value="saab">User 2</option>
      <option value="opel">User 3</option>
      <option value="audi">User 4</option>
      </select>
      </div>
      <div class="form-group">
      <input type="radio" name="gender" value="zip"> Enable &nbsp;
      <input type="radio" name="gender" value="zip"> Disable<br>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
      </form>
      <hr>
      permission settings
      <hr>
      <p>Configure Upload</p>
      <form>
        <div class="form-group">
        Uploads Directory Path :
        <input type="text" name="bucket_name" class="form-control" value="{{ \Config::get('s3.upload_store') }}">
        </div>
        <div class="form-group">
        Permissions :<br>
        <input type="checkbox" name="vehicle" value="Bike">Read<br>
        </div>
        <div class="form-group">
        <input type="checkbox" name="vehicle" value="Car" checked="checked">Write<br>
        </div>
        <div class="form-group">
        <input type="checkbox" name="vehicle" value="Car" checked="checked">Execute<br>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
      <hr>
      <p>Configure Download</p>
      <form>
        <div class="form-group">
        User Download Type:<br>
        <input type="radio" name="gender" value="multiple" checked> Multiple &nbsp;&nbsp;&nbsp;
        <input type="radio" name="gender" value="zip"> Zip &nbsp;&nbsp;&nbsp;
        <input type="radio" name="gender" value="zip"> Disable<br>
        </div>
        
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
      <hr>
     
      </div>
    </div>
    </div>
<!-- </div> -->
<script>
  // $(document).ready(function(){
  $('#update-bkt').click(function() {
    param = {};
    param.bucketname = $('#bucket_name').val();
    ajaxOps('update/Bucket', param);
  });
  // });
</script>
</body>

</html>
