<!DOCTYPE html>
<html>
<head>
	<title>Shopping or some shit</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="js/jquery-2.1.4.min.js"></script>

	<script>
	$(document).ready(function() {
		// If the sessions timezone hasn't been set, do that
		if("<?php echo get_in($_SESSION,'timezone',''); ?>".length === 0) {
			var tz = new Date().getTimezoneOffset() / -60;
			$.post("api/action.php", {action:'settimezone', timezone:tz}, function() {
				location.reload();
			});
		}
	});
	</script>
</head>
<body>
	<?php
		require("navbar.php");

		$errors = get_errors();

		if(count($errors) > 0) {
			foreach ($errors as $e) {
				echo "<div class='error'>$e</div>";
			}

			reset_errors();
		}
	?>
	<section>
		<?php render(); ?>
	</section>
</body>
</html>