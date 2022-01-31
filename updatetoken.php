<pre><?php
function shopify_call($token, $shop, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array()) {
    
	// Build URL
	$url = "https://" . $shop .$api_endpoint;
	if (!is_null($query) && in_array($method, array('GET', 	'DELETE'))) $url = $url . "?" . http_build_query($query);
	// Configure cURL
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, TRUE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 3);
	// curl_setopt($curl, CURLOPT_SSLVERSION, 3);
	curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	// Setup headers
	$request_headers[] = "";
	if (!is_null($token)) $request_headers[] = "X-Shopify-Access-Token: " . $token;
	curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
	if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
		if (is_array($query)) $query = http_build_query($query);
		curl_setopt ($curl, CURLOPT_POSTFIELDS, $query);
	}
    
	// Send request to Shopify and capture any errors
	$response = curl_exec($curl);
	$error_number = curl_errno($curl);
	$error_message = curl_error($curl);
	// Close cURL to be nice
	curl_close($curl);
	// Return an error is cURL has a problem
	if ($error_number) {
		return $error_message;
	} else {
		// No error, return Shopify's response by parsing out the body and the headers
		$response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
		// Convert headers into an array
		$headers = array();
		$header_data = explode("\n",$response[0]);
		$headers['status'] = $header_data[0]; // Does not contain a key, have to explicitly set
		array_shift($header_data); // Remove status, we've already set it above
		foreach($header_data as $part) {
			$h = explode(":", $part);
			$headers[trim($h[0])] = trim($h[1]);
		}
		// Return headers and Shopify's response
		return array('headers' => $headers, 'response' => $response[1]);
	}
    
}
	/* Define your APP`s key and secret*/
	define('SHOPIFY_API_KEY','aa16c1c5ed0aceb99592c4fff8776d01');
	define('SHOPIFY_SECRET','d52fdcbcbc27fda049d77fe7682d88da');
	
// Get our helper functions

// Set variables for our request
session_start();
$shop = $_SESSION["shop"];
$api_key = SHOPIFY_API_KEY;
$shared_secret = SHOPIFY_SECRET;
$code = $_SESSION["code"];
$timestamp = $_SESSION["timestamp"];
$signature = $_SESSION["hmac"];
// Compile signature data
print_r($_SESSION);


// Use signature data to check that the response is from Shopify or not
 
	// Set variables for our request
	$query = array(
		"Content-type" => "application/json", // Tell Shopify that we're expecting a response in JSON format
		"client_id" => $api_key, // Your API key
		"client_secret" => $shared_secret, // Your app credentials (secret key)
		"code" => $code // Grab the access key from the URL
	);
	// Call our Shopify function
	echo '>'.$shopify_response = shopify_call(NULL, $shop, "/admin/oauth/access_token", $query, 'POST');
	print_r($shopify_response);
	// Convert response into a nice and simple array
	$shopify_response = json_decode($shopify_response['response'], TRUE);
	// Store the response
	$token = $shopify_response['access_token'];
	// Show token (DO NOT DO THIS IN YOUR PRODUCTION ENVIRONMENT)
	echo $token;
 
?>