<?php

$comments = null;

// [productID: [{timestamp, name, email, comment}]]

function get_comments() {
	global $comments;

	if($comments === null) {
		$data = file_get_contents("comments.data");
		if($data === false) {
			add_error("Comment database open failed");
			return [];
		}

		$comments = json_decode($data, true);
	}

	return $comments;
}

function set_comments($coms) {
	global $comments;
	$comments = $coms;

	$data = json_encode($comments);

	file_put_contents("comments.data", $data);
}

function sanitise_input($value) {
	// TODO
	return $value;
}

// $comment should be in format {name, email or null, comment}
function add_comment($prodId, $comment) {
	$prodId = (int) $prodId;
	$comments = get_comments();

	// Create an array for $prodId comments 
	//	if there is none
	if(!isset($comments[$prodId])) {
		$comments[$prodId] = [];
	}

	// Verify and sanitise comment
	if(!isset($comment['email'])) $comment['email'] = null;

	$comment['timestamp'] = time();
	$comment['name'] = sanitise_input($comment['name']);
	$comment['email'] = sanitise_input($comment['email']);
	$comment['comment'] = sanitise_input($comment['comment']);

	$comments[$prodId][] = $comment; // Append new comment

	// Save
	set_comments($comments);
}

?>