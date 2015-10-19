<?php

require('init.php');

// Render set of tiles
function render_productlist() {
	echo "<table>";
	echo "<tr><th>Product</th><th>Price</th></tr>";
	foreach (get_products() as $product) {
		render_product($product);
	}
	echo "</table>";
}

// Render individual tile
function render_product($product) {
	echo "<tr>";
	echo "<td><a href='productdetails.php?id=${product["id"]}'>${product['name']}</a></td>";
	echo "<td>${product['price']}</td>";
	echo "</tr>";
}

function render() {
	render_productlist();
}

require('skeleton.php');

?>