<?php

require_once('api/init.php');
require_once('api/product.php');

// Render individual product
function render_product($product) {
	echo "<div class='tile'>";
		// Tiles are giant anchor tags
		echo "<a href='productdetails.php?id=${product["id"]}'>";
			// Thumbnail
			echo "<div class='tileimg'><img src='img/${product["img"]}'></img></div>";
			echo "<div class='tilecap'>";
				// Product name
				echo "<strong>${product['name']}</strong>";
				// Product price
				echo "<figcaption>$${product['price']}</figcaption>";
	echo "</div></a></div>";
}

// Render set of products
function render() {
	echo "<h1>Rocks For Adoption</h1>";

	foreach (get_products() as $product) {
		render_product($product);
	}
}

require('skeleton.php');

?>