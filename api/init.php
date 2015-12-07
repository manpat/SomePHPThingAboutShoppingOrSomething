<?php

// Init
//
// Starts the session and provides some helper functions.

session_start();

// Tests if a key exists in a map and returns it
//	otherwise returns a default value
function get_in($array, $key, $default = null) {
	if(gettype($array) === 'object')
		$array = (array) $array;

	return isset($array[$key]) ? $array[$key] : $default;
}

// Saves an error for later rendering
function add_error($e) {
	if(gettype($e) !== "string") {
		$e = json_encode($e);
	}

	$error = get_in($_SESSION, 'error', []);
	$error[] = $e;
	$_SESSION['error'] = $error;
}

// Gets a list of errors from the session
function get_errors() {
	return get_in($_SESSION, 'error', []);
}

// Clears errors
function reset_errors() {
	$_SESSION['error'] = [];
}

// Gets (mostly) absolute path to root of web directory
//	Mainly for file operations
function get_root() {
	return dirname(__FILE__)."/..";
}

?>