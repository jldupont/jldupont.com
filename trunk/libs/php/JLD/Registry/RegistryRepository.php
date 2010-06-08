<?php
/**
 * RegistryRepository abstract class
 *  This class is used as a base for the RegistryRepository classes.
 *  A Registry Repository class allows interfacing to an repository.
 *
 * A registry key exists in a namespace partitioned in the following way:
 * $key = /$realm/$class/$sub-key
 *
 * The 'expiry' value for a particular key exists in the 'persistency' realm e.g.
 * key1 = /system/$class1/$subkey1
 * expirty for key1: /persistency/$class1/$subkey1
 *
 * @author Jean-Lou Dupont
 * @package Registry
 * @version @@package-version@@
 * @Id $Id: RegistryRepository.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once 'JLD/Object/Object.php';

class JLD_RegistryRepository extends JLD_Object implements Iterator
{
	/**
	 * Default expiry timeout value in seconds.
	 */
	static $default_expiry = 86400; // 1day.
		 
	/**
	 * This class' version
	 */
	const thisVersion = '$Id: RegistryRepository.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $';

	/**
	 * Registry [key;value;type]
	 */
	var $data;

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// HELPER METHODS
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * Sets the default expiry timeout value (in seconds)
	 */
	public function setDefaultExpiry( $exp ) { return self::$default_expiry = $exp; }
	/**
	 * Gets the default expiry timeout value (in seconds)
	 */
	public function getDefaultExpiry( ) { return self::$default_expiry; }

	/**
	 * This method decomposes a 'key' into its basic components.
	 * 
	 * @param string Input key
	 * @param mixed Reference to variable for holding the 'realm' component
	 * @param mixed Reference to variable for holding the 'class' component
	 * @param mixed Reference to variable for holding the 'subkey' component	 
	 * @return bool TRUE if operation succeeded, else FALSE
	 */
	protected function decomposeKey( &$key, &$realm, &$class, &$subkey )
	{
		// preg_match would be nice... but slower.
		$bits = explode('/', $key );
		
		if (empty( $bits[0] ))
			return false;
		$realm = $bits[0];
		array_shift( $bits );
		
		if (empty( $bits[1] ))
			return false;
		$class = $bits[1];
		array_shift( $bits );
		
		if (empty( $bits ))
			return false;
		$subkey = implode( '/', $bits );
		
		return true;
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ITERATOR METHODS
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	public function next()   {return next($this->data); }
	public function rewind() {return rewind($this->data);}
	public function valid()  {return valid($this->data);}
	public function key()    {return key($this->data);}
	public function current(){return current($this->data);}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// METHODS which must be OVERLOADED	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	/**
	 * Gets a value corresponding to a key in the registry.
	 * @param string $key
	 * @param object $value reference to a object that will hold the value
	 * @return bool Returns TRUE/FALSE
	 */
	public function getKey( $key, &$value, &$type )
	{
		if (!isset( $this->data[$key] ))
			return false;
		
		$value = $this->data[$key]['v'];
		$type = $this->data[$key]['t'];		
		
		return true;
	}
	
	/**
	 * Gets the integer expiry timeout in seconds associated with a given key.
	 * @param string $key
	 * @return integer Expiry timeout value in seconds.
	 */
	public function getExpiry( $key )
	{
		$expD = self::$default_expiry;
		$realm = null; $class = null; $subkey = null; $type = null;

		$r = $this->decomposeKey( $key, $realm, $class, $subkey );
		if ($r === false)
			throw new JLD_System_Exception( JLD_RegistryRepository_WRONGKEYFORMAT );
		
		$expKey = "/persistency/$class/$subkey";
		$r = $this->getKey( $expKey, $exp, $type );
		if ($r === false)
			return $expD; // default
		
		return $exp;
	}
	
	/**
	 * Fetches a fresh & complete copy of the registry.
	 */
	public function refresh(){}
	
	/**
	 * Initialization of the configuration parameters
	 *
	 * @param mixed $parameters Configuration Array
	 */
	public function init( $parameters ){}
	
}//end class
//</source>