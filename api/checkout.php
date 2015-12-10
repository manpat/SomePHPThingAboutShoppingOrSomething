<?php

require_once('init.php');
require_once('validation.php');

// Maps from fieldname to English
function checkout_field_to_english($fn) {
	return get_in([
			"name" => "Full Name",
			"phone" => "Phone Number",
			"address" => "Address",
			"cardtype" => "Credit Card Type",
			"cardnum" => "Credit Card Number",
			"cardexprmonth" => "Credit Card Expiry Month",
			"cardexpryear" => "Credit Card Expiry Year",
			"gift" => "Gift Wrapped"
		],
		$fn, // Key
		"invalid fieldname" // Default
	);
}

// Takes a hashmap of fields from checkout page
//	validates them, and saves them to the session
function process_checkout_details($details) {
	$mandatoryFields = ["name", "phone", "address", "cardtype", "cardnum", "cardexprmonth", "cardexpryear"];
	$missing = [];
	$vs = [];

	// For each mandatory field verify that a value was recieved
	//	and is not an empty string
	foreach($mandatoryFields as $fn) {
		$v = get_in($details, $fn);
		$vs[$fn] = $v;

		if(is_null($v) || $v === "") {
			$missing[] = $fn;
		}
	}

	// Bail if fields are missing
	if(count($missing) > 0) {
		return ["success"=>false, "reason"=>"missing_fields", "fields"=>$missing];
	}

	$vs["gift"] = (get_in($details, "gift") !== null);

	// Validate the card number given the type, and
	//	bail if it doesn't quite add up
	if(!validate_card($vs["cardnum"], $vs["cardtype"])) {
		return ["success" => false, "reason" => "card_validation_failed"];
	}

	$vs["cardexprmonth"] = (int) $vs["cardexprmonth"];
	$vs["cardexpryear"] = (int) $vs["cardexpryear"];

	// Validate that the card expiry date hasn't already passed
	if(!validate_card_expiry_date($vs["cardexprmonth"], $vs["cardexpryear"])) {
		return ["success" => false, "reason" => "card_expiry_passed"];
	}

	$_SESSION["checkout_details"] = $vs;

	$expiryTime = time()+60*60*24*60; // 60 days
	setcookie("checkout_details", json_encode($vs), $expiryTime, '/');

	$checkoutLog = @fopen(get_root()."/data/checkout_log.data", "ab");
	if($checkoutLog === false) {
		return ["success"=>false, "reason"=>"database_open_fail"];
	}

	$cart = get_cart();
	$pidQtyPairs = [];
	foreach ($cart as $id => $pair) {
		$pidQtyPairs[(int) $id] = (int) $pair["qty"];
	}

	$vs["purchases"] = $pidQtyPairs;
	$vs["total"] = calculate_cart_total();

	$jsonData = json_encode($vs);
	$logSuccess = fwrite($checkoutLog, $jsonData . "\n");
	fclose($checkoutLog);

	if($logSuccess === false) {
		return ["success"=>false, "reason"=>"database_write_fail"];
	}

	return ["success"=>true];
}

function get_checkout_details() {
	$details = get_in($_SESSION, "checkout_details");
	if(!is_null($details)) return $details;

	$details = get_in($_COOKIE, "checkout_details");
	if(!is_null($details)) {
		$details = json_decode($details, true);
		return $details;
	}

	return [];
}