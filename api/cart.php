<?php

// api/Cart
//
// Cart api

require_once("init.php");

function get_cart() {
	// Try to get cart from session
	$cart = get_in($_SESSION, 'cart');

	// If it wasn't found, set it to 
	//	an empty array and return
	if(!$cart) {
		set_cart([]);
		return [];
	}

	// Else return the cart
	return $cart;
}

function set_cart($cart) {
	// Save the state of the cart in the session
	$_SESSION['cart'] = $cart;
}

function add_to_cart($product) {
	$cart = get_cart();
	$pid = $product['id'];

	// If the product exists in the cart already
	if(isset($cart[$pid])) {
		// Just increment its quantity
		$cart[$pid]['qty']++;
		
	}else{
		// Otherwise create a new pair
		$cart[$pid] = ['product'=>$product, 'qty'=>1];
	}

	// Save cart state
	set_cart($cart);
}

// Sum the products of all items in cart and
//	their quantities
function calculate_cart_total() {
	$total = 0;

	foreach (get_cart() as $v) {
		$total += $v['product']['price'] * $v['qty'];
	}

	return $total;
}

function get_qty_in_cart($pid) {
	// Try to find product in cart
	$v = get_in(get_cart(), $pid);

	// If it doesn't exist in the cart
	//	it's quantity is zero so return that
	if(!$v) return 0;

	// Otherwise, return its actual quantity
	return $v['qty'];
}

function update_cart_quantities($nqtys) {
	$cart = get_cart();

	// For each item in $nqtys
	foreach ($nqtys as $pid => $nval) {
		$pid = (int) $pid;

		// If product exists in cart
		if(isset($cart[$pid])) {
			// and the value in $nqtys is > 0
			if($nval > 0){
				// Update the products quantity
				$cart[$pid]['qty'] = $nval;
			}else{
				// Otherwise remove it from the cart
				unset($cart[$pid]);
			}
		}else{
			// Otherwise it's an error
			add_error("Tried to update quantity of entity not in cart: $pid");
		}
	}

	// Save new cart state
	set_cart($cart);
}

?>