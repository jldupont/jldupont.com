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

class JLD_MindMeister_method_getlist 
	extends JLD_MindMeister_Method {
	
	const METHOD = 'mm.maps.getList';
	
	static $refList = array();
	
	public function __construct( &$key, &$secret, &$args ) {

		$this->setParam( 'method' , self::METHOD );

		parent::__construct( $key, $secret, $args );
	}

	protected function getRefList() {
		return self::$refList;
	}
}