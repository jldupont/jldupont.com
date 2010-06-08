<?php
/**
 * Base exception class
 *
 * @author Jean-Lou Dupont
 * @package JLD
 * @subpackage System
 * @version 1.0.1
 */
//<source lang=php>
require 'JLD/System/ExceptionList.php';

class JLD_System_Exception extends Exception
{
	/**
	 * @var mixed An array containing the registered 'listeners'
	 */
	static $listeners = array();
	
	/**
	 * Each listener will be called in turn when an exception turns up.
	 * @param mixed Object reference
	 */
	public static function registerListener( &$l )
	{
		
	}
	
}//end class
//</source>