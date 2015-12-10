<?php

require_once('init.php');
require_once('cart.php');
require_once('product.php');
require_once('comment.php');
require_once('checkout.php');

// Used to respond to async post requests
function echo_success($data = null) {
	if($data === null) {
		$data = [];
	}

	header('Content-Type: application/json');
	echo json_encode(array_merge(["success" => true], $data));
	die;
}

// Used to respond to async post requests
function echo_fail($reason, $data = null) {
	if($data === null) {
		$data = [];
	}

	header('Content-Type: application/json');
	echo json_encode(array_merge(["success" => false, "reason" => $reason], $data));
	die;
}

$action = get_in($_POST, 'action');

// Asynchronous POST Requests

// Verifies details from checkout page
if($action === 'attemptcheckout') {
	$details = get_in($_POST, 'details');
	if(is_null($details)) {
		echo_fail("missing_details");
	}

	$res = process_checkout_details($details);
	if(!$res["success"]) {
		echo_fail(get_in($res, "reason", "[unknown_reason]"), $res);
	}

	echo_success($details);

// Sets the timezone for the session
}else if($action === 'settimezone'){
	$_SESSION['timezone'] = get_in($_POST, 'timezone');
	echo_success();
}

// Synchronous POST Requests

// Saves the updated quantities of items in the cart
if($action === 'savecart'){
	$nqtys = get_in($_POST, "qtys");
	update_cart_quantities($nqtys);
	header("Location: ../cartview.php");

// Removes all items from the cart
}else if($action === 'clearcart'){
	set_cart([]);
	header("Location: ../cartview.php");

// Updates the cart and proceeds to redirects to checkout
//	if the cart isn't empty
}else if($action === 'checkout'){
	$nqtys = get_in($_POST, "qtys");
	update_cart_quantities($nqtys);

	if(count(get_cart()) === 0) {
		add_error("Cart is empty");
		header("Location: ../cartview.php");

	}else{
		header("Location: ../checkout.php");
	}

// Adds item $_GET['item'] to cart and
//	redirects back to productdetails
}else if($action === 'addtocart'){
	$prodID = (int) get_in($_POST, 'item');

	$product = get_product($prodID);
	if(!$product) {
		add_error("Invalid product: $prodID");
	}else{
		add_to_cart($product);
	}

	header("Location: ../productdetails.php?id=$prodID");

// Adds a comment to a product description page
}else if($action === 'addcomment'){
	$prodID = (int) get_in($_POST, 'item');
	$uname = trim(get_in($_POST, 'name', ''));
	$email = get_in($_POST, 'email'); // optional
	$comment = get_in($_POST, 'comment', '');

	if($uname === '') {
		add_error("Name is empty");

	}else if($comment === '') {
		add_error("Comment is empty");

	}else{
		add_comment($prodID, ['name'=>$uname, 'email'=>$email, 'comment'=>$comment]);
	}

	header("Location: ".$_SERVER['HTTP_REFERER']);

}else{
	add_error("Invalid action: " . json_encode($action));
	header("Location: " . $_SERVER['HTTP_REFERER']);
}


?>