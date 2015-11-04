<?php

// CheckoutComplete
//
// Displayed after checkout has been completed and
//	confirmed.
// 

require("init.php");
require("api/cart.php");

function bail() {
	add_error(json_encode($_POST));
	header("Location: checkout.php");
	die;	
}

// https://en.wikipedia.org/wiki/Luhn_algorithm
function validate_card($str, $type) {
	// Remove whitespace
	$str = preg_replace('/\s+/', '', $str);

	if(!is_numeric($str)) {
		add_error("Card number can only contain numeric characters");
		return false;
	}

	// Check card number length and IIN
	$len = strlen($str);
	switch($type) {
		case "visa": {
			if($len !== 13 && $len !== 16) {
				add_error("Invalid card number length for card type visa");
				return false;

			}else if(substr($str, 0, 1) !== "4") {
				add_error("Invalid iin for card type visa");
				return false;
			}	
		} break;

		case "mastercard":{
			$iin = (int) substr($str, 0, 2);
			if($len !== 16) {
				add_error("Invalid card number length for card type mastercard");
				return false;

			}else if($iin < 51 || $iin > 55) {
				add_error("Invalid iin for card type mastercard");
				return false;
			}			
		} break;

		default:
			add_error("Invalid card type: $type");
			return false;
	}

	// Save and strip out validation digit
	$val = (int) substr($str, -1);
	$str = strrev(substr($str, 0, -1));
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

function process() {
	// Mandatory fields
	$ps = ["name", "phone", "address", "cardtype", "cardnum"];
	$vs = []; // Map of fields to values
	$error = false;

	// Verify that values exist for each mandatory field
	foreach($ps as $p) {
		$v = get_in($_POST, $p);
		$vs[$p] = $v;

		if(is_null($v) || $v === "") {
			// TODO: Add a translation map for english field names
			add_error("Missing $p");
			$error = true;
		}
	}

	$vs["gift"] = get_in($_POST, "gift") !== null;

	// One of the mandatory fields is missing
	// Bail out
	if($error) bail();

	if(!validate_card($vs["cardnum"], $vs["cardtype"])) {
		add_error("Card validation failed");
		bail();
	}
}

function render() {
	echo json_encode($_POST);
}

process();

require("skeleton.php");

?>