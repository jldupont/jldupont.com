<?php
/**
 * ObjectCache Interface definition
 *
 * This file defines the interface.
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package ObjectCache
 */

/**
 * The production interface.
 *
 */
interface ObjectCache_Interface
{
	/**
	 * Sets the 'expiry' timeout for this ObjectCache instance.
	 *
	 * @param int $expiry in seconds
	 * @throws ObjectCache_Exception
	 */
	public function setExpiry( $expiry );
	
	/**
	 * Sets a given 'key' to the specified 'value'
	 *
	 * @param string $key
	 * @param string $value
	 * @throws ObjectCache_Exception
	 */
	public function set( $key, $value );

	/**
	 * Gets the specified 'value' associated with 'key'
	 *
	 * @param string $key
	 * @return null if specified does not exist/is expired
	 * @return string $value if specified 'key' exists
	 * @throws ObjectCache_Exception	 
	 */
	public function get( $key );	 
	
	/**
	 * Deletes the specified $key. If the 'key' does not exist,
	 * returns without error.
	 *
	 * @param string $key
	 * @throws ObjectCache_Exception	 
	 */
	public function delete( $key );	 

	/**
	 * Clears all entries from the cache.
	 * @throws ObjectCache_Exception	 	 
	 */
	public function clear( );
	
}//end interface


/**
 * The management interface
 *
 */
interface ObjectCache_ManagementInterface
{
	/**
	 * Sets the database parameters.
	 *
	 * @param string $dsn MDB2 DSN
	 * @param string $databaseName the name of the database
	 * @param string $tableName the name of the table in the specified database
	 * @throws ObjectCache_Exception
	 */
	public function init( $dsn, $databaseName, $tableName );
	
	/**
	 * Inits the required database structure.
	 * Must set the database objects through 'setDatabaseParameters'.
	 * If the objects already exist, return gracefully.
	 *
	 * @see ObjectCache_ManagementInterface::setDatabaseParameters
	 * @throws ObjectCache_Exception
	 */
	public function initDatabaseStructure( );

	/**
	 * Deletes the specified database structure.
	 * Must set the database objects through 'setDatabaseParameters'.
	 * If the objects do not exist, return gracefully.
	 *
	 * @see ObjectCache_ManagementInterface::setDatabaseParameters
	 * @throws ObjectCache_Exception	 
	 */
	public function deleteDatabaseStructure( );	 
	
	/**
	 * Verifies that the required structure is in place.
	 * Must set the database objects through 'setDatabaseParameters'.
	 *
	 * @see ObjectCache_ManagementInterface::setDatabaseParameters
	 * @throws ObjectCache_Exception	 
	 */	
	public function checkDatabaseStructure( );
		
}//end interface
 