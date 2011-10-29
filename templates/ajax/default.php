<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
<body>
			<?php
				ErrorHelper::print_errors();
				if(isset($con))
					$con->render();
			?>
</body>
</html>
