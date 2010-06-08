<?php
/**
 * ReadCachedSimpleDb class
 *
 * This class implements a 'read cached' interface to Amazon's SimpleDB WEB service.
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package ReadCachedSimpleDb
 */
/*
 * 
 *
 */
require_once 'Amazon/SimpleDB/Client.php';
require_once 'JLD/ReadCachedSimpleDb/Interface.php';
require_once 'MDB2.php';

class ReadCachedSimpleDb 
	extends Amazon_SimpleDB_Client 
	implements ReadCachedSimpleDb_Interface, Amazon_SimpleDB_Interface
{
	/**
	 * Amazon WEB service 'Access key'
	 */
	var $awsAccessKeyId = null;

	/**
	 * Amazon WEB service 'Secret key'
	 */
	var $awsSecretAccessKey = null;

	/**
	 * Amazon WEB service 'config' parameters
	 */
	var	$config = null;	

	/**
	 * MDB2 object
	 */
	var $mdb2 = null;
	/**
	 * Table name to use in MDB2 database
	 */
	var $tableName = null;
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// Initialization
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 *
	 */
	public function __construct()
	{
		
	}	

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ReadCachedSimpleDb interface definition
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * Sets the AMZ parameters
	 */
	public function setAWSparams( $awsAccessKeyId, $awsSecretAccessKey, $config )
	{
		$this->awsAccessKeyId = $awsAccessKeyId;
		$this->awsSecretAccessKey = $awsSecretAccessKey;
		$this->config = $config;
	}
	/**
	 *
	 */
	public function setCacheDBparams( MDB2_Driver_Common $mdb2, string $tableName )
	{
		$this->mdb2 = $mdb2;
		$this->tableName = $tableName;
	}
	/**
	 * Verifies that the required table structure exists
	 * in the local cache database.
	 * All the required fields must be present for the
	 * method to declare victory.
	 *
	 * @param $tableSchema : must be in MBD2 format
	 */
	public function checkCacheDbTable( &$tableSchema )
	{
		return $this->checkLocalDatabase( $tableSchema );
	}
	/**
	 * Creates the required table structure of the local cache database.
	 * Adds the missing fields when necessary i.e. does not break if some
	 * fields already exist.
	 *
	 * @param $tableSchema : must be in MBD2 format	 
	 */
	public function createCacheDb( &$tableSchema )
	{
		// verify we have a valid mdb2 object instance
		if ( !$this->isValidMDB2() )
		{
			require_once 'JLD/ReadCachedSimpleDb/Exception.php';	
			throw new ReadCachedSimpleDb_Exception; //##TODO
		}
		// we need the manager module to create table, fields etc.
		$mdb2->loadModule('Manager');
		
		// does the required table already exist?
		if (!$this->tableExists())
			$this->createTable();
		
		// does the field already exist? skip if YES
		// BUT make sure it is of the right type.
		
	}
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// AMAZON SimpleDb interface
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * We do not need to support this method here.
	 * @throws ReadCachedSimpleDb_Exception
	 */
    public function createDomain($action)
	{
		require_once 'JLD/ReadCachedSimpleDb/Exception.php';	
		throw new ReadCachedSimpleDb_Exception; //##TODO
	}
	/**
	 * @throws ReadCachedSimpleDb_Exception
	 */
    public function listDomains($action)
	{
		require_once 'JLD/ReadCachedSimpleDb/Exception.php';	
		throw new ReadCachedSimpleDb_Exception; //##TODO
	}
	/**
	 *
	 * @throws ReadCachedSimpleDb_Exception
	 */
    public function deleteDomain($action)
	{
		require_once 'JLD/ReadCachedSimpleDb/Exception.php';	
		throw new ReadCachedSimpleDb_Exception; //##TODO
	}
	/**
	 * Puts the attributes associated with an 'item'.
	 * Case1: all attributes exist
	 * Case2: at least one attribute does not exist
	 *
	 * Task1: update the local cache
	 */
    public function putAttributes($action)
	{
		
	}
	/**
	 *	Fetches attributes associated with an 'item' from the local cache.
	 *  Case1: cache hit  - entry not expired
	 *	Case2: cache hit  - entry expired
	 *	Case3: cache miss - remote item exists
	 *	Case4: cache miss - remote item does not exist
	 */
    public function getAttributes($action)
	{
		
	}
	/**
	 * 
	 */
    public function deleteAttributes($action)
	{
		
	}
	/**
	 * Task: translate the SimpleDb query string to an MDB2 one.
	 */
    public function query($action)
	{
		
	}


// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// Helper methods for AMZ SimpleDb
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%



// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// Helper methods 
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 *
	 */
	private function checkLocalDatabase( &$tableSchema )
	{
		// verify if the table exist
		if ( !$this->isValidMDB2() )
			return false;
			
		// does the field already exist? skip if YES
		// BUT make sure it is of the right type.
		
		
	}
	/**
	 * Performs some basic tests on the $mdb2 object instance
	 */	
	private function isValidMDB2( )	 
	{
		// verify we have a valid mdb2 object instance
		if ( $this->mdb2 === null )
			return false;
		if ( !$this->mdb2 instanceof MDB2_Driver_Common)
			return false;
			
		return true;		
	}
	/**
	 * Verifies that the required database table exists
	 *
	 * @return
	 */	
	private function tableExists()
	{
		// we need a valid table name!
		if ( empty( $this->tableName ) )
		{
			require_once 'JLD/ReadCachedSimpleDb/Exception.php';	
			throw new ReadCachedSimpleDb_Exception; //##TODO
		}
		
		// ##TODO check if this function fails if NO table exists
		$tables = $this->mdb2->listTables();
		// we can't run into an error here!
        if (PEAR::isError($tables))
		{
			require_once 'JLD/ReadCachedSimpleDb/Exception.php';	
			throw new ReadCachedSimpleDb_Exception; //##TODO
		}

		return in_array( $this->tableName, $tables );
	}
	/**
	 * Creates the required table in the local database.
	 */	
	private function createTable()
	{
		
	}	 

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// Expiry related helper methods 
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

}//end class