<?php

// ProductDetails
//
// Displays a description of, the price of and the option to
//	add to cart a product

require('init.php');
require('api/product.php');

$product = null;

function render() {
	global $product;
	if(is_null($product)) {
		echo "<p>Null product!</p>";
		return;
	}

	echo "<p>Product name: ${product['name']}</p>";
	echo "<p>Product price: ${product['price']}</p>";
	echo "<form action='action/addtocart.php' method='GET'>";
		echo "<input type='hidden' name='item' value='${product['id']}'/>";
		echo "<button type='submit'>Add to cart, yo!</button>";
	echo "</form>";
}

function process() {
	global $product;

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
}

process();

require('skeleton.php');

?>