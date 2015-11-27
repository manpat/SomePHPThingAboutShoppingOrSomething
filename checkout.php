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
function generate_month_options() {
	$date = new DateTime();

	for($i = 1; $i <= 12; $i++) {
		$date->setDate(0, $i, 1);
		echo "<option value='$i'>".$date->format("F")."</option>";
	}
}

// Generates <option>s for credit card expiry year
//	starting from this year and going to 25 years in
//	the future
function generate_year_options() {
	$date = new DateTime(); // Today
	$yearint = new DateInterval("P1Y"); // Represents one year

	// 25 years is a guestimation
	for($i = 0; $i <= 25; $i++) {
		$year = $date->format("Y");
		$date->add($yearint);

		echo "<option value='$year'>".$year."</option>";
	}
}

function render() { ?>
	<h1>Checkout, yo!</h1>
	<!-- action='checkoutcomplete.php' method='POST' -->
	<form id='checkoutform'>
		<table class='form'>
			<tr><td><label>Full Name</label></td>
				<td><input type='text' name='name'/></td></tr>

			<tr><td><label>Phone Number</label></td>
				<td><input type='text' name='phone'/></td></tr>

			<tr><td><label>Address</label></td>
				<td><input type='text' name='address'/></td></tr>

			<tr><td><label>Gift Wrapped</label></td>
				<td><input type='checkbox' name='gift'/></td></tr>

			<tr><td><label>Credit Card Type</label></td>
				<td><input type='radio' name='cardtype' value='visa' default>Visa</input>
					<input type='radio' name='cardtype' value='mastercard'>Mastercard</input></td></tr>

			<tr><td><label>Credit Card Number</label></td>
				<td><input type='text' name='cardnum'/></td></tr>

			<tr><td><label>Credit Card Expiry</label></td>
				<td><select name='cardexprmonth'> <?php generate_month_options();?> </select>
					<select name='cardexpryear'> <?php generate_year_options();?> </select>
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
					return;
				}

				console.log("Request success!");
				window.location.href='checkoutcomplete.php';
				// Goto checkoutcomplete
			});

			console.log(data);
		});
	</script>
<?php }

require("skeleton.php");

?>