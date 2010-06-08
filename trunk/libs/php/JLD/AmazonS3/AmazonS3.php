<?php
/**
 * @author Jean-Lou Dupont
 * @package AmazonS3
 * @version $Id: AmazonS3.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
/*
// <source lang=php>

/*
  	getBuckets
	createBucket( $bucket )   
	deleteBucket( $bucket )	
	getDirectorySize( $bucket, $prefix = "" )	
	getBucketContents(	$bucket, $prefix = null, $delim = null, $marker = null  )	
	bucketExists( $bucket )	
	
	getObject( $bucket, $object, &$document )	
	putObject(	$bucket, $object, &$document, $ext = null, $public = null )
	deleteObject( $bucket, $object )	
	getObjectHead($bucket, $object, &$responseHeaders )	
	getObjectInfo( $bucket, $object )	
	deleteObject( $bucket, $object )	
	objectExists($bucket, $object)	
*/

/*
   This class assumes that the following extensions
   are installed and accessible.
   Use Pear to grab these.
*/
require_once 'HTTP/Request.php';
require_once 'Crypt/HMAC.php';

class AmazonS3
{
	// constants
	const c_timeout = 5;
	const c_port = 80;
	
	const amazon_site = 'http://s3.amazonaws.com';
	
	var $keyId;
	var $secretKey;
	
	var $responseHeaders = null;
	var $responseBody = null;
	var $put_responseBody = null; // debug.
	var $responseCode = null;
	var $requestHeaders = null;
	var $put_requestHeaders = null; // debug.

	// statics
	static $timeout = null;
	static $port = null;
	static $site = null;
	
	public function __construct( $keyId, $secretKey )
	{
		$this->keyId = $keyId;
		$this->secretKey = $secretKey;
		
		$this->init();
	}
	/**
	 * Initialization of the static variables.
	 */
	public function init()
	{
		// initialize the defaults.
		self::$timeout = self::c_timeout;
		self::$port = self::c_port;
		self::$site = self::amazon_site;
	}
	public static function setTimeout( $t ) { self::$timeout = $t;	}
	public static function setPort( $p )	{ self::$port = $p; 	}
	public static function setSite( $s )	{ self::$site = $s; 	}

// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// BUCKET RELATED

	/**
	 * GET BUCKETS
	 */
	public function getBuckets( )
	{
		$req = array(	"verb"		=> "GET",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"acl"		=> null,
						"resource"	=> "/",
					);
		$result = $this->doRequest($req, null /*no parameters*/ );
		
		if (empty( $this->responseBody ))
			return null;
			
		$r = preg_match_all("@<"."Name>(.*?)<"."/Name>@", $this->responseBody, $matches);
		if (($r === false) || ($r === 0))
			return null;
			
		return $matches[1];
	}
	/**
	 * CREATE BUCKET
	 */
	public function createBucket( $bucket )
	{
		$req = array(	"verb"		=> "PUT",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"resource"	=> "/$bucket",
					);
		$result = $this->doRequest( $req );
		return $this->isOk($result);
	}
	/**
	 * DELETE BUCKET
	 */
	public function deleteBucket( $bucket )
	{
		$req = array(	"verb"		=> "DELETE",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"resource"	=> "/$bucket",
					);
					
		$result = $this->doRequest( $req );
		return $this->isOk($result);
	}
	
	/**
	 * Returns the size of a specific $bucket/$prefix
	 */
	public function getDirectorySize( $bucket, $prefix = "" )
	{
		$total = 0;
		$foo = $this->getBucketContents($bucket, $prefix);
		
		if(!is_array($foo)) 
			return false;

		foreach($foo as $bar)
			if($bar['type'] == "key")
				$total += $bar['size'];

		return $total;
	}

