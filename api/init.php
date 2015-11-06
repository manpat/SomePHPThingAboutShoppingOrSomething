<?php

// Init
//
// Starts the session and provides some helper functions.

session_start();

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

?>