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

class JLD_MindMeister_frob 
	extends JLD_MindMeister_Object {

	public function __construct( $obj ) {
		$this->name  = 'frob';	
		$this->value = (string) $obj->frob;
	}
	
}//end definition