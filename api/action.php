<?php

require_once('../api/init.php');
require_once('../api/cart.php');
require_once('../api/product.php');
require_once('../api/comment.php');

$action = get_in($_POST, 'action');
$nqtys = get_in($_POST, "qtys");

// Saves the updated quantities of items in the cart
if($action === 'savecart'){
	update_cart_quantities($nqtys);
	header("Location: ../cartview.php");

// Removes all items from the cart
}else if($action === 'clearcart'){
	set_cart([]);
	header("Location: ../cartview.php");

// Updates the cart and proceeds to redirects to checkout
//	if the cart isn't empty
}else if($action === 'checkout'){
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