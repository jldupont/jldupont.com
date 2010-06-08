<?php
/**
 * @author Jean-Lou Dupont
 * @package Object
 * @version @@package-version@@
 * @id $Id: Object.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once 'JLD/System/Exception.php';

abstract class JLD_Object
{
	const JLD_Object_Version = '$Id: Object.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $';
	static $instance = array();
	var $version = null;
	
	/**
	 * Class variables
	 * @var mixed
	 */
	var $vars = array();
	/**
	 * Constructor
	 * Optional 'version' parameter when used in conjunction with
	 * the singleton functionality.
	 */
	public function __construct( $version = null )
	{
		$this->version = $version;
	}
	/**
	 * Singleton interface
	 * 
	 * @param string $pClass
	 * @param string $version
	 * @return JLD_Object
	 */
	public static function singleton( $pClass = null, $version = null)
	{
		if ( self::$instance[$pClass] !== null)	
			return self::$instance[$pClass];
		
		if (empty($pClass))
			throw new JLD_System_Exception(__CLASS__.':'.__METHOD__." requires a valid class name." );
		
		self::$instance[$pClass] = new $pClass( $version );
		
		return self::$instance[$pClass];
	}
	/**
	 * Get class version
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}
	/**
	 * Generates a 'unique' key for this object class.
	 * Uses the top level namespace 'JLD'.
	 *
	 * @param string $var
	 */
	protected static function getCacheKey( $var )
	{
		return 'JLD:'.self::$pClass.":$var";
	}
	/**
	 * Catch-all parameter 'setter'
	 * @param string $key
	 * @param mixed $value
	 * @param mixed
	 */
	public function __set( $key, $value )
	{
		return $this->vars[$key] = $value;
	}
	/**
	 * Catch-all parameter 'getter'
	 * This function suppresses any error. 
	 *
	 * @param string $key
	 * @param mixed
	 */
	public function __get( $key )
	{
		return @$this->vars[$key];
	}
	/**
	 * Returns 'true' if the requested variable exists.
	 * @param string $key
	 * @return bool
	 */
	public function varExists( $key )
	{
		return ( isset( $this->vars[ $key ] ));
	}
	/**
	 * Validates the configuration parameters sent to the object.
	 * - All 'required' parameters must be present
	 * - Extra parameters are *not* checked
	 * 
	 * Structure of the arrays:
	 *  array( 'r' => true, 'key' => parameter_key_name )
	 *
	 * @param array $referenceList The list of expected configuration parameters
	 * @param array $parameters The list sent to configure the object instance
	 * @return bool TRUE if the validation was successful OR 
	 *                   'key' of first missing required parameter
	 *                 
	 */
	protected function validateConfiguration( &$referenceList, &$parameters )
	{
		$result = true;
		
		if (empty( $referenceList ))
			throw new JLD_System_Exception();

		if (empty( $parameters ))
			throw new JLD_System_Exception();
		
		foreach ( $referenceList as &$e )
		{
			// is the entry 'required' ?
			if ( $e['r'] === true )
				if ( !isset( $parameters[ $e['key'] ] ))
				{
					// give a hint to the caller of what's missing...
					$result = $e['key'];
					break;
				}
		}
		
		return $result;
	}

	/**
	 * This method validates & sets the configuration parameters.
	 * If a parameter is already set in this object, bail out with error.
	 *
	 * Structure of the $params array
	 *  array( key => value, ... )
	 *
	 * @param array $referenceList The list of expected configuration parameters
	 * @param array $parameters The list sent to configure the object instance
	 * @return bool TRUE if the validation was successful OR 
	 *                   'key' of first missing required parameter
	 */
	protected function digestConfiguration( &$refList, &$params )
	{
		// bail out if we can't pass the validation phase.
		if ( ($hint = $this->validateConfiguration( $refList, $params )) !== true)
			return $hint;
		
		// now, go through all the configuration parameters
		// and sets them locally in this object instance
		foreach ( $params as $key => &$value )
		{
			// let the client layer handle this sort of error.
			if ( $this->varExists( $key ) )
				return false;
				
			$this->__set( $key, $value );
		}
		
		return true;
	}

}//end class
//</source>