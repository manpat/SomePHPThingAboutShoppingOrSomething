<?php

session_start();

$error = null;

function set_error($e) {
	global $error;
	$error = $e;
}

function get_in($array, $key, $default = null) {
	return isset($array[$key]) ? $array[$key] : $default;
}

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

?>