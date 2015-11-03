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

require("init.php");
require("api/cart.php");

function render() { ?>
	<h1>Checkout, yo!</h1>
	<form action='checkoutcomplete.php' method='POST'>
		<table>
			<tr><td><label>Full Name</label></td>
				<td><input type='text' name='name'/></td></tr>

			<tr><td><label>Phone Number</label></td>
				<td><input type='text' name='phone'/></td></tr>

			<tr><td><label>Address</label></td>
				<td><input type='text' name='address'/></td></tr>
				
			<tr><td><label>Gift Wrapped</label></td>
				<td><input type='checkbox' name='gift'/></td></tr>
				
			<tr><td><label>Credit Card Type</label></td>
				<td><input type='radio' name='cardtype' value='visa'>Visa</input></td>
				<td><input type='radio' name='cardtype' value='mastercard'>Mastercard</input></td></tr>

			<tr><td><label>Credit Card Number</label></td>
				<td><input type='text' name='cardnum'/></td></tr>

			<tr><td><button type='submit'>Done</button></td></tr>
		</table>
	</form>
<?php }

require("skeleton.php");

?>