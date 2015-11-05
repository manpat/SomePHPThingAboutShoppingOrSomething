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

function calculate_cart_total() {
	$total = 0;

	foreach (get_cart() as $v) {
		$total += $v['product']['price'] * $v['qty'];
	}

	return $total;
}

function get_qty_in_cart($pid) {
	$v = get_in(get_cart(), $pid);
	if(!$v) return 0;

	return $v['qty'];
}

function update_cart_quantities($nqtys) {
	$cart = get_cart();
	foreach ($nqtys as $pid => $nval) {
		$pid = (int) $pid;

		if(isset($cart[$pid])) {
			if($nval > 0){
				$cart[$pid]['qty'] = $nval;
			}else{
				unset($cart[$pid]);
			}
		}else{
			add_error("Tried to update quantity of entity not in cart: $pid");
		}
	}

	set_cart($cart);
}

?>