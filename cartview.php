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
		<table>
			<thead>
				<th>Name</th>
				<th width='8%'>Price</th>
				<th width='8%'>Quantity</th>
				<th width='8%'>Total</th>
				<th></th>
			</thead>
			<tbody id='itemlist'>
				<?php
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
		$('.removebutton').click(function(e) {
			var row = $(this).parent();
			var id = row.data('id');
			var qtybox = row.find('input');

			qtybox.val(0);
			qtybox.trigger('change');
		});

		$('#itemlist input[type=number]').on('change', function(e){
			var $subtotal = $(this).parent().next();
			var price = $(this).parent().parent().data('price');
			var subtotal = price * $(this).val();
			$subtotal.text("$"+subtotal.toFixed(2));
		});
	</script>
<?php }

function render_item($item) {
	$prod = $item['product'];
	$id = $prod['id'];
	$price = $prod['price'];
	$qty = $item['qty'];

	$subtotal = $qty * $price;

	echo "<tr data-id='$id' data-price='$price'>";
	echo "<td>${prod['name']}</td>";
	printf("<td>$%.2f</td>", $price); // Don't print more than 2 dp's
	echo "<td><input type='number' style='width:50px;' min='0' name='qtys[$id]' value='$qty'/></td>";
	printf("<td>$%.2f</td>", $subtotal); // Don't print more than 2 dp's
	echo "<td class='removebutton'>x</td>";
	echo "</tr>";
}

require("skeleton.php");

?>