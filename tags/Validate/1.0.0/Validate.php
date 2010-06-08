<?php
/**
 * Parameter validation
 *
 * @author Jean-Lou Dupont
 * @version 1.0.0
 */
/**
  m: mandatory parameter
  s: sanitization required
  l: which parameters to pick from list
  d: default value
  t: type of parameter

EXAMPLE of a reference list:
============================
	static $parameters = array(
		'email_p1'	=> array( 'm' => true,  's' => true, 'l' => false, 'd' => null,   'sq' => true, 'dq' => true, 't' => 'string'  ),
		'email_p2'	=> array( 'm' => true,  's' => true, 'l' => false, 'd' => null,   'sq' => true, 'dq' => true, 't' => 'string'  ),
		'size'		=> array( 'm' => false, 's' => true, 'l' => false, 'd' => '40',   'sq' => true, 'dq' => true  ),
		'default'	=> array( 'm' => false, 's' => true, 'l' => false, 'd' => null,   'sq' => true, 'dq' => true  ),
		'width'		=> array( 'm' => false, 's' => true, 'l' => true,  'd' => null,   'sq' => true, 'dq' => true  ),
		'height'	=> array( 'm' => false, 's' => true, 'l' => true,  'd' => null,   'sq' => true, 'dq' => true  ),		
		'alt'		=> array( 'm' => false, 's' => true, 'l' => true,  'd' => null,   'sq' => true, 'dq' => true  ),		
		'title'		=> array( 'm' => false, 's' => true, 'l' => true,  'd' => null,   'sq' => true, 'dq' => true  ),		
	);
*/
class JLD_Validate
{
	/**
	 * Builds a parameter list (i.e. array) from an
	 * object instance. Uses the 'reference list' passed
	 * as input list.
	 *
	 * @param object $obj
	 * @param array reference list
	 * @return array output list
	 */
	public static function initFromObject( &$obj, &$ref_liste )
	{
		if (empty( $ref_liste ))
			return null;
			
		$output = array();
		
		foreach( $ref_liste as $key => &$value )
			if ( isset( $obj->$key ) )
				$output[ $key ] = $obj->$key;
				
		return $output;
	}	 
	/**
	 * Retrieves the specified list of parameters from the list.
	 * Uses the ''l'' parameter from the reference list.
	 *
	 * @param array input $liste
	 * @param array reference list
	 * @return array of parameters
	 */
	public static function buildList( &$liste, &$ref_liste )	
	{
		if (empty( $liste ))
			return null;
			
		$result = '';
		// only pick the key:value pairs that have been
		// explictly marked using the 'l' key in the
		// reference list.
		foreach( $liste as $key => &$value )
		{
			$key = trim( $key );
			$val = trim( $value );
			if ( isset( $ref_liste[ $key ] ) )
				if ( isset( $ref_liste[ $key ]['l'] ) )
					if ( $ref_liste[ $key ]['l'] === true )
						$result .= " $key='$val'";
		}
		return $result;		
	}
	/**
	 * Sanitize the parameters list & initialize to the default
	 * value the missing ones.
	 * Just keeps the parameters defined in the reference list.
	 *
	 * @param array input $liste
	 * @param array reference list
	 * @return array $new_liste when process is successful
	 * @return string $key which parameter is missing
	 */
	public static function doListSanitization( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return array();

		// first, let's make sure we only have valid parameters
		$new_liste = array();
		foreach( $liste as $key => &$value )
			if (isset( $ref_liste[ $key ] ))
				$new_liste[ $key ] = $value;
				
		// then make sure we have all mandatory parameters
		foreach( $ref_liste as $key => &$instructions )
			if ( isset( $instructions[ 'm' ] ) )
				if ( $instructions['m'] === true )
					if ( !isset( $liste[ $key ] ))
						return $key;
					
		// finally, initialize to default values the missing parameters
		foreach( $ref_liste as $key => &$instructions )
			if ( $instructions['d'] === true )
				if ( $instructions['d'] !== null )
					if ( !isset( $new_liste[ $key ] ))
						$new_liste[ $key ] = $instructions['d'];
				
		return $new_liste;
	}
	/**
	 * Performs various sanitization.
	 * Only valid parameters should end-up here.
	 *
	 * @param array input $liste
	 * @param array reference list
	 * @return string $key IF wrong type
	 */
	public static function doSanitization( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return null;
			
		foreach( $liste as $key => &$value )
		{
			// Remove leading & trailing double-quotes
			if (isset( $ref_liste[ $key ]['dq'] ))
					if ( $ref_liste[ $key ]['dq'] === true )
					{
						$value = ltrim( $value, "\" \t\n\r\0\x0B" );
						$value = rtrim( $value, "\" \t\n\r\0\x0B" );
					}

			// Remove leading & trailing single-quotes
			if (isset( $ref_liste[ $key ]['sq'] ))
					if ( $ref_liste[ $key ]['sq'] === true )
					{
						$value = ltrim( $value, "\' \t\n\r\0\x0B" );
						$value = rtrim( $value, "\' \t\n\r\0\x0B" );
					}

			// Type checking
			if (isset( $ref_liste[ $key ]['t'] ))
				if (!self::doTypeCheck( $value, $ref_liste[ $key]['t'] ) )
					return $key;

			// HTML sanitization
			if (isset( $ref_liste[ $key ]['s'] ))
				if ( $ref_liste[ $key ]['s'] === true )
					$value = htmlspecialchars( $value );
		}
		
		return true;		
	}
	/**
	 * Performs type checking
	 * Types supported: [array, string, int, bool, float, dir, file]
	 *
	 * @param mixed $value to check
	 * @param string $type expected type 
	 * @return bool
	 * @throws Exception
	 */	
	public static function doTypeCheck( &$value, $type )
	{
		// be optimist and avoid PHP warning!
		$result = true;
		
		switch( $type )
		{
			case 'string':
				$result = is_string( $value );
				break;
			case 'int':
				$result = is_numeric( $value );
				break;
			case 'bool':
				$result = is_bool( $value );
				break;
			case 'float':
				$result = is_float( $value );
				break;
			case 'array':
				$result = is_array( $value );
				break;
			case 'dir':
				$result = is_dir( $value );
				break;
			case 'file':
				$result = is_file( $value );
				break;
	
			default:
				throw new Exception( __CLASS__.'::'.__METHOD__.': wrong type.' );
		}
		
		return $result;		
	}	 
	/**
	 * Checks for if the $liste contains parameters 
	 * marked as ''r'' (i.e. restricted)
	 *
	 * @return bool null for empty list
	 * @return string restricted key name
	 * @return bool false if no restricted parameter found
	 */
	public static function checkListForRestrictions( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return null;

		foreach( $liste as $key => &$value )
		{
			// HTML sanitization
			if (isset( $ref_liste[ $key ]['r'] ))
				if ( $ref_liste[ $key ]['r'] === true )
					return $key;							
			
		}
		
		return false;		
	}
}// end class ExtHelpers
