<?php
/**
 * Logger
 *
 * Configuration parameters from the cache:
 * REALM / CLASS / SUBKEY
 * =====   =====   ======
 * SYSTEM  LOG     MAX_FREQ_LOG_EMERG
 * SYSTEM  LOG     MAX_FREQ_LOG_ALERT
 * SYSTEM  LOG     MAX_FREQ_LOG_CRIT
 * SYSTEM  LOG     MAX_FREQ_LOG_ERR
 * SYSTEM  LOG     MAX_FREQ_LOG_WARNING
 * SYSTEM  LOG     MAX_FREQ_LOG_NOTICE
 * SYSTEM  LOG     MAX_FREQ_LOG_INFO
 * SYSTEM  LOG     MAX_FREQ_LOG_DEBUG      
 *
 * @author Jean-Lou Dupont
 * @package System
 * @subpackage Logger
 * @version @@package-version@@
 * @Id $Id: Logger.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php>

require_once 'JLD/Cache/Cache.php';

define_syslog_variables();

class JLD_System_Logger
{
	/**
	 * The identifier to use in the system log
	 */
	static $ident = 'JLD';
	
	/**
	 * The 'realm' to use in the cache.
	 */
	static $realm = 'SYSTEM';

	/**
	 * The 'class' to use in the cache.
	 */
	static $classe = 'LOG';
	
	/**
	 *
	 */
	static $map = array(	LOG_EMERG   => 'LOG_EMERG',
							LOG_ALERT   => 'LOG_ALERT',
							LOG_CRIT    => 'LOG_CRIT',
							LOG_ERR     => 'LOG_ERR',
							LOG_WARNING => 'LOG_WARNING',
							LOG_NOTICE  => 'LOG_NOTICE',
							LOG_INFO    => 'LOG_INFO',
							LOG_DEBUG   => 'LOG_DEBUG',
	);
	/**
	 * Defaults
	 */
	static $defs = array(	LOG_EMERG   => 'LOG_EMERG',
							LOG_ALERT   => 'LOG_ALERT',
							LOG_CRIT    => 'LOG_CRIT',
							LOG_ERR     => 'LOG_ERR',
							LOG_WARNING => 'LOG_WARNING',
							LOG_NOTICE  => 'LOG_NOTICE',
							LOG_INFO    => 'LOG_INFO',
							LOG_DEBUG   => 'LOG_DEBUG',
	);	
	/**
	 * Logs a message to the system logger
	 */
	public static function log( $priority = LOG_INFO, $message = null)
	{
		if ( self::$gate( $priority, $message ) )
			self::$doLogging( $priority, $message );
	}
	/**
	 * Determines if the message should be logged or not.
	 * This method acts as a 'gate' in order 
	 * to control possible 'avalanches'.
	 * 
	 */
	protected static function gate( $p, $m )	
	{
		$key = self::$getKey( $p, $m );
		
	}
	/**
	 * This function does the actual logging.
	 */			 
	protected static function doLogging( $p, $m )
	{
		openlog( 	self::$ident, 
					LOG_NDELAY | LOG_CONS | LOG_PID | LOG_PERROR, 
					LOG_LOCAL0 /*not available under windows*/ );		
		
		syslog( $priority, $message );
		
		closelog();
	}
	/**
	 * Calculates a 'key' which will be used to retrieve information
	 * from the cache.
	 */
	protected static function getKey( &$p, &$m )
	{
		return md5( $m.$p );	
	}
	/**
	 * Reads from the cache ...
	 */
	protected function getTimeout( $p )
	{
		
	}
	
}//end class

//</source>