<?php
/**
 * Gliffy
 *  
 *
 * @author Jean-Lou Dupont
 * @version 1.0.0
 * @package Gliffy
 * @dependencies [optional] JLD/Delicious
 */

class JLD_Gliffy_PictureRepresentation {

	var $title = null;
	var $ext   = null;
	var $id    = null;
	var $size  = null;

	static $_p = array(
	
		'title'	=> true,
		'ext'	=> true,
		'id'	=> true,
		'size'	=> true,
	);
	
	static $repr = array(

		'L'	=> 'http://www.gliffy.com/pubdoc/%id%/L.jpg',
		'M' => 'http://www.gliffy.com/pubdoc/%id%/M.jpg',
		'S'	=> 'http://www.gliffy.com/pubdoc/%id%/S.jpg',
		'T'	=> 'http://www.gliffy.com/pubdoc/%id%/T.jpg',
	);
	
	static $_extPatterns = array(
	
		'/L.jpg/siU'	=> array( 'ext'	=> 'jpg', 'size' => 'L' ),
		'/M.jpg/siU'	=> array( 'ext'	=> 'jpg', 'size' => 'M' ),
		'/S.jpg/siU'	=> array( 'ext'	=> 'jpg', 'size' => 'S' ),	
		'/T.jpg/siU'	=> array( 'ext'	=> 'jpg', 'size' => 'T' ),
		
	);
	
	public function __construct( $title, $id, $size, $ext ) {
	
		$this->title = $title;
		$this->id    = $id;
		$this->size  = $size;
		$this->ext   = $ext;
		
	}
	public function getUrl() {
	
		if ( $this->id === null )
			throw new Exception( __METHOD__.": id can not be null" );
		if ( $this->size === null )
			throw new Exception( __METHOD__.": size can not be null" );

		$p = self::$repr[$this->size];
		$url = str_replace( '%id%', $this->id, $p );
		
		return $url;
	}
	public static function newFromUrl( $title, $id, $url ) {

		$p = self::extractParams( $url );
		if ( $p === null )
			throw new Exception( __METHOD__.": invalid url ($url) ");
		$size = $p['size'];
		$ext  = $p['ext'];
		
		return new JLD_Gliffy_PictureRepresentation( $title, $id, $size, $ext );
	}
	
	public function __get( $key ) {
	
		$key = strtolower( $key );
		if ( $key == 'url' )
			return $this->getUrl();
	
		if (!array_key_exists( $key, self::$_p ))
			throw new Exception( __METHOD__.": invalid key $key");
			
		return $this->$key;
	}
	public function __set( $key, $value ) {

		if (!array_key_exists( $key, self::$_p ))
			throw new Exception( __METHOD__.": invalid key $key");
	
		$this->$key = $value;
		
		return $this;
	}
	
	/******************************************************************
	 * 					PROTECTED INTERFACE
	 ******************************************************************/
	
	protected static function extractParams( $url ) {
	
		$params = null;
	
		foreach( self::$_extPatterns as $pattern =>&$p ) {
			
			$result = preg_match( $pattern, $url, $match );
			if ( $result === 1 ) {
				$params = $p;
			}
		}
		
		return $params;
	}
	
}// end class definition