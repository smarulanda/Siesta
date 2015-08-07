<?php

/**
 * Siesta Class
 *
 * A micro php router, ready to REST
 *
 * @author		Sebastian Marulanda
 * @link		http://sebastianmarulanda.com/
 */

class Siesta {

	protected $base_path;
	protected $request_uri;
	protected $request_method;
	protected $http_methods = array('get', 'post', 'put', 'delete', 'patch');
	protected $wild_cards = array('int' => '/^[0-9]+$/', 'any' => '/^[0-9A-Za-z]+$/');

	/**
	 * Constructor
	 *
	 * Set a base path if necesary
	 *
	 * @param string $base_path If all routes will share a common base path, it can be set here in the constructor
	 */
	function __construct($base_path = '') {
		$this->base_path = $base_path;

		// Remove query string and trim trailing slash
		$this->request_uri = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
		$this->request_method = $this->_determine_http_method();
	}

	/**
	 * Point a method/route to its target
	 *
	 * @param string $method The HTTP method to respond to (GET, POST, PUT, DELETE, or PATCH)
	 * @param string $route The uri route (with any wild cards) to respond to
	 * @param function $callable The method to execute when a successful route is matched
	 */
	public function respond($method, $route, $callable) {
		$method = strtolower($method);

		if ($route == '/') $route = $this->base_path;
		else $route = $this->base_path . $route;

		$matches = $this->_match_wild_cards($route);

		if (is_array($matches) && $method == $this->request_method) {
			// Routes match and request method matches
			call_user_func_array($callable, $matches);
		}
	}

	/**
	 * Determine method
	 *
	 * Determine which HTTP method was sent
	 *
	 * @return string
	 */
	private function _determine_http_method() {
		$method = strtolower($_SERVER['REQUEST_METHOD']);

		if (in_array($method, $this->http_methods)) {
			return $method;
		}

		return 'get';
	}

	/**
	 * Match wild cards
	 *
	 * Check if any wild cards are supplied.
	 *
	 * This will return false if there is a mis-match anywhere in the route, 
	 * or it will return an array with the key => values being the user supplied variable names.
	 *
	 * If no variable names are supplied an empty array will be returned.
	 *
	 * TODO - Support for custom regex
	 *
	 * @param string $route The user-supplied route (with wild cards) to match against
	 *
	 * @return mixed
	 */
	private function _match_wild_cards($route) {
		$variables = array();

		$exp_request = explode('/', $this->request_uri);
		$exp_route = explode('/', $route);

		if (count($exp_request) == count($exp_route)) {
			foreach ($exp_route as $key => $value) {
				if ($value == $exp_request[$key]) {
					// So far the routes are matching
					continue;
				}
				elseif ($value[0] == '(' && substr($value, -1) == ')') {
					// A wild card has been supplied in the route at this position
					$strip = str_replace(array('(', ')'), '', $value);
					$exp = explode(':', $strip);

					$wc_type = $exp[0];

					if (array_key_exists($wc_type, $this->wild_cards)) {
						// Check if the regex pattern matches the supplied route segment
						$pattern = $this->wild_cards[$wc_type];
						
						if (preg_match($pattern, $exp_request[$key])) {
							if (isset($exp[1])) {
								// A variable was supplied, let's assign it
								$variables[$exp[1]] = $exp_request[$key];
							}

							// We have a matching pattern
							continue;
						}
					}
				}
				
				// There is a mis-match
				return false;
			}

			// All segments match
			return $variables;
		}

		return false;
	}

}