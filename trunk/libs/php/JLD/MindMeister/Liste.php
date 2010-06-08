<?php
/**
 * JLD_MindMeister_Liste
 *  
 *
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package MindMeister
 * @category OnlineTools
 * @example
 */
class JLD_MindMeister_Liste 
	implements Iterator {

	var $liste = array();
	
	public function __construct() {
	}
	

	/*********************************************************
	 * 				Iterator Interface
	 ********************************************************/	
	public function count() {

		return count( $this->liste );
	}
	public function current() {

		return current( $this->liste );
	}
	public function key() {

		return key( $this->liste );
	}
	public function next() {

		return next( $this->liste );
	}
	public function rewind() {
	
		return reset( $this->liste );
	}
	public function valid() {

		return ( key( $this->liste ) !== null );
	}	
	
}//end-of-class


//EOF