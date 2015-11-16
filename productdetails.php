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

function render() {
	global $product;
	if(is_null($product)) {
		echo "<p>Null product!</p>";
		return;
	}

	$qty = get_qty_in_cart($product['id']);

	echo "<p>Product name: ${product['name']}</p>";
	echo "<p>Product price: ${product['price']}</p>";
	echo "<p>Product description: ${product['description']}</p>";
	echo "<form action='api/action.php' method='POST'>";
		echo "<input type='hidden' name='action' value='addtocart'/>";
		echo "<input type='hidden' name='item' value='${product['id']}'/>";
		
		echo "<button type='submit'>Add to cart, yo!</button>";
		echo "<span style='padding-left: 5px'>In cart: $qty</span>";
	echo "</form>";

	render_comments();
}

function render_comments() {
	global $product;
	global $comments;

	$prodID = $product['id'];

	echo "<h3>Comments</h3>";
	?>
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

			<span>
				<div class='moveoverpls'></div>
				<textarea name='comment'></textarea>
				<button type='submit'>Comment</button>
			</span>
		</form>
	</div>
	<div class='clear'></div>
	<?php
	foreach ($comments as $key => $value) {
		$timestr = date("F jS, Y \a\\t g:i a", $value['timestamp']);

		echo "<div class='comment'>";
		echo "<h4>${value['name']} <small>$timestr</small></h4>";

		if(isset($value['email'])) {
			echo "<a href='mailto:${value['email']}'>${value['email']}</a>";
		}

		echo "<p>${value['comment']}</p>";
		echo "</div>";
	};
}

function process() {
	global $product;
	global $comments;

	$pid = (int) get_in($_GET, 'id', -1);
	if($pid === -1) {
		add_error("Missing product id");
		return;
	}

	$product = get_product($pid);
	if(!$product){
		add_error("Invalid product");
		return;
	}

	$comments = get_in(get_comments(), $pid, []);

	// Reverse chronological sort
	usort($comments, function($c1, $c2) {
		return ($c1['timestamp'] > $c2['timestamp']) ? -1 : 1;
	});
}

process();

require('skeleton.php');

?>