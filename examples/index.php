<?php

/**
 * Siesta example
 *
 * How to respond to various requests
 */

require "Siesta.php";

$siesta = new Siesta();

// Respond to a home page request
$siesta->respond("GET", "/", function() {
	echo "Root path get";
});

// Respond to a specific person by id
$siesta->respond("GET", "/person/(int:i)", function($i) {
	echo "This is person #" . $i;
});