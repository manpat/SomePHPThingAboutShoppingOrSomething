<?php

// AddToCart
//
// Adds item $_GET['item'] to cart and immediately
//	redirects back to ProductDetails 

require_once('init.php');
require_once('api/cart.php');
require_once('api/product.php');

$prodID = null;

function process() {
	global $prodID;
	$prodID = get_in($_GET, 'item');

	$product = get_product($prodID);
	if(!$product) {
		add_error("Invalid product: $prodID");
		return;
	}

	add_to_cart($product);
}

process();

header("Location: productdetails.php?id=$prodID");

?>