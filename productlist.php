<?php

require('api/init.php');
require('api/product.php');

// Render set of products
function render_productlist() {
	echo "<h1>Products</h1>";
	
	echo "<table>";
	echo "<thead><th>Product Name</th><th>Price</th></thead>";
	echo "<tbody>";
	foreach (get_products() as $product) {
		render_product($product);
	}
	echo "</tbody>";
	echo "</table>";
}

// Render individual product
function render_product($product) {
	echo "<tr>";
	echo "<td><a href='productdetails.php?id=${product["id"]}'>${product['name']}</a></td>";
	echo "<td width='10%'>${product['price']}</td>";
	echo "</tr>";
}

function render() {
	render_productlist();
}

require('skeleton.php');

?>