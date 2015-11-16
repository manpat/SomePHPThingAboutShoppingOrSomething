<?php

// api/Products
//
// Product api

function get_products() {
	$data = file_get_contents("products.data");
	if($data === false) {
		add_error("Database open failed");
		return [];
	}

	$prods = json_decode($data, true);

	foreach ($prods as $id => &$prod) {
		$prod['id'] = (int) $id;
	}

	return $prods;
}

function get_product($id) {
	$id = (int) $id;
	$products = get_products();

	// Find the product that matches $id
	$productMatch = array_filter($products, function($p) use ($id) {
		return $p['id'] === $id;
	});

	// If it wasn't found, return null
	if(count($productMatch) <= 0){
		return null;
	}

	// Get the first value, there should only be on
	return array_shift($productMatch);
}

?>