<?php
/**
 * JLD_MindMeister
 *  
 *
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package MindMeister
 * @category OnlineTools
 * @example
 */

require_once 'Method.php';
require_once 'Liste.php';
require_once 'HTTP/Request.php';
require_once 'Exceptions.php';
require_once 'Object.php';

class JLD_MindMeister {

	const CLASS_PATH = 'JLD_MindMeister_';

	static $key  = null;
	static $skey = null;
	
	/**
	 * No need to instantiate from this class
	 */
	public function __construct( $api_key, $secret_key ) {
	
		$this->api_key  = $api_key;
		$this->secret_key = $secret_key;
	}
	
	/**
	 * 
	 */
	public static function factory( $classe, $args = null ) {
		
		$c = self::CLASS_PATH . $classe ;
		if (class_exists( $c ))
			return new $c( $args );
			
		$r = include dirname(__FILE__)."/Classes/$classe/$classe.php";
		if ( !$r )
			throw new Exception( __METHOD__. ": can't load class $classe" );
			
		if ( !class_exists( $c ))
			throw new Exception( __METHOD__. ": can't find class $classe" );
		
		return new $c( $args );		
	}
	/**
	 * 
	 */
	public function __call( $method, $args = null ) {

		if ( is_null( $this->api_key ) || is_null( $this->secret_key) )
			throw new Exception( __METHOD__.": key(s) not set");
	
		$method = strtolower( $method );
	
		$c = self::CLASS_PATH . 'Method_'. $method ;
		$m = "Methods/$method.php";
		
		if (class_exists( $c ))
			return $this->executeMethod ( $c , $args );
			
		$r = include dirname(__FILE__).'/'.$m;
		if ( !$r )
			throw new Exception( __METHOD__. ": can't load class $c associated with method $m" );
			
		if ( !class_exists( $c ))
			throw new Exception( __METHOD__. ": can't find class $c associated with method $m" );

		return $this->executeMethod ( $c , $args );		
	}
	/**
	 * 
	 */
	protected function executeMethod( &$classe, &$args ) {

		$o = new $classe( $this->api_key, $this->secret_key, $args );

		return $o->execute();
	}
	
}//end-of-class


//EOF