<?php

// ProductDetails
//
// Displays a description of, the price of and the option to
//	add to cart a product

require_once('api/init.php');
require_once('api/cart.php');
require_once('api/comment.php');
require_once('api/product.php');

$product = null;
$comments = null;

function process() {
	global $product;
	global $comments;

	// Try and get the id from $_GET
	$pid = (int) get_in($_GET, 'id', -1);
	if($pid === -1) {
		// If it wasn't available, bail
		add_error("Missing product id");
		return false;
	}

	// Then try to get product with that id
	$product = get_product($pid);
	if(!$product){
		// If it doesn't exist, bail
		add_error("Invalid product");
		return false;
	}

	// Try to get comments for product $pid
	$comments = get_in(get_comments(), $pid, []);

	// Reverse chronological sort through the comments
	usort($comments, function($c1, $c2) {
		return ($c1['timestamp'] > $c2['timestamp']) ? -1 : 1;
	});

	return true;
}

function render() {
	global $product;

	// Don't bother rendering product details
	//	of a null product. Should never get here
	if(is_null($product)) {
		echo "<p>Null product!</p>";
		return;
	}

	// Get quantity of $product in cart
	$qty = get_qty_in_cart($product['id']);

	// Render thumbnail
	echo "<figure class='right'><img src='img/${product['img']}' alt='${product['name']}'></img></figure>";

	// Render other details
	echo "<h3>${product['name']}</h3>";
	echo "<p>${product['description']}</p>";
	echo "<p>Product price: $${product['price']}</p>";

	// Render Add to Cart button 
	echo "<button id='addtocart'>Add to cart, yo!</button>";
	echo "<span style='padding-left: 5px'>In cart: $qty</span>";

	?><script>
		// When Add to Cart button is clicked
		$('#addtocart').click(function(e) {
			var $this = $(this);

			// Post request
			$.post('api/action.php', {
				action: 'addtocart',
				item: <?php echo $product['id'];?>

			}).success(function() {
				// Go to cart page
				window.location.href = 'cartview.php';
			});
		});
	</script><?php

	render_comments();
}

function render_comments() {
	global $product;
	global $comments;

	$prodID = $product['id'];
	?>
		<h3>Comments</h3>
		<div class='commententry'>
			<form action='api/action.php' method='POST'>
				<input type='hidden' name='action' value='addcomment'/>
				<input type='hidden' name='item' value='<?php echo $prodID; ?>'/>

				<label>Name</label>
				<input type='text' name='name'/>
				<br/>
				
				<label>Email</label>
				<input type='email' name='email'/>
				<br/>

				<div>
					<div class='moveoverpls'></div>
					<textarea name='comment'></textarea>
					<button type='submit'>Comment</button>
				</div>
			</form>
		</div>
		<div class='clear'></div>
	<?php

	// Render each existing comment
	foreach ($comments as $key => $value) {
		// Format timestamp and convert to users local timezone
		//	if the timezone hasn't been set, assume +10
		$timestr = gmdate("F jS, Y \a\\t g:i a", 
			$value['timestamp'] + get_in($_SESSION, 'timezone', 10) * 60 * 60);

		echo "<div class='comment'>";
		echo "<h4>${value['name']} <small>$timestr</small></h4>";

		// Don't try to render the email if there isn't one
		if(isset($value['email']) && $value['email'] !== '') {
			echo "<a href='mailto:${value['email']}'>${value['email']}</a>";
		}

		echo "<p>${value['comment']}</p>";
		echo "</div>";
	};

	?><script>
		// When keys are pressed in the comment box
		$(".commententry textarea[name='comment']").keypress(function(e){
			e = e || event;

			// Check if the key was 'enter' and if ctrl or shift is pressed
			if (e.keyCode === 13 && (e.ctrlKey || e.shiftKey)) {
				// If so, submit the comment
				$(".commententry form").submit();
				return false;
			}

			// Otherwise continue as per usual
			return true;
		});

		// When a comment is submitted
		$(".commententry form").on('submit', function(e){
			e = e || event;

			// Ensure that both the name and comment fields are valid
			//	Note: I'm treating email as optional
			var $name = $(".commententry input[name='name']");
			var $comment = $(".commententry textarea[name='comment']");

			if($name.val() === '') {
				return false;
			}else if($comment.val() === ''){
				return false;
			}

			return true;
		});
	</script><?php
}

// If processing fails, return to product list
if(!process()) {
	header("Location: index.php");
	die;
}

require('skeleton.php');

?>