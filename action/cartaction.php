<?php

require_once('../api/init.php');
require_once('../api/cart.php');

$action = get_in($_POST, 'action');
$nqtys = get_in($_POST, "qtys");

if($action === 'save'){
	update_cart_quantities($nqtys);
	header("Location: ../cartview.php");

}else if($action === 'clear'){
	set_cart([]);
	header("Location: ../cartview.php");

}else if($action === 'checkout'){
	update_cart_quantities($nqtys);

	if(count(get_cart()) === 0) {
		add_error("Cart is empty");
		header("Location: ../cartview.php");

	}else{
		header("Location: ../checkout.php");
	}

}else{
	add_error("Invalid action: $action");
	header("Location: ../cartview.php");
}


?>