	/**
	 * Returns the content of a specific bucket
	 */
	function getBucketContents(	$bucket, 
								$prefix = null, 
								$delim = null, 
								$marker = null  )
	{
		$req = array(	"verb"		=> "GET",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"resource"	=> "/$bucket",
					);

		$prefix = trim( $prefix );
		if($prefix[0] == "/") 
			$prefix[0] = "";
			
		$params = array(	"prefix"	=> $prefix, 
							"marker"	=> $marker, 
							"delimiter" => $delim );
							
		$result = $this->doRequest($req, $params );		

		$document = $this->responseBody;

		$keys = array();
		
		// don't waste time
		if (empty( $document ))
			return $keys; // empty array
		
		$r = preg_match_all("@<"."Contents>(.*?)</"."Contents>@", $document, $matches);
		if ( ( $r === false) || ( $r === 0 ))
			return $keys; // empty array
		
		if (!empty( $matches )) // paranoia
			foreach($matches[1] as $match)
			{
				$r = preg_match_all('@<(.*?)>(.*?)</\1>@', $match, $keyInfo);
				if (($r === false) || ($r === 0))
					continue;
					
				list($name, $date, $hash, $size) = $keyInfo[2];
				$hash = str_replace("&"."quot".";", "", $hash);
				$keys[] = array(	"name" => $name, 
									"date" => $date, 
									"hash" => $hash, 
									"size" => $size, 
									"type" => "key"  );
			}

		$r = preg_match_all("@<"."Prefix>(.*?)<"."/Prefix>@", $document, $matches);
		
		// return our results up to here
		if ( ( $r === false) || ( $r === 0 ))
			return $keys;
		
		array_shift($matches[1]);
		foreach($matches[1] as $match)
			$keys[] = array( "name" => $match, "type" => "prefix" );

		$r = preg_match('@<'.'NextMarker>(.*?)<'.'/NextMarker>@', $document, $matches);

		if(isset($matches[1]) && strlen($matches[1]) > 0)
		{
			preg_match('@<'.'NextMarker>(.*?)<'.'/NextMarker>@', $result, $matches);
			$keys = array_merge($keys, $this->getBucketContents($bucket, $prefix, $delim, $matches[1]));
		}

		return $keys;
	}
	
	/**
	 * Verifies the existence of a specific bucket
	 */
	function bucketExists( $bucket )
	{
		$buckets = $this->getBuckets();
		
		if (empty( $buckets ))
			return false;

		return in_array($bucket, $buckets );
	}

// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// OBJECT RELATED

