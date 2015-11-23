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
	<form action='api/action.php' method='POST'>
		<table id='cartview'>
			<thead>
				<th>Name</th>
				<th class='small'>Price</th>
				<th class='small'>Quantity</th>
				<th class='small'>Total</th>
				<th></th> <!-- For the remove button -->
			</thead>
			<tbody>
				<?php
					// Render each item in the cart
					$cart = get_cart();
					foreach ($cart as $item) {
						render_item($item);
					}
				?>
			</tbody>
		</table>
		<br/>

		<div class='right'>Total: $<?php echo calculate_cart_total();?></div>
		<br/>
		<br/>

		<button class='right' type='submit' name='action' value='checkout' default>Checkout</button>
		<button class='right' type='submit' name='action' value='savecart'>Save</button>
		<button class='right red' type='submit' name='action' value='clearcart'>Clear</button>
	</form>

	<script>
		// When a .removebutton is clicked
		$('.removebutton').click(function(e) {
			// Get the nearest input
			var qtybox = $(this).parent().find('input');

			// Set it's value to zero and trigger
			//	'change' callback (defined below)
			qtybox.val(0);
			qtybox.trigger('change');
		});

		// When an input in the cartview is changed
		$('#cartview input[type=number]').on('change', function(e){
			// Calculate a new subtotal
			var price = $(this).parent().parent().data('price');
			var subtotal = price * $(this).val();

			// And set $subtotals text to that
			var $subtotal = $(this).parent().next();
			$subtotal.text("$"+subtotal.toFixed(2));
		});
	</script>
<?php }

function render_item($item) {
	$prod = $item['product'];
	$id = $prod['id'];
	$price = $prod['price'];
	$qty = $item['qty'];

	// Calculate the items subtotal
	$subtotal = $qty * $price;

	echo "<tr data-price='$price'>"; // Echo item price for client-side subtotal recalculation
		echo "<td>${prod['name']}</td>";
		printf("<td>$%.2f</td>", $price); // Don't print more than 2 dp's
		echo "<td><input type='number' min='0' name='qtys[$id]' value='$qty'/></td>";
		printf("<td>$%.2f</td>", $subtotal); // Don't print more than 2 dp's
		echo "<td class='removebutton'>x</td>";
	echo "</tr>";
}

require("skeleton.php");

?>