<?php
/**
 * Shared memory cache.
 *
 * Note that the method 'clearCache' does _not_
 * perform unconditional clearing of the cache.
 *
 * @author Jean-Lou Dupont
 * @package Cache
 * @version @@package-version@@
 * @Id $Id: Cache.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

// create only one instance of the following class.
JLD_Cache_Manager::singleton();

class JLD_Cache_Manager
{
	/**
	 * Array sorted in order of preference
	 */
	static $classes = array(
		'JLD_Cache_eAccel',
		'JLD_Cache_APC',
		
		// fake cache must be last in list!
		'JLD_Cache_Fake'
	);

	static $instance = null;
	static $cache = null;

	/**
	 * Can't create an instance of this class directly;
	 * need to go through the singleton interface.
	 *
	 */
	protected function __construct(){}

	public static function singleton()
	{
		// singleton pattern
		if ( self::$instance === null)
		{
			self::$instance = new JLD_Cache_Manager;
			self::init();	
		}
		
		return self::$instance;
	}
	public static function isFake()
	{
		return (self::$cache instanceof JLD_Cache_Fake);	
	}
	protected static function init()
	{
		foreach ( self::$classes as $classe )
			if (self::checkPresence( $classe ))
			{
				// we just need one cache
				self::$cache = new $classe;
				break;
			}
	}
	public static function getCache()
	{
		return self::$cache;
	}

	public static function getAvailableCaches()
	{
		$caches = array();
		
		foreach ( self::$classes as $classe )
			if (self::checkPresence( $classe ))
				array_push( $caches, $classe );

		return $caches;
	}
	
	public static function checkPresence( &$classe )
	{
		$c = 'return '.$classe.'::checkPresence();';	
		return eval( $c );
	}
	
}// end JLD_Cache_Manager

/**
 * Simple generic object store
 */
abstract class JLD_Cache
{
	function __construct() 
	{}

	/**
	 * Returns information relative to the current cache.
	 */
	public function getInfo()
	{ 
		/* stub */	
		return null;
	}

	/**
	 * This method must check the availability
	 * of the cache functionality in question.
	 */
	public static function checkPresence()
	{
		/* stub */
		return false;	
	}

	public function clearCache()
	{
		/* stub */
		return false;	
	}
	/* *** THE GUTS OF THE OPERATION *** */
	/* Override these with functional things in subclasses */

	function get($key) 
	{
		/* stub */
		return false;
	}

	function set($key, $value, $exptime=0) 
	{
		/* stub */
		return false;
	}

	function delete($key, $time=0) 
	{
		/* stub */
		return false;
	}

	function lock($key, $timeout = 0) 
	{
		/* stub */
		return true;
	}

	function unlock($key) 
	{
		/* stub */
		return true;
	}

	/* *** Emulated functions *** */
	/* Better performance can likely be got with custom written versions */
	function get_multi($keys) 
	{
		$out = array();
		foreach($keys as $key)
			$out[$key] = $this->get($key);
		return $out;
	}

	function set_multi($hash, $exptime=0) 
	{
		foreach($hash as $key => $value)
			$this->set($key, $value, $exptime);
	}

	function add($key, $value, $exptime=0) 
	{
		if( $this->get($key) === false ) 
		{
			$this->set($key, $value, $exptime);
			return true;
		}
		return false;
	}

	function add_multi($hash, $exptime=0) 
	{
		foreach($hash as $key => $value)
			$this->add($key, $value, $exptime);
	}

	function delete_multi($keys, $time=0) 
	{
		foreach($keys as $key)
			$this->delete($key, $time);
	}

	function replace($key, $value, $exptime=0) 
	{
		if( $this->get($key) !== false )
			$this->set($key, $value, $exptime);
	}
	/**
	 * Atomic Increment.
	 * @return false if lock can not be acquired.
	 */
	function incr($key, $value=1) 
	{
		if ( !$this->lock($key) )
			return false;

		$value = intval($value);
		if($value < 0) $value = 0;

		$n = false;
		if( ($n = $this->get($key)) !== false ) 
		{
			$n += $value;
			$this->set($key, $n); // exptime?
		}
		$this->unlock($key);
		return $n;
	}

	function decr($key, $value=1) 
	{
		if ( !$this->lock($key) ) 
			return false;

		$value = intval($value);
		if($value < 0) $value = 0;

		$m = false;
		if( ($n = $this->get($key)) !== false ) 
		{
			$m = $n - $value;
			if($m < 0) $m = 0;
			$this->set($key, $m); // exptime?
		}
		$this->unlock($key);
		return $m;
	}

	/**
	 * Convert an optionally relative time to an absolute time
	 */
	static function convertExpiry( $exptime ) 
	{
		if(($exptime != 0) && ($exptime < 3600*24*30)) 
		{
			return time() + $exptime;
		} 
		else 
		{
			return $exptime;
		}
	}
}

/**
 * This is a wrapper for APC's shared memory functions
 *
 */
class JLD_Cache_APC extends JLD_Cache 
{
	public function getInfo()
	{
		return apc_cache_info();
	}
	
	public static function checkPresence()
	{
		return function_exists( 'apc_fetch' );
	}

	public function clearCache()
	{
		return apc_clear_cache();
	}

	function get($key) 
	{
		$val = apc_fetch($key);
		if ( is_string( $val ) ) 
			$val = unserialize( $val );
		else
			$val = false;

		return $val;
	}
	
	function set($key, $value, $exptime=0) 
	{
		return apc_store($key, serialize($value), $exptime);
	}
	
	function delete($key, $time=0) 
	{
		return apc_delete($key);
	}
}// END APC

/**
 * This is a wrapper for eAccelerator's shared memory functions.
 *
 */
class JLD_Cache_eAccel extends JLD_Cache 
{
	public function getInfo()
	{
		return eaccelerator_info();
	}

	public static function checkPresence()
	{
		return function_exists( 'eaccelerator_get' );		
	}
	/**
	 * Only clears the elements marked for deletion
	 * i.e. does not perform an unconditional 'clear' of
	 * all entries.
	 */
	public function clearCache()
	{
		return eaccelerator_clear();
	}

	function get($key) 
	{
		$val = eaccelerator_get( $key );

		if ( $val === null )
			return false;
			
		if ( is_string( $val ) ) 
			$val = unserialize( $val );

		return $val;
	}

	function set($key, $value, $exptime=0) 
	{
		return eaccelerator_put( $key, serialize( $value ), $exptime );
	}

	function delete($key, $time=0) 
	{
		return eaccelerator_rm( $key );
	}

	function lock($key, $waitTimeout = 0 ) 
	{
		return eaccelerator_lock( $key );
	}

	function unlock($key) 
	{
		return eaccelerator_unlock( $key );
	}
}

class JLD_Cache_Fake
{
	public function getInfo() { return array(); }
	public static function checkPresence() { return true; }
	public function clearCache() { return true; }

	function add ($key, $val, $exp = 0) { return true; }
	function decr ($key, $amt=1) { return null; }
	function delete ($key, $time = 0) { return false; }
	function get ($key) { return null; }
	function get_multi ($keys) { return array_pad(array(), count($keys), null); }
	function incr ($key, $amt=1) { return null; }
	function replace ($key, $value, $exp=0) { return false; }
	function set ($key, $value, $exp=0){ return true; }
}
//</source>