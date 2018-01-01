<!DOCTYPE html>
<html lang="en">
<head>
  <title>My Cloud</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  


  <style type="text/css">
    
    .list_view {
      list-style: none;
    }
  </style>
</head>
<body style="background: ">

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#"><span class="glyphicon glyphicon-cloud"></span> My Cloud</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="#">Page 1</a></li>
      <li><a href="#">Page 2</a></li>
      <li><a href="#">Page 3</a></li>
    </ul>
  </div>
</nav>
  
<div class="" style="margin-top:10px">
<!-- LEFT COLUMN -->
        <div class="col-md-2" style="border-right: 1px #ccc solid">
          
          <select class="form-control" id='bucket_container'>

        </select>
<br>
        <button id='load_bucket' class="btn btn-info">Load Files from Bucket</button>

        </div>

<!-- RIGHT COLUMN -->
        <div class="col-md-10">
         <ol class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li><a href="#">Private</a></li>
    <li><a href="#">Pictures</a></li>
    <li class="active">Vacation</li>        
  </ol>

  <br>
          <div id='file_container'></div>
        </div>
  </div>
</div>

<!-- post page load scripts -->
<script src="{{asset('js/home.js')}}"></script>
</body>
</html>
