# Siesta

**Siesta** is a simple PHP routing class. It allows you to map HTTP requests to user-defined functions in a RESTful manner.

* Respond to different HTTP request types (GET, POST, PUT, PATCH, DELETE)
* Follow RESTful patterns, e.g. GET user/123/status
* Match against integers or strings

## Setup

Like most routers, requests will all go through an index.php file. You will have to be able to rewrite the URL (to remove the index.php). This can be achieved with an .htaccess file on Apache systems:

```
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [L]
```

Include the Siesta.php file in your index page and set the routes you would like to respond to:

```php
require "Siesta.php";

$siesta = new Siesta();

// Respond to a home page request
$siesta->respond("GET", "/", function() {
	echo "Root path get";
});

// Get a person by id, but only if an integer is supplied
$siesta->respond("GET", "/person/(int:id)", function($id) {
	echo "You found person " . $id;
});
```