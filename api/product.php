<?php

// api/Products
//
// Product api

$prods = null;

function get_products() {
	global $prods;

	// If the product list has yet to be cached
	// 	load the product list from file 
	if($prods === null) {
		$data = file_get_contents(get_root()."/data/products.data");
		if($data === false) {
			add_error("Product database open failed");
			return [];
		}

		$prods = json_decode($data, true);
		if(is_null($prods)) {
			add_error("Product database decode failed");
			return [];
		}
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