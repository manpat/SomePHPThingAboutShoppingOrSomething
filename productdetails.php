<?php

require('init.php');

$product = null;

function render() {
	global $product;
	if(is_null($product)) {
		echo "<p>Null product!</p>";
		return;
	}

	echo "<p>Product name: ${product['name']}</p>";
	echo "<p>Product price: ${product['price']}</p>";
	echo "<a class='button'>Buy now, yo</a>";
}

function process() {
	global $product;

	$products = get_products();
	$pid = (int) get_in($_GET, 'id', -1);
	if($pid === -1) {
		set_error("Missing product id");
		return;
	}

	$productMatch = array_filter($products, function($p) use ($pid) {
		return $p['id'] === $pid;
	});

	if(count($productMatch) <= 0){
		set_error("Invalid product id");
		return;
	}

	$product = array_shift(array_values($productMatch));
}

process();

require('skeleton.php');

?>