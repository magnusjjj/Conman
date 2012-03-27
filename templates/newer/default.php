<!DOCTYPE html>
<html>
  <head>
	<meta charset="UTF-8" />
	<title>NärCon 2012</title>
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/newer/css_reset.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/newer/box_model.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/newer/style.css" />
  </head>
  <body>
	<div id="topbar">
	  <a class="blue"  href="<?php echo Router::url('/index/index');?>">Har du ett konto? <h1>Logga in här!</h1></a>
	  <a class="black" href="<?php echo Router::url('/index/register_start');?>">Har du ingen biljett ännu? <h1>Köp en nu</h1></a>
	</div>
	<img src="<?php echo Settings::$path;?>templates/newer/bg.png" alt="bar" />
	<!-- Start Contentbox -->
	<div id="content">
		<?php  
			ErrorHelper::print_errors();
			if(isset($con))
	                	 $con->render();
		?>
	</div>
	<!-- End Contentbox -->
  </body>
</html>
