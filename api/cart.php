<?php

// api/Cart
//
// Cart api

function get_cart() {
	$cart = get_in($_SESSION, 'cart');
	if(!$cart) {
		set_cart([]);
		return [];
	}

	return $cart;
}

function set_cart($cart) {
	$_SESSION['cart'] = $cart;
}

function add_to_cart($product) {
	$cart = get_cart();
	$pid = $product['id'];

	if(isset($cart[$pid])) {
		$cart[$pid]['qty']++;
		
	}else{
		$cart[$pid] = ['product'=>$product, 'qty'=>1];
	}

	set_cart($cart);
}

function update_cart_quantities($nqtys) {
	$cart = get_cart();
	foreach ($nqtys as $pid => $nval) {
		$pid = (int) $pid;

		if(isset($cart[$pid])) {
			$cart[$pid]['qty'] = $nval;
		}else{
			add_error("Tried to update quantity of entity not in cart: $pid");
		}
	}

	set_cart($cart);
}

?>