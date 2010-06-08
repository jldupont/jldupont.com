<?php
/**
 * Gliffy_Delicious
 * Handles the representation of a Gliffy URL
 * provided through a Del.icio.us post
 *
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package Gliffy
 * @dependencies JLD/Delicious
 * @example
 * 
 */

class JLD_Gliffy_Delicious {

	/**
	 * REGEX patterns used to locate
	 * the diagram identifier
	 */
	static $formats = array(
	
		'/publish\/(\d+)\//siU',
		'/pubdoc\/(\d+)\//siU',

		'/publish\/(\d+)$/siU',
		'/pubdoc\/(\d+)$/siU',
	
	);

	/**
	 * Extracts the diagram ID from a JLD_DeliciousPost 
	 * object instance.
	 * The ID integer is located in the "link" variable
	 * and can come in different formats.
	 * 
	 * @param $obj JLD_DeliciousPost object instance
	 * @return $id integer
	 */
	public static function extractId( $obj ) {
	
		if (!( $obj instanceof JLD_DeliciousPost ))
			throw new Exception( __METHOD__.": requires JLD_DeliciousPost object instance" );
	
		$id = null;
			
		$link = $obj->link;
		
		foreach( self::$formats as $pattern ) {
		
			$result = preg_match( $pattern, $link, $match );

			if ( $result === 1 )
				$id = $match[ 1 ];
		}
	
		return $id;
	}

	public static function extractTitle( $obj ) {

		if (!( $obj instanceof JLD_DeliciousPost ))
			throw new Exception( __METHOD__.": requires JLD_DeliciousPost object instance" );
	
		return $obj->title;
	}

} // end class definition