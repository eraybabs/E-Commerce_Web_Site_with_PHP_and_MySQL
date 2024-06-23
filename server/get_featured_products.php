<?php

include('SQLManager.php');

$sqlManager = new SQLManager();

// Get featured products
$featured_products = $sqlManager->getFeaturedProducts();

?>
