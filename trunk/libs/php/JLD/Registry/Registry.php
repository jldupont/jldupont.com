<?php
/**
 * Registry class: used to manage static parameters.
 * - The consumers of the class have 'read only' access to the
 *   [key ; value] transaction_cache fetched from the central registry.
 * - The consumers of the class have 'read/write' access to the
 *   [key ; value] transaction_cache managed locally.
 *
 * This class depends on the availability of a shared memory
 * manager such as 'APC' or 'eAccelerator'.
 *
 * KEY namespace organisation:
 * ===========================
 * /$key = /$realm/$class/$subkey
 *
 * RESERVED REALMS:
 * ================
 * - SYSTEM
 * - LOCAL
 * - CACHE
 * - CONFIG
 * - DESCRIPTION
 *
 * EXPIRY policy for /$key:
 * =======================
 * 1) Entry exists for $key/expiry ?
 * 2) Entry exists for /$realm/$class ?
 * 3) Entry exists for /CACHE/DEFAULT/expiry ?
 * 4) DEFAULT: use class level definition
 *
 * @author Jean-Lou Dupont
 * @package Registry
 * @version @@package-version@@
 * @Id $Id: Registry.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require 'JLD/Object/Object.php';
require 'JLD/Cache/Cache.php';

// only one instance.
JLD_Registry::singleton();

class JLD_Registry extends JLD_Object
{
	const thisVersion = '$Id: Registry.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $';
	
	/**
	 * Default expiry timeout for transaction_cache
	 * @var integer
	 */
	static $expiry = 86400; // 1day.

	// session based cache related
	var $transaction_cache = null;
	var $system_cache = null;

	public function __construct( ) 
	{
		if (self::$instance !== null)
			throw new JLD_System_Exception( __CLASS__.': only one instance of this class can be created.' );
			
		if (JLD_Cache_Manager::isFake())
			throw new JLD_System_Exception( __CLASS__.':'.__METHOD__.' requires a real cache for performance consideration' );
			
		$this->system_cache = JLD_Cache_Manager::getCache();
		
		// we must make sure we are initialized!
		$this->init();
		
		return parent::__construct( self::thisVersion );
	}
	/**
	 * Singleton interface
	 */
	public static function singleton()
	{
		return parent::singleton( __CLASS__ );	
	}
	/**
	 * Returns the default expiry time
	 * @return integer
	 */
	public function getDefaultExpiry()
	{
		return self::$expiry;
	}
	/**
	 * Sets the default expiry time
	 *
	 * @param integer $expiry
	 * @return integer
	 */
	public function setDefaultExpiry( $expiry )
	{
		return (self::$expiry = $expiry);
	}
	/**
	 * Initialization procedure
	 * This method is being kept 'lean' for normal transactions
	 * i.e. usual transactions which do not require querying the
	 * central registry repository.
	 * 
	 */
	protected function init()
	{
		
	}
	/**
	 *  Returns the registry value in $value
	 *  
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	public function get( $key, &$value )
	{
		// check if we can get lucky with the transaction scoped cache.
		if (isset( $this->transaction_cache[ $key ] ))
		{
			$value = $this->transaction_cache[ $key ];
			return true;
		}
			
		// next, try with the system scoped cache
		$value = $this->system_cache( $key );
		
		// store it in the transaction cache
		$this->transaction_cache[$key] = $value;
		
		return ($value !== false);
	}

	/**
	 *  This method will set the LOCAL session & local caches
	 *  with the required [$key => $value] pair.
	 *
	 */
	public function set( $key, $value )
	{
		// get it in our transaction cache.
		$this->transaction_cache[ $key ] = $value;
		
		// this shouldn't fail... some init check has already been done.
		$expiry = $this->getExpiry( $key );
			
		// but store it also in the system cache.
		$this->system_cache->set( $key, $value, $expiry );

		return true;		
	}
	/**
	 * Returns true if the requested key is 'local'
	 * i.e. the key only exists locally on this system.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function isLocal( $key )
	{
		return ( $this->system_cache->get( $key ) === false );	
	}
	/**
	 * Get expiry timeout for a given key
	 *
	 * @param $key
	 * @return integer The expiry timeout integer value in seconds.
	 */
	public function getExpiry( $key )
	{
		
	}
	
} // end class

//</source>