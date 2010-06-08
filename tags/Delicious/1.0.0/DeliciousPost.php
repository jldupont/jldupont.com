<?php
/**
 * DeliciousPost
 * Base class
 *
 * @author Jean-Lou Dupont
 * @version 1.0.0
 * @package Delicious
 * @dependencies PEAR::HTTP_Request
 */

class JLD_DeliciousPost {

	/**
	 * Valid parameters for a 'post'
	 */
	static $parameters = array(
		'title'			=> true,
		'link'			=> true,
		'description'	=> true,
	);
	/**
	 * Instance parameters
	 */
	var $params = array();
	
	/*=======================================================================
	 *						PUBLIC INTERFACE 
	 *======================================================================*/	
	
	/**
	 * Constructor
	 */
	public function __construct( $obj ) {

		if ( $obj instanceof ArrayObject )
			return $this->newFromArray( $obj );
			
		if ( $obj instanceof SimpleXMLElement )
			return $this->newFromXml( $obj );
			
		throw new Exception( __METHOD__.": invalid parameter" );
	}
	
	/*=======================================================================
	 *						WILDCARD INTERFACE 
	 *======================================================================*/	
	
	public function __isset( $key ) {
	
		if ( array_key_exists( $key, self::$parameters ) )
			return isset( $this->params[ $key ] );
		
		return false;
	}
/*	
	public function __unset( $key ) {

		if ( array_key_exists( $key, self::$parameters ) )
			return unset( $this->params[ $key ] );
	
		return false;
	}
*/
	public function __get( $key ) {
	
		if ( array_key_exists( $key, self::$parameters ) )
			return $this->params[ $key ];
		return null;
	}
	
	public function __set( $key, $value ) {
	
		if ( array_key_exists( $key, self::$parameters ) ) {
			$this->params[ $key ] = $value;
			return $this;	
		}
		
		throw new Exception( __METHOD__.": invalid key" );
	}
	
	/*=======================================================================
	 *						PROTECTED INTERFACE 
	 *======================================================================*/	
	/** 
	 * FACTORY object from SimpleXMLElement 
	 */
	protected function & newFromXml( &$xmlObj ) {

		foreach( self::$parameters as $key => &$value ) {
			if ( isset( $xmlObj->$key ) )
				$this->params[$key] = $xmlObj->$key;
		}
		return $this;	
	}
	/**
	 * FACTORY object from simple array
	 */
	protected function & newFromArray( &$a ) {
	
		foreach( self::$parameters as $key => &$value ) {
			if ( isset( $a[ $key ] ) )	
				$this->params[ $key ] = $a[ $key ];			
		}
		return $this;
	}
	
} //end class definition