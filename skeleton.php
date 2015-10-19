<!DOCTYPE html>
<html>
<head>
	<title>Shopping or some shit</title>
	<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>
	<?php 
		if(!is_null($error)){
			echo "<h4 style='color:red'>$error</h4>";
		}
	?>
	<div class='content'>
		<?php render(); ?>
	</div>
</body>
</html>