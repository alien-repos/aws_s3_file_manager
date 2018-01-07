<!DOCTYPE html>
<html lang="en">
<head>
  <title style="color:#7cc2dd">My Cloud</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  


  <style type="text/css">
    
    .settings_list {
      list-style: none;
      
    }

    .settings_list > li {
      padding-top: 10px;
    }
  </style>
</head>
<body style="background: ">

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#"><span class="glyphicon glyphicon-cloud" style="color:#7cc2dd"></span> My Cloud</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="{{ route('home') }}">Home</a></li>
      <li class="active"><a href="#">Settings</a></li>
      <!-- <li><a href="#">Page 2</a></li> -->
      <!-- <li><a href="#">Page 3</a></li> -->
    </ul>
  </div>
</nav>
  
<div class="container">
<div class="row">
  <div class="col-md-6">
    
<h2><span class="glyphicon glyphicon-wrench"></span> Settings </h2>
<hr>
<form>
<ul class="settings_list">
  <li><input type="text" name="a_key" class="form-control" value="{{ Config::get('aws.credentials.key') }}"></li>
  <li><input type="text" name="s_key" class="form-control" value="{{ Config::get('aws.credentials.secret') }}"></li>
  <li><input type="text" name="region" class="form-control" value="{{ Config::get('aws.region') }}"></li>
  <li><input type="text" name="version" class="form-control" value="{{ Config::get('aws.version') }}"></li>
  <li><button class="btn-primary btn">Save</button></li>
</ul>
</form>
  </div>
</div>
</div>


<!-- post page load scripts -->
<script src="{{asset('js/settings.js')}}"></script>

</body>
</html>
