<?php

/**
 * Siesta example
 *
 * How to respond to various requests
 */

require "../Siesta.php";

$siesta = new Siesta();

// Respond to a home page request
$siesta->respond("GET", "/", function() {
	echo "Root path get";
});

// Respond to a specific person by id
$siesta->respond("GET", "/person/(int:i)", function($i) {
	echo "This is person #" . $i;
});

// Respond to a DELETE request, delete an account by id
$siesta->respond("DELETE", "/account/(int:id)", function($id) {
	echo "You are deleting account #" . $id;
});