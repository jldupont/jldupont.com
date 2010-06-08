<?php
/**
 * Gliffy
 *  
 *
 * @author Jean-Lou Dupont
 * @version 1.0.0
 * @package Gliffy
 * @dependencies [optional] JLD/Delicious
 * @example
 * 		$posts = new JLD_DeliciousPosts( 'jldupont/my-diagrams' );
 * 		$posts->run();
 * 		foreach( $o as $post ) {
 * 
 *			$this->assertEquals( $post instanceof JLD_DeliciousPost, true );
 * 			$g = JLD_Gliffy::newFromDeliciousPost( $post );
 * 
 *			$i = $g->getPictureIterator();
 *			foreach( $i as $index => $repr ) {
 * 				$this->assertEquals( is_string( $repr ) , true );
 * 				echo "\nrepresention $index: $repr";
 * 			}
 * 		}
 */

require_once 'Gliffy_Delicious.php';
require_once 'Gliffy_PictureIterator.php';
require_once 'Gliffy_PictureRepresentation.php';

class JLD_Gliffy { 

	var $title = null;
	var $link  = null;

	/**
	 * API end-point
	 */
	static $service = "http://www.gliffy.com/";
	
	static $defaultRepresentation = 'jpg_large';
	
	/**
	 * CONSTANT that needs to be adjusted
	 * if new representations are added to self::$repr
	 */
	const COUNT_PICTURE_REPRESENTATION = 4;
	/**
	 * Supported representations
	 */
	static $repr = array(

		# PICTURE representations
		'jpg_large'	=> array( 'type' => 'picture', 'url' => 'http://www.gliffy.com/pubdoc/%id%/L.jpg' ),
		'jpg_medium'=> array( 'type' => 'picture', 'url' => 'http://www.gliffy.com/pubdoc/%id%/M.jpg' ),
		'jpg_small'	=> array( 'type' => 'picture', 'url' => 'http://www.gliffy.com/pubdoc/%id%/S.jpg' ),
		'jpg_thumb'	=> array( 'type' => 'picture', 'url' => 'http://www.gliffy.com/pubdoc/%id%/T.jpg' ),

		# PAGE representation
		'page'		=> array( 'type' => 'page', 'url' => 'http://www.gliffy.com/publish/%id%' ),
	);

	/**
	 * List of possible 'picture' representations
	 * Must be correlated to self::$repr 
	 */
	static $pict_repr = array(
	
		'jpg_large',
		'jpg_medium',
		'jpg_small',
		'jpg_thumb'
		
	);
	
	var $id = null;
	
	/*********************************************************
	 * 						PUBLIC Interface
	 ********************************************************/	
	
	public function __construct( $id = null, $title = null ) {
	
		$this->id = $id;
		$this->title = $title;
		
	}
	/**
	 * FACTORY instance from DeliciousPost instance
	 * 
	 * @return JLD_Gliffy object instance
	 */
	public static function newFromDeliciousPost( $obj ) {
	
		if (!( $obj instanceof JLD_DeliciousPost ))
			throw new Exception( "requires an instance of JLD_DeliciousPost class" );

		$id = JLD_Gliffy_Delicious::extractId( $obj );
		$title = JLD_Gliffy_Delicious::extractTitle( $obj );
		
		return new JLD_Gliffy( $id, $title );
	}
	/**
	 * Returns the title of this diagram
	 */
	public function getTitle() {
	
		return $this->title;
	}
	/**
	 * Returns an URL pointing to the requested
	 * representation
	 * 
	 * @param $repType string: representation type
	 * @return $url string
	 */
	public function getUrl( $repType = null ) {
	
		if ( is_null( $this->id ) )
			throw new Exception( "diagram id not initialized" );
	
		if ( $repType === null )
			$repType = self::$defaultRepresentation;
			
		if (!( array_key_exists( $repType, self::$repr ) ))
			throw new Exception( "representation type not supported" );

		return $this->formatForRepresentationType( $repType );
	}
	/**
	 * Returns an iterator object instance
	 * which can iterate over all the picture
	 * representation of this class
	 */
	public function getPictureIterator() {
	
		return new JLD_Gliffy_PictureIterator( $this );
	}
	/*********************************************************
	 * 					PUBLIC/friend interface
	 * NOTE: used by 'JLD_Gliffy_PictureIterator'
	 ********************************************************/	
	public function getPictureRepresentationCount() {

		return self::COUNT_PICTURE_REPRESENTATION;
	}
	public function getPictureRepresentationByIndex( $index ) {
		
		return self::$pict_repr[ $index ];
	}
	/*********************************************************
	 * 						PROTECTED Interface
	 ********************************************************/	
	
	/**
	 * Actually performs the formatting based on the required
	 * representation type.
	 * 
	 * @param $repType string
	 * @return $url string
	 */
	protected function  formatForRepresentationType( $repType ) {
	
		$format = self::$repr[ $repType ]['url'];
		
		return str_replace( '%id%', $this->id, $format );
	}
	
} // end class definition
