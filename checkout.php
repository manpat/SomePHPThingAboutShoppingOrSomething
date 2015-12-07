<?php

// Checkout
//
// Requests user details and confirms purchase(s)
//		Full name
//		Phone number
//		Address
//		Gift wrapped? checkbox
//		Credit card type radio
//		Credit card expiry
// Goes to CheckoutComplete on confirmation

// https://en.wikipedia.org/wiki/Bank_card_number

require_once("api/init.php");
require_once("api/cart.php");

// Generates <option>s for credit card expiry month
function generate_month_options($savedValue) {
	$date = new DateTime();

	if(!is_null($savedValue))
		$savedValue = (int) $savedValue;

	for($i = 1; $i <= 12; $i++) {
		$date->setDate(0, $i, 1);
		if($savedValue === $i)
			echo "<option value='$i' selected='selected'>".$date->format("F")."</option>";
		else
			echo "<option value='$i'>".$date->format("F")."</option>";
	}
}

// Generates <option>s for credit card expiry year
//	starting from this year and going to 25 years in
//	the future
function generate_year_options($savedValue) {
	$date = new DateTime(); // Today
	$yearint = new DateInterval("P1Y"); // Represents one year

	if(!is_null($savedValue))
		$savedValue = (int) $savedValue;

	// 25 years is a guestimation
	for($i = 0; $i <= 25; $i++) {
		$year = $date->format("Y");
		$date->add($yearint);

		if($savedValue === (int) $year)
			echo "<option value='$year' selected='selected'>".$year."</option>";
		else
			echo "<option value='$year'>".$year."</option>";
	}
}

$savedFields = null;

function process() {
	global $savedFields;

	$savedFields = get_in($_SESSION, "checkout_details");
	if(!is_null($savedFields)) return;

	$savedFields = get_in($_COOKIE, "checkout_details");
	if(!is_null($savedFields)) {
		$savedFields = json_decode($savedFields);
		return;
	}

	$savedFields = [];
}

process();

function render() { 
	global $savedFields;

	$name = get_in($savedFields, "name", "");
	$gift = get_in($savedFields, "gift", "") !== "";
	$phone = get_in($savedFields, "phone", "");
	$address = get_in($savedFields, "address", "");
	$cardnum = get_in($savedFields, "cardnum", "");
	$isMC = get_in($savedFields, "cardtype", "") === "mastercard";
	$cardexpryear = get_in($savedFields, "cardexpryear");
	$cardexprmonth = get_in($savedFields, "cardexprmonth");

	?>
	<h1>Checkout, yo!</h1>
	<form id='checkoutform'>
		<table class='form'>
			<tr><td><label>Full Name</label></td>
				<td><input type='text' name='name' value='<?= $name ?>'/></td></tr>

			<tr><td><label>Phone Number</label></td>
				<td><input type='text' name='phone' value='<?= $phone ?>'/></td></tr>

			<tr><td><label>Address</label></td>
				<td><input type='text' name='address' value='<?= $address ?>'/></td></tr>

			<tr><td><label>Gift Wrapped</label></td>
				<td><input type='checkbox' name='gift' <?= $gift?"checked":"" ?>/></td></tr>

			<tr><td><label>Credit Card Type</label></td>
				<td><input type='radio' name='cardtype' value='visa' <?= $isMC?"":"checked" ?>>Visa</input>
					<input type='radio' name='cardtype' value='mastercard' <?= $isMC?"checked":"" ?>>Mastercard</input></td></tr>

			<tr><td><label>Credit Card Number</label></td>
				<td><input type='text' name='cardnum' value='<?= $cardnum ?>'/></td></tr>

			<tr><td><label>Credit Card Expiry</label></td>
				<td><select name='cardexprmonth'> <?php generate_month_options($cardexprmonth);?> </select>
					<select name='cardexpryear'> <?php generate_year_options($cardexpryear);?> </select>
				</td></tr>
		</table>

		<br/>

		<button class='right' type='submit'>Continue</button>
	</form>

	<!-- FOR TESTING ONLY  -->
	<div style='position: absolute; bottom: 1em'>Visa: 4012888888881881</div>
	<div style='position: absolute; bottom: 2em'>Mastercard: 5105105105105100</div>

	<script>
		$('#checkoutform').submit(function(event){
			event.preventDefault();
			var $form = $(this);
			
			var data = {};
			$.each($form.serializeArray(), function(i, kv){
				data[kv.name] = kv.value;
			});

			$.post('api/action.php', {action: 'attemptcheckout', details: data})
			.done(function(d){
				if(d.success !== true) {
					console.log("Request failed");
					console.log(d.reason);
					console.log(d);

					if(d.reason === "missing_fields") {
						for(var i = 0; i < d.fields.length; i++) {
							var field = d.fields[i];
							$form.find("[name=" + field + "]").addClass("error");
						}

					}else if(d.reason === "card_validation_failed"){
						// addError("Card Number invalid");

					}else if(d.reason === "card_expiry_passed"){
						// addError("Credit card expiry has aleady passed");
					}

					return;
				}

				console.log("Request success!");
				window.location.href='checkoutcomplete.php';
			});

			console.log(data);
		});
	</script>
<?php }

require("skeleton.php");

?>