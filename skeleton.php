<!DOCTYPE html>
<html>
<head>
	<title>Shopping or some shit</title>
	<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>
	<?php 
		$errors = get_errors();

		if(count($errors) > 0) {
			foreach ($errors as $e) {
				echo "<h4 style='color:red'>$e</h4>";
			}

			reset_errors();
		}
	?>
	<div class='content'>
		<?php render(); ?>
	</div>
</body>
</html>