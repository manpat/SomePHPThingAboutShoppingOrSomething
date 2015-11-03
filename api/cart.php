<?php

// api/Cart
//
// Cart api

function get_cart() {
	$cart = get_in($_SESSION, 'cart');
	if(!$cart) {
		$cart = [];
		$_SESSION['cart'] = $cart;
	}

	return $cart;
}

function add_to_cart($product) {
	
}

?>