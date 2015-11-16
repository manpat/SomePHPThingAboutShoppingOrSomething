<?php

// CartView
// 
// A full page list of items in the cart to
//	be shown before checkout
// Each item should have a price, maybe quantity
// This should show total price

require_once("api/init.php");
require_once("api/cart.php");

function render() { ?>
	<h1>Cart</h1>
	<form action='action/cartaction.php' method='POST'>
		<table>
			<thead>
				<th>Name</th>
				<th width='8%'>Price</th>
				<th width='8%'>Quantity</th>
				<th width='8%'>Total</th>
			</thead>
			<tbody>
				<?php
					$cart = get_cart();
					foreach ($cart as $item) {
						echo "<tr>";
						render_item($item);
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
		<br/>
		
		<div class='right'>Total: $<?php echo calculate_cart_total();?></div>
		<br/>
		<br/>

		<button class='right' type='submit' name='action' value='checkout' default>Checkout</button>
		<button class='right' type='submit' name='action' value='save'>Save</button>
		<button class='right red' type='submit' name='action' value='clear'>Clear</button>
	</form>
<?php }

function render_item($item) {
	$prod = $item['product'];
	$price = $prod['price'];
	$qty = $item['qty'];

	$subtotal = $qty * $price;

	echo "<td>${prod['name']}</td>";
	echo "<td>$$price</td>";
	echo "<td><input type='number' style='width:50px;' name='qtys[${prod['id']}]' value='$qty'/></td>";
	echo "<td>$$subtotal</td>";
}

require("skeleton.php");

?>