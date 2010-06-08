<?php
/**
 * ReadCachedSimpleDb Interface definition
 *
 * This file defines the interface.
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package ReadCachedSimpleDb
 */

interface ReadCachedSimpleDb_Interface
{
	/**
	 * setAWSparams
	 * Sets the AWS parameters.
	 *  
	 * @see 
	 * @param 
	 * @see 
	 *
	 * @throws 
	 */
	public function setAWSparams( $awsAccessKeyId, $awsSecretAccessKey, $config );

	/**
	 * setCacheDb
	 * setCacheDb sets the MDB2 instance to be used as cache. The MDB2 object instance 
	 * must have been created prior the calling this method.
	 * The $tableName table must hold a column named 'ItemName' which indexes
	 * the corresponding 'items' of the SimpleDB domain.
	 *   
	 * @see 
	 * @param MDB2_Driver_Common $mdb2
	 * @param string $tableName The name of the table to used. 
	 * @see 
	 *
	 * @throws 
	 */
	public function setCacheDBparams( MDB2_Driver_Common $mdb2, string $tableName );

	/**
	 * Verifies that the cache DB table consists of the required fields.
	 * 
	 *   
	 * @see 
	 * @param 
	 * @see 
	 *
	 * @throws ReadCachedSimpleDb_Exception
	 */
	public function checkCacheDbTable( &$tableSchema );	

	/**
	 * Creates the required fields for the cache DB table.
	 * 
	 *   
	 * @see 
	 * @param 
	 * @see 
	 *
	 * @throws ReadCachedSimpleDb_Exception
	 */
	public function createCacheDb( &$tableSchema );	
		
}
 