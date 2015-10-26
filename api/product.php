<?php

// api/Products
//
// Product api

function get_products() {
	$prods = [
		['name'=>"Test Product", 'price'=>100],
		['name'=>"Another Product", 'price'=>65],
		['name'=>"Cheap Product", 'price'=>10],
	];

	foreach ($prods as $id => &$prod) {
		$prod['id'] = $id;
	}

	return $prods;
}

function get_product($id) {
	$products = get_products();

	$productMatch = array_filter($products, function($p) use ($id) {
		return $p['id'] === $id;
	});

	if(count($productMatch) <= 0){
		return null;
	}

	return array_shift(array_values($productMatch));
}

?>