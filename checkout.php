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

require("init.php");
require("api/cart.php");

function generate_month_options() {
	$date = new DateTime();

	for($i = 1; $i <= 12; $i++) {
		$date->setDate(0, $i, 1);
		echo "<option value='$i'>".$date->format("F")."</option>";
	}
}

function generate_year_options() {
	$date = new DateTime();
	$yearint = new DateInterval("P1Y");

	// Guestimation
	for($i = 0; $i <= 25; $i++) {
		$year = $date->format("Y");
		$date->add($yearint);

		echo "<option value='$year'>".$year."</option>";
	}
}

function render() { ?>
	<h1>Checkout, yo!</h1>
	<form action='checkoutcomplete.php' method='POST'>
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
				<td><input type='radio' name='cardtype' value='visa'>Visa</input>
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
<?php }

require("skeleton.php");

?>