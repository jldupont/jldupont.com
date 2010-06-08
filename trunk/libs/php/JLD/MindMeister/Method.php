<?php
/**
 * JLD_MindMeister_Method
 *  
 *
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package MindMeister
 */

	abstract
class JLD_MindMeister_Method {

	/**
	 * REST API end-point
	 */
	const DEFAULT_REST = 'http://www.mindmeister.com/services/rest/?api_key=%api_key%&method=%method%&api_sig=%api_sig%';

	/**
	 * List of parameters that are not to be included when
	 * calculating the signature
	 */
	static $excludeFromSignature = array(
		'api_sign', 'secret_key', // 'api_key', 
	);
	
	/**
	 * Parameters list for this method
	 */
	var $params = array();
	
	var $args = null;
	
	var $url = null;
	var $rep_headers = null;
	var $rep_body    = null;
	var $rep_code    = null;
	
	var $rest = null;
	
	public function __construct( &$api_key, $secret_key, &$args, $rest = null ) {
	
		$this->setParam( 'api_key' ,    $api_key );
		$this->setParam( 'secret_key' , $secret_key );		

		// don't include in $params just yet...
		$this->args = $args[0];
		
		// sub-classes should update this info if required.
		if ( is_null( $rest ) )
			$this->rest = self::DEFAULT_REST;
		else
			$this->rest = $rest;
	}
	/**
	 * 
	 */
	protected function setParam( $key, $value ) {
	
		$this->params[ $key ] = $value;
		return $this;
	}
	/**
	 * 
	 */
	protected function getParam( $key ) {
	
		return $this->params[ $key ];
	}
	/**
	 * Verify the parameter list given a reference list
	 * 
	 * 1) Verify that mandatory parameters are specified
	 * 2) Verify allowable values
	 */
	protected function verifyParamsList( ) {
	
		$refListe = $this->getRefList();
		
		$pl = array();
		
		foreach( $refListe as $key =>$entry ) {
		
			if ( !array_key_exists( $key, $this->args ))
				throw new JLD_MindMeister_Exception( "Missing mandatory parameter $key" );
			
			$value = $this->args[ $key ];
			
			$checkMethod = $entry[ 'm' ];

			if (!is_callable( $checkMethod ))
				throw new Exception( "method is not callable" );
			
			if ( !isset( $entry['a'] ) )
				throw new Exception( "allowed values not set in reference array" ); 
				
			$r = call_user_func( $checkMethod, $key, $value, $entry['a'] );
							
			if ( !$r )
				throw new JLD_MindMeister_Exception( "Parameter $key can not hold value $value" );
			
			$pl[ $key ] = $value;
		}
		
		return $pl;
	}
	public function checkStringArray( &$key, &$value, &$values ) {

		if ( !is_array( $values) )
			return ( $value == $values );
			
		return ( in_array( $value, $values ));
	}
	
	public function checkClass( &$key, &$value, &$values ) {
	
		if ( !is_array( $values) )
			return ( $value instanceof $values );
	
		foreach( $values as $possibleValue )
			if ( $value instanceof $possibleValue )
				return true;
				
		return false;
	}
	
	protected function setParamsList( &$args ) {
	
		foreach( $args as $key => &$value )
			$this->setParam( $key, $value );
	}
	
	/** 
	 * Needs to be defined in derived classes.
	 */
	abstract protected function getRefList();
	
	/**
	 * 
	 */
	public function execute( $doRequest = true ) {
		
		$verifiedParams = $this->verifyParamsList();
		$this->setParamsList( $verifiedParams );
	
		// get the signature
		$api_sig = $this->sign();
		$this->setParam( 'api_sig', $api_sig );
	
		$this->url = $this->formatURL( );
		
		echo $this->url . "\n";
		
		if ( $doRequest )
			return $this->doRequest( $this->url );
	}
	/**
	 * 
	 */
	protected function formatURL( ) {
	
		$url = $this->rest;
		
		foreach( $this->params as $key => $value ) {
		
			$value = $this->getParam( $key );
			if ( is_object( $value ) )
				$value = $value->getValue();
				
			$url = str_replace( '%' . $key . '%', $value , $url );	
		}
	
		return $url;
	
	}
	protected function doRequest( &$url ) {
	
		$request =& new HTTP_Request( $url );
		$request->_timeout = 5;
			
		// REDIRECTS are a requirement
		$request->_allowRedirects = false;
		
		$request->setMethod( "GET" );

		try
		{
			$exception = false;
			$code = $request->sendRequest();			
		}
		catch(Exception $e)
		{
			$exception = true;
		}
		
		if ( PEAR::isError( $code ) || $exception )
			return false;

	    $this->rep_headers = $request->getResponseHeader();
		$this->rep_body    = $request->getResponseBody();
		$this->rep_code    = $request->getResponseCode();

		if ( $this->rep_code != 200 )
			return $this->rep_code;
		
		$o = simplexml_load_string( $this->rep_body );
			
		if ( !is_object( $o ))
			throw new Exception( "network error" );
			
		if ( isset( $o->err ) )
			return JLD_MindMeister::factory( 'err', $o );
			
		return $o;
	}
	/**
	 * Sign the request
	 */
	protected function sign() {

		$key = $this->params['secret_key'];
		$ol = $this->orderParamsList();
		$to = "$key".$this->getStringToSign( $ol );
 
		$api_sig = md5( $to );
	
		return $api_sig;
	}
	/**
	 * Orders the parameters in alphabetical order
	 */
	protected function orderParamsList() {
	
		if ( empty( $this->params ))
			return array();
	
		$r = ksort( $this->params, SORT_STRING );
		
		if ( !$r )
			throw new Exception( __METHOD__.": can't sort parameters list" );
			
		return $this->params;
	}
	/**
	 * Extracts the value of each parameter and
	 * concatenates those to form a string
	 */
	protected function getStringToSign( &$orderedList ) {

		if ( empty( $orderedList ))
			return '';

		$str = '';
		
		foreach( $orderedList as $key => $value ) {
			if ( !in_array( $key, self::$excludeFromSignature ) ) {
				if ( is_object( $value ) ) 
					$value = $value->getValue();
				$str .= $key.$value;
			}
						
		}//foreach
		
		echo __METHOD__."string to sign: $str \n";
		
		return $str;
	}
	
}//end class