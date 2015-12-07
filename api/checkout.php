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

	// Validate that the card expiry date hasn't already passed
	if(!validate_card_expiry_date($vs["cardexprmonth"], $vs["cardexpryear"])) {
		return ["success" => false, "reason" => "card_expiry_passed"];
	}

	$_SESSION["checkout_details"] = $vs;
	setcookie("checkout_details", json_encode($vs));

	return ["success"=>true];
}