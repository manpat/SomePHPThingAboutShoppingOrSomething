<?php

// CheckoutComplete
//
// Displayed after checkout has been completed and
//	confirmed.
//

require_once("api/init.php");
require_once("api/cart.php");
require_once("api/checkout.php");
require_once("api/validation.php");

function bail() {
	header("Location: checkout.php");
	die;
}

// visa: 4012888888881881
// mastercard: 5105105105105100

// Saved values for rendering stage
$spent = 0; // Cart total
$vs = []; // Map of fields to values

function process() {
	// global $fieldtrans;
	global $spent;
	global $vs;

	if(count(get_cart()) === 0) {
		add_error("Cart empty");
		header("Location: cartview.php");
	}

	// Mandatory fields
	$ps = ["name", "phone", "address", "cardtype", "cardnum", "cardexprmonth", "cardexpryear"];
	// $vs = [];
	$error = false;

	// For each mandatory field verify that a value was recieved
	//	and is not an empty string
	// foreach($ps as $p) {
	// 	$v = get_in($_POST, $p);
	// 	$vs[$p] = $v;

	// 	if(is_null($v) || $v === "") {
	// 		add_error("Missing ${fieldtrans[$p]}");
	// 		$error = true;
	// 	}
	// }


	// // One of the mandatory fields is missing or empty
	// //	so bail out
	// if($error) bail();

	// // Validate the card number given the type, and
	// //	bail if it doesn't quite add up
	// if(!validate_card($vs["cardnum"], $vs["cardtype"])) {
	// 	add_error("Card validation failed");
	// 	bail();
	// }

	// // Validate that the card expiry date hasn't already passed
	// if(!validate_card_expiry_date($vs["cardexprmonth"], $vs["cardexpryear"])) {
	// 	add_error("Card expiry date already passed");
	// 	bail();
	// }

	$vs = get_in($_SESSION, "checkout_details");
	if(is_null($vs)) {
		add_error("Missing checkout details");
		header("Location: checkout.php");
	}

	$vs["gift"] = $vs["gift"] ? "Yes":"No";

	// Convert month to English name
	$month = new DateTime();
	$month->setDate(0, (int) $vs["cardexprmonth"], 1);
	$vs["cardexprmonth"] = $month->format("F");

	// Save cart total for rendering
	$spent = calculate_cart_total();

	// Clear cart so user can't rebuy things
	// set_cart([]);
	////////////////////////////////////////////////////////// YO
}

function render() {
	global $vs;
	global $spent;
	global $fieldtrans;

	echo "<h1>Checkout Complete, Yo.</h1>";

	echo "<table>";
	echo "<thead><th>Details</th><th></th></thead>";
	// Foreach value saved
	foreach ($vs as $k => $v) {
		// Render a row containing
		echo "<tr>";
		// it's English name
		echo "<td style='width:20%; font-weight: bold;'>".checkout_field_to_english($k)."</td>";
		// and it's value
		echo "<td>$v</td>";
		echo "</tr>";
	}
	echo "</table>";

	echo "<br/>";
	echo "<table>";
		echo "<tr><td style='width:20%; font-weight: bold;'>Money Wasted</td><td>$$spent</td></tr>";
	echo "</table>";

	echo "<br/>";
	echo "<p><a href='index.php' class='button'>Spend <!-- Waste -->more money</a></p>";
}

process();

require("skeleton.php");

?>