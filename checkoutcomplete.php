<?php

// CheckoutComplete
//
// Displayed after checkout has been completed and
//	confirmed.
//

require_once("api/init.php");
require_once("api/cart.php");

function bail() {
	// add_error($_POST);
	header("Location: checkout.php");
	die;
}

// https://en.wikipedia.org/wiki/Luhn_algorithm
// visa: 4012888888881881
// mastercard: 5105105105105100

function validate_card($str, $type) {
	// Remove whitespace
	$str = preg_replace('/\s+/', '', $str);

	if(!is_numeric($str)) {
		add_error("Card number can only contain numeric characters");
		return false;
	}

	// Check card number lengths and IINs
	$len = strlen($str);
	if($type === "visa") {
		// Validate length
		if($len !== 13 && $len !== 16) {
			add_error("Invalid card number length for card type visa");
			return false;

		// Validate IIN
		}else if(substr($str, 0, 1) !== "4") {
			add_error("Invalid IIN for card type visa");
			return false;
		}

	}else if($type === "mastercard") {
		$iin = (int) substr($str, 0, 2);

		// Validate length
		if($len !== 16) {
			add_error("Invalid card number length for card type mastercard");
			return false;

		// Validate IIN
		}else if($iin < 51 || $iin > 55) {
			add_error("Invalid iin for card type mastercard");
			return false;
		}

	}else{
		add_error("Invalid card type: $type");
		return false;
	}

	// Save and strip out validation digit
	$val = (int) substr($str, -1);
	$str = strrev(substr($str, 0, -1)); // Reverse string
	$astr = str_split($str); // Convert to array

	// Convert values to integers
	$anum = array_map(function($s) {
		return (int) $s;
	}, $astr);

	// Double every second digit and sum individual digits if > 9
	for($i = 0; $i < count($anum); $i += 2) {
		$v = $anum[$i] * 2;
		if($v > 9) $v = $v - 9;
		$anum[$i] = $v;
	}

	// Check sum
	$checksum = (array_sum($anum)*9) % 10;
	return $checksum === $val;
}

function validate_card_expiry_date($month, $year) {
	$date = new DateTime();
	$expr = new DateTime();
	$expr->setDate((int) $year, ((int)$month)+1, 1);

	return $date <= $expr;
}

// Map from fieldname to English
$fieldtrans = [
	"name" => "Full Name",
	"phone" => "Phone Number",
	"address" => "Address",
	"cardtype" => "Credit Card Type",
	"cardnum" => "Credit Card Number",
	"cardexprmonth" => "Credit Card Expiry Month",
	"cardexpryear" => "Credit Card Expiry Year",
	"gift" => "Gift Wrapped"
];

// Saved values for rendering stage
$spent = 0; // Cart total
$vs = []; // Map of fields to values

function process() {
	global $fieldtrans;
	global $spent;
	global $vs;

	if(count(get_cart()) === 0) {
		add_error("Cart empty");
		header("Location: cartview.php");
	}

	// Mandatory fields
	$ps = ["name", "phone", "address", "cardtype", "cardnum", "cardexprmonth", "cardexpryear"];
	$vs = [];
	$error = false;

	// For each mandatory field verify that a value was recieved
	//	and is not an empty string
	foreach($ps as $p) {
		$v = get_in($_POST, $p);
		$vs[$p] = $v;

		if(is_null($v) || $v === "") {
			add_error("Missing ${fieldtrans[$p]}");
			$error = true;
		}
	}

	$vs["gift"] = (get_in($_POST, "gift") !== null) ? "Yes":"No";

	// One of the mandatory fields is missing or empty
	//	so bail out
	if($error) bail();

	// Validate the card number given the type, and
	//	bail if it doesn't quite add up
	if(!validate_card($vs["cardnum"], $vs["cardtype"])) {
		add_error("Card validation failed");
		bail();
	}

	// Validate that the card expiry date hasn't already passed
	if(!validate_card_expiry_date($vs["cardexprmonth"], $vs["cardexpryear"])) {
		add_error("Card expiry date already passed");
		bail();
	}

	// Convert month to English name
	$month = new DateTime();
	$month->setDate(0, (int) $vs["cardexprmonth"], 1);
	$vs["cardexprmonth"] = $month->format("F");

	// Save cart total for rendering
	$spent = calculate_cart_total();

	// Clear cart so user can't rebuy things
	set_cart([]);
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
		echo "<td style='width:20%; font-weight: bold;'>${fieldtrans[$k]}</td>";
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