<?php

include "HTTP/Request.php";

// Prepare Request object
$request =& new HTTP_Request(	'http://www.web-bloks.com/srv/s4/jldupont/Mediawiki/' );
										
$request->setMethod( "HEAD" );

try
{
	$code = $request->sendRequest();			
}
catch(Exception $e)
{
	echo $e->getMessage();
}
		
if ( PEAR::isError( $code ))
	return "error!";

		
$head =	$request->getResponseHeader();
$body = $request->getResponseBody();
$code = $request->getResponseCode();

echo "code: " . $code . "\n";
var_dump( $head );