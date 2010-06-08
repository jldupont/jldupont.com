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

class JLD_MindMeister_auth 
	extends JLD_MindMeister_Object {

	public function __construct( $obj ) {
	
		$this->name  = 'auth';	
		$this->value = (string) $obj->auth;
	}

}//end definition