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

abstract class JLD_MindMeister_Object {

	var $name  = null;
	var $value = null;
	
	public function getName() {
		return $this->name;
	}

	public function getValue() {
		return $this->value;
	}
	
}//end-of-class


//EOF