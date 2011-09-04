<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" src="<?php echo Settings::$path;?>js/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo Settings::$path;?>templates/default/css/conman.css"/>
</head>
<body>
	<div id="content">
		<div id="content_top">
		</div>
		<div id="content_content">
			<?php
				ErrorHelper::print_errors();
				if(isset($con))
					$con->render();
			?>
		</div>
	</div>
</body>
</html>
