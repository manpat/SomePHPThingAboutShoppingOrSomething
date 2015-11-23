<?php

require_once('api/init.php');
require_once('api/product.php');

// Render individual product
function render_product($product) {
	// echo "<tr>";
	// echo "<td><a href='productdetails.php?id=${product["id"]}'>${product['name']}</a></td>";
	// echo "<td width='10%'>${product['price']}</td>";
	// echo "</tr>";
	echo "<div class='tile'>";
	echo "<a href='productdetails.php?id=${product["id"]}'>";
	echo "<div class='tileimg'><img src='img/${product["img"]}'></img></div>";
	echo "<div class='tilecap'>";
	echo "<strong>${product['name']}</strong>";
	echo "<figcaption>$${product['price']}</figcaption>";
	echo "</div></a></div>";
}

// Render set of products
function render() {
	echo "<h1>Products</h1>";

	foreach (get_products() as $product) {
		render_product($product);
	}
}

require('skeleton.php');

?>