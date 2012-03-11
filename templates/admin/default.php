<!DOCTYPE html>
<html>
	<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo Settings::$path;?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script type="text/javascript" src="<?php echo Settings::$path;?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/admin/css/conman.css"/>
</head>
<body>
	<div id="content">
		<div id="content_top">
		</div>
		<div id="content_content">
		</div>
		
		
	</div>
	<div class="head">
		<h1>Conman</h1>
		<div class="login">
			Välkommen <?php $user = Auth::user(true); echo $user['username'];?> <a href="<?php echo Router::url('/index/logout');?>" class="button">Logga ut</a>
		</div>
		<ul class="menu">
			<li class="active"><a href="<?php echo Router::url('index');?>">Ordrar</a></li>
			<li><a href="<?php echo Router::url('/entrance');?>">Entré</a></li>
			<li><a href="<?php echo Router::url('members');?>">Medlemmar</a></li>
			<li><a href="<?php echo Router::url('typemembers');?>">Medlemmar efter köp</a></li>
		</ul>
	</div>
	<div class="content">
			<?php
				ErrorHelper::print_errors();
				if(isset($con))
					$con->render();
			?>
	</div>
</body>
</html>