	/**
	 * GET OBJECT
	 */
	public function getObject( $bucket, $object, &$document )
	{
		self::formatObject( $object );
					
		$req = array(	"verb"		=> "GET",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"resource"	=> "/$bucket" . $object,
					);
		return $this->doRequest($req, null, $document );
	}
	/**
	 * PUT OBJECT
	 */	
	public function putObject(	$bucket, 
								$object, 
								&$document,
								$ext = null,
								$public = null )
	{
		$type = isset(self::$mime_types[$ext]) ? self::$mime_types[$ext] : 
												"application/octet-stream";
	
		$acl = isset($public) ? "public-read" : null;

		self::formatObject( $object );
					
		$req = array(	"verb"		=> "PUT",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"resource"	=> "/$bucket" . $object,
						"type" 		=> $type,
						"acl"		=> $acl,
					);
					
		$result = $this->doRequest($req, null, $document, true );
		
		$this->put_responseBody   = $this->responseBody;
		$this->put_requestHeaders = $this->requestHeaders;
		
		$info = $this->getObjectInfo( $bucket, $object );

		return ($info['hash'] == md5($document));
	}
	/**
	 * DELETE OBJECT
	 */
	public function deleteObject( $bucket, $object )
	{
		self::formatObject( $object );			
		
		$req = array(	"verb"		=> "DELETE",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"resource"	=> "/$bucket" . $object,
					);
					
		$result = $this->doRequest( $req, null, $document );
		return !$this->objectExists($bucket, $object);
	}
	/**
	 * HEAD OBJECT	
	 *
	 * 'x-amz-id-2'
	 * 'x-amz-request-id'
	 * 'date'
	 * 'last-modified'
	 * 'etag'
	 * 'content-type'
	 * 'content-length'
	 * 'server'
	 */
	public function getObjectHead($bucket, $object, &$responseHeaders )
	{
		self::formatObject( $object );

		$req = array(	"verb"		=> "HEAD",
						"md5"		=> null,
						"type"		=> null,
						"headers"	=> null,
						"resource"	=> "/$bucket" . $object,
					);

		$result = $this->doRequest( $req );
		
		$responseHeaders = $this->responseHeaders;
		
		return $result;
	}
	/**
	 * Gets information about a specific object
	 *
	 * 'name', 'date', 'hash', 'size', 'type'
	 */
	public function getObjectInfo( $bucket, $object )
	{
		$object = $this->getBucketContents($bucket, $object);
		if(count($object) == 0) 
			return false;
			
		if($object[0]['type'] == "prefix") 
			return false;
			
		return $object[0];
	}
	/**
	 * Returns the existence status of an object.
	 *
	 */
	function objectExists($bucket, $object)
	{
		return ($this->getObjectInfo($bucket, $object) !== false);
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * This method handles *all* requests to AmazonS3.
	 */
	public function doRequest(	&$req, 
								$params = null,		// uri level parameters
								&$document = null, 	// as input: only used in PUT request
								$putBody = false )	// signals a PUT request									
	{
		if(isset($req['resource']))
			$req['resource'] = self::urlencodeClean( $req['resource'] );

		$verb = $req['verb'];
    	$date = gmdate( DATE_RFC2822 );

		$sig = $this->signature($req, $date, $this->secretKey );
		
		$args = array();
		$args['Date'] = $date;
		$args["Authorization"] = "AWS ".
								"{$this->keyId}".	// keep the "" !!!
								":".
								$sig;
		
		if(isset($req['acl']))	
			$args["x-amz-acl"] = $req['acl'];
		if(isset($req['type']))
			$args["content-Type"] = $req['type'];
		if(isset($req['md5']))
			$args["Content-Md5"] = $req['md5'];
	
		if(is_array($req['headers']))
			foreach($req['headers'] as $key => $val)
				$args[$key] =$val;

		// deal with parameters
		$parameters = null;
		if(is_array($params))
		{
			$parameters = "?";
			foreach($params as $key => $val)
				$parameters .= "$key=" . urlencode($val) . "&";
		}
	
		// Prepare Request object
		$request =& new HTTP_Request( self::$site . $req['resource'] . $parameters );
		$request->_timeout = self::$timeout;
		
		$request->setMethod( $verb );

		// do headers
		foreach( $args as $key => $value )
			$request->addHeader( $key, $value );

		// file upload case.		
		if ($putBody)
			$request->setBody( $document );

		$error = $request->sendRequest();

		// return all response headers.
		$this->requestHeaders	= $args;
	    $this->responseHeaders	= 			$request->getResponseHeader();
		$this->responseBody		= $document = $request->getResponseBody();
		$this->responseCode		= $code 	= $request->getResponseCode();
	
		// if one requires the raw returned code, then use
		// $this->responseCode
		return ($code === 200);
	}
	/**
	 * Verifies if the operation went without errors.
	 */
	function isOk( &$result )
	{
		$r = preg_match("@<Error>.*?<Message>(.*?)</Message>.*?</Error>@", $result, $matches);
		
		if(($r !== false) && ($r !== 0))
		{
			$this->_error = $matches[1];
			return false;
		}

		return true;
	}
	/**
	 * Sign a request.
	 */
	function signature( &$req, $date, $secretKey )
	{
		if (isset($req['acl']))
			$acl = 'x-amz-acl:'.$req['acl']."\n";
		else
			$acl = null;
			
		// Build and sign the string
		$str  = 			$req['verb'] . "\n" . 
							$req['md5']  . "\n" . 
							$req['type'] . "\n" . 
							$date        . "\n".
							$acl. 
							$req['resource'];
							
	    $hasher =& new Crypt_HMAC($secretKey, "sha1");
	    return $this->hex2b64($hasher->hash($str));
	}
	/**
	 * Helper for the signature method.
	 */
	function hex2b64($str) 
	{
	    $raw = '';
	    for ($i=0; $i < strlen($str); $i+=2)
	        $raw .= chr(hexdec(substr($str, $i, 2)));
	    return base64_encode($raw);
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 *  Special urlencode
	 */
	static function urlencodeClean( &$input )
	{
		$input = urlencode( $input );
		$input = str_replace( "%2F", "/", $input );
		
		return $input;
	}

	/**
	 * Format an object.
	 */
	static function formatObject( &$object )
	{
		$object = trim( $object );
		if(substr($object, 0, 1) != "/" ) 
			$object = "/$object";
			
		return $object;
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	
	/**
	 * DEBUG function
	 */
	function ByteToString( $str )
	{
		$ret = null;
		$liste = explode(" ", $str );
		foreach( $liste as $char )
			$ret .= chr( hexdec($char) );
		return $ret;
	}
	/**
	 * DEBUG function
	 */
	function StringToByte( $str )
	{
		$a = str_split( $str );
		
		$callback = create_function('$i','return dechex( ord( $i ));');
		$b = array_map( $callback, $a );
		
		$str = implode(" ", $b );
		
		return $str;
	}

/**
 * MIME types
 *
 * Helper table.
 */	
static $mime_types = array(
"323" => "text/h323", 
"acx" => "application/internet-property-stream", 
"ai" => "application/postscript", 
"aif" => "audio/x-aiff", 
"aifc" => "audio/x-aiff", 
"aiff" => "audio/x-aiff",
"asf" => "video/x-ms-asf", 
"asr" => "video/x-ms-asf", 
"asx" => "video/x-ms-asf", 
"au" => "audio/basic", 
"avi" => "video/quicktime", 
"axs" => "application/olescript", 
"bas" => "text/plain", 
"bcpio" => "application/x-bcpio", 
"bin" => "application/octet-stream", 
"bmp" => "image/bmp",
"c" => "text/plain", 
"cat" => "application/vnd.ms-pkiseccat", 
"cdf" => "application/x-cdf", 
"cer" => "application/x-x509-ca-cert", 
"class" => "application/octet-stream", 
"clp" => "application/x-msclip", 
"cmx" => "image/x-cmx", 
"cod" => "image/cis-cod", 
"cpio" => "application/x-cpio", 
"crd" => "application/x-mscardfile",
"crl" => "application/pkix-crl", 
"crt" => "application/x-x509-ca-cert", 
"csh" => "application/x-csh", 
"css" => "text/css", 
"dcr" => "application/x-director", 
"der" => "application/x-x509-ca-cert", 
"dir" => "application/x-director", 
"dll" => "application/x-msdownload", 
"dms" => "application/octet-stream", 
"doc" => "application/msword",
"dot" => "application/msword", 
"dvi" => "application/x-dvi", 
"dxr" => "application/x-director", 
"eps" => "application/postscript", 
"etx" => "text/x-setext", 
"evy" => "application/envoy", 
"exe" => "application/octet-stream", 
"fif" => "application/fractals", 
"flr" => "x-world/x-vrml", 
"gif" => "image/gif",
"gtar" => "application/x-gtar", 
"gz" => "application/x-gzip", 
"h" => "text/plain", 
"hdf" => "application/x-hdf", 
"hlp" => "application/winhlp", 
"hqx" => "application/mac-binhex40", 
"hta" => "application/hta", 
"htc" => "text/x-component", 
"htm" => "text/html", 
"html" => "text/html",
"htt" => "text/webviewhtml", 
"ico" => "image/x-icon", 
"ief" => "image/ief", 
"iii" => "application/x-iphone", 
"ins" => "application/x-internet-signup", 
"isp" => "application/x-internet-signup", 
"jfif" => "image/pipeg", 
"jpe" => "image/jpeg", 
"jpeg" => "image/jpeg", 
"jpg" => "image/jpeg",
"js" => "application/x-javascript", 
"latex" => "application/x-latex", 
"lha" => "application/octet-stream", 
"lsf" => "video/x-la-asf", 
"lsx" => "video/x-la-asf", 
"lzh" => "application/octet-stream", 
"m13" => "application/x-msmediaview", 
"m14" => "application/x-msmediaview", 
"m3u" => "audio/x-mpegurl", 
"man" => "application/x-troff-man",
"mdb" => "application/x-msaccess", 
"me" => "application/x-troff-me", 
"mht" => "message/rfc822", 
"mhtml" => "message/rfc822", 
"mid" => "audio/mid", 
"mny" => "application/x-msmoney", 
"mov" => "video/quicktime", 
"movie" => "video/x-sgi-movie", 
"mp2" => "video/mpeg", 
"mp3" => "audio/mpeg",
"mpa" => "video/mpeg", 
"mpe" => "video/mpeg", 
"mpeg" => "video/mpeg", 
"mpg" => "video/mpeg", 
"mpp" => "application/vnd.ms-project", 
"mpv2" => "video/mpeg", 
"ms" => "application/x-troff-ms", 
"mvb" => "application/x-msmediaview", 
"nws" => "message/rfc822", 
"oda" => "application/oda",
"p10" => "application/pkcs10", 
"p12" => "application/x-pkcs12", 
"p7b" => "application/x-pkcs7-certificates", 
"p7c" => "application/x-pkcs7-mime", 
"p7m" => "application/x-pkcs7-mime", 
"p7r" => "application/x-pkcs7-certreqresp", 
"p7s" => "application/x-pkcs7-signature", 
"pbm" => "image/x-portable-bitmap", 
"pdf" => "application/pdf", 
"pfx" => "application/x-pkcs12",
"pgm" => "image/x-portable-graymap", 
"php" => "text/x-php",
"pko" => "application/ynd.ms-pkipko", 
"pma" => "application/x-perfmon", 
"pmc" => "application/x-perfmon", 
"pml" => "application/x-perfmon", 
"pmr" => "application/x-perfmon", 
"pmw" => "application/x-perfmon", 
"png" => "image/png", 
"pnm" => "image/x-portable-anymap", 
"pot" => "application/vnd.ms-powerpoint", 
"ppm" => "image/x-portable-pixmap",
"pps" => "application/vnd.ms-powerpoint", 
"ppt" => "application/vnd.ms-powerpoint", 
"prf" => "application/pics-rules", 
"ps" => "application/postscript", 
"pub" => "application/x-mspublisher", 
"qt" => "video/quicktime", 
"ra" => "audio/x-pn-realaudio", 
"ram" => "audio/x-pn-realaudio", 
"ras" => "image/x-cmu-raster", 
"rgb" => "image/x-rgb",
"rmi" => "audio/mid", 
"roff" => "application/x-troff", 
"rtf" => "application/rtf", 
"rtx" => "text/richtext", 
"scd" => "application/x-msschedule", 
"sct" => "text/scriptlet", "
setpay" => "application/set-payment-initiation", 
"setreg" => "application/set-registration-initiation", 
"sh" => "application/x-sh", 
"shar" => "application/x-shar",
"sit" => "application/x-stuffit", 
"snd" => "audio/basic", 
"spc" => "application/x-pkcs7-certificates", 
"spl" => "application/futuresplash", 
"src" => "application/x-wais-source", 
"sst" => "application/vnd.ms-pkicertstore", 
"stl" => "application/vnd.ms-pkistl", 
"stm" => "text/html", 
"svg" => "image/svg+xml", 
"sv4cpio" => "application/x-sv4cpio",
"sv4crc" => "application/x-sv4crc", 
"t" => "application/x-troff", 
"tar" => "application/x-tar", 
"tcl" => "application/x-tcl", 
"tex" => "application/x-tex", 
"texi" => "application/x-texinfo", 
"texinfo" => "application/x-texinfo", 
"tgz" => "application/x-compressed", 
"tif" => "image/tiff", 
"tiff" => "image/tiff",
"tr" => "application/x-troff", 
"trm" => "application/x-msterminal", 
"tsv" => "text/tab-separated-values", 
"txt" => "text/plain", 
"uls" => "text/iuls", 
"ustar" => "application/x-ustar", 
"vcf" => "text/x-vcard", 
"vrml" => "x-world/x-vrml", 
"wav" => "audio/x-wav", 
"wcm" => "application/vnd.ms-works",
"wdb" => "application/vnd.ms-works", 
"wks" => "application/vnd.ms-works", 
"wmf" => "application/x-msmetafile", 
"wps" => "application/vnd.ms-works", 
"wri" => "application/x-mswrite", 
"wrl" => "x-world/x-vrml", 
"wrz" => "x-world/x-vrml", 
"xaf" => "x-world/x-vrml", 
"xbm" => "image/x-xbitmap", 
"xla" => "application/vnd.ms-excel",
"xlc" => "application/vnd.ms-excel", 
"xlm" => "application/vnd.ms-excel", 
"xls" => "application/vnd.ms-excel", 
"xlt" => "application/vnd.ms-excel", 
"xlw" => "application/vnd.ms-excel", 
"xof" => "x-world/x-vrml", 
"xpm" => "image/x-xpixmap", 
"xwd" => "image/x-xwindowdump", 
"z" => "application/x-compress", 
"zip" => "application/zip"
);

	
} // end class AmazonS3

// </source>