<?php

$comments = null;

// [productID: [{timestamp, name, email, comment}]]

function get_comments() {
	global $comments;

	if($comments === null) {
		$data = file_get_contents(get_root()."/data/comments.data");
		if($data === false) {
			add_error("Comment database open failed");
			return [];
		}

		// Convert keys to integers
		$rawcomments = json_decode($data, true);
		$comments = [];
		foreach ($rawcomments as $key => $value) {
			$comments[(int) $key] = $value;
		}
	}

	return $comments;
}

function set_comments($coms) {
	global $comments;
	$comments = $coms;

	// Convert keys to strings
	$rawcomments = [];
	foreach ($comments as $key => $value) {
		$rawcomments[(string) $key] = $value;
	}

	$data = json_encode($rawcomments);

	file_put_contents(get_root()."/data/comments.data", $data);
}

function sanitise_input($value) {
	// TODO
	return htmlspecialchars(trim($value));
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