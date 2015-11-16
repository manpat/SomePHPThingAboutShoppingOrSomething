<?php

// Init
//
// Starts the session and provides some helper functions.

session_start();

// Tests if a key exists in a map and returns it
//	otherwise returns a default value
function get_in($array, $key, $default = null) {
	return isset($array[$key]) ? $array[$key] : $default;
}

function add_error($e) {
	if(gettype($e) !== "string") {
		$e = json_encode($e);
	}

	$error = get_in($_SESSION, 'error', []);
	$error[] = $e;
	$_SESSION['error'] = $error;
}

function get_errors() {
	return get_in($_SESSION, 'error', []);
}

function reset_errors() {
	$_SESSION['error'] = [];
}

function get_root() {
	return dirname(__FILE__)."/..";
}

?>