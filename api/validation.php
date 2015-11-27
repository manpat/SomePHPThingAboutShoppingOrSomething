<?php

// https://en.wikipedia.org/wiki/Luhn_algorithm
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

// Validates that a card expiry has not already passed
function validate_card_expiry_date($month, $year) {
	$date = new DateTime();
	$expr = new DateTime();
	$expr->setDate((int) $year, ((int)$month)+1, 1);

	return $date <= $expr;
}