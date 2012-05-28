<!DOCTYPE html>
<html>
  <head>
	
	<!-- google analytics -->
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-30461411-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
	
	<meta charset="UTF-8" />
	<title>NärCon 2012</title>
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/newer/css_reset.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/newer/box_model.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/newer/style.css" />
	<script>
	function googleTranslateElementInit() {
	  new google.translate.TranslateElement({
	    pageLanguage: 'sv',
	    floatPosition: google.translate.TranslateElement.FloatPosition.BOTTOM_RIGHT
	  });
	}
	</script><script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  </head>
  <body>
      <div id="centerpage">
	<div id="topbar">
	  <?php if(!Auth::user()):?>
	  <a class="blue"  href="<?php echo Router::url('/index/index');?>">Har du ett konto? <h1>Logga in här!</h1></a>
	  <a class="black" href="<?php echo Router::url('/index/register_start');?>">Har du ingen biljett ännu? <h1>Köp en nu</h1></a>
	  <?php else:?>
		<?php $user = Auth::user(true);?>
		<div class="black">Välkommen <h1><?php echo $user['username'];?>&nbsp;<a href="<?php echo Router::url('/index/logout');?>" style="font-size: 0.5em">(logga ut)</a></h1></div>
	  <?php endif;?>
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
      </div>
  </body>
</html>
