<?php

require_once('../api/init.php');
require_once('../api/cart.php');
require_once('../api/product.php');

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

}else{
	add_error("Invalid action: " . json_encode($action));
	// TODO: change to return rather than assume goto cartview
	header("Location: ../cartview.php");
}


?>