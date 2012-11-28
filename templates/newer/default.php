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
	<title><?php echo Settings::$EventName; ?></title>
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo Settings::$path;?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
        <script type="text/javascript" src="<?php echo Settings::$path;?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
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
	<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox();
		});
	</script>
  </head>
  <body>
      <div id="centerpage">
	<div id="topbar">
	  <?php if(!Auth::user()):?>
	  <a class="blue"  href="<?php echo Router::url('/index/index');?>">Har du ett konto? <h1>Logga in här</h1></a>
	  <a class="black" href="<?php echo Router::url('/index/register_start');?>">Har du inget konto? <h1>Skapa ett nu</h1></a>
		</div>
		<img src="<?php echo Settings::$path;?>templates/newer/bg.png" alt="bar" />
	  <?php else:?>
		<?php $user = Auth::user(true);?>
			<!-- <a class="blue"  href="<?php echo Router::url('/ticket/index');?>">Välkommen till ConMan<h1><?php echo $user['username'];?></h1></a> -->
		  	<div class="black"><p class="awesome_margin">Välkommen</p><h1 class="awesome_margin"><?php echo $user['username'];?></h1><p class="small_text"><a class="small_link" href="<?php echo Router::url('/ticket/index');?>">(Orderhistorik</a> / <a class="small_link" href="<?php echo Router::url('/index/logout');?>">logga ut)</a></p></div>
		</div>
		<img src="<?php echo Settings::$path;?>templates/newer/bg_login.png" alt="bar" />
	<?php endif;?>
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
