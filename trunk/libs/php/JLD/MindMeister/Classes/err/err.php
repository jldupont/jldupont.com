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

class JLD_MindMeister_err {

	var $code = null;
	var $msg  = null;

	public function __construct( $obj ) {

		$this->code = (string) $obj->err['code'];
		$this->msg  = (string) $obj->err['msg'];		
	
	}

}//end definition