<?php
/**
 * Base exception class
 * Upon construction, posts an 'exception' message through the system.
 *
 * @author Jean-Lou Dupont
 * @package System
 * @subpackage Exception
 * @version @@package-version@@
 * @Id $Id: Exception.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php>
require_once 'JLD/System/Logger.php';

class JLD_System_Exception extends Exception
{
	/**
	 * Constructor
	 */
	public function __construct( $message = null, $priority = LOG_INFO)
	{
		parent::__construct( $message, $priority );

		JLD_System_Logger::log( $priority, $message );		
	}
	
}//end class
//</source>