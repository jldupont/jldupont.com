<?php
/**
 * RegistryRepository based on GoogleDocs
 *
 * Utilisation:
 * <code>
 * require 'JLD/GoogleDocs/RegistryRepository.php';
 *  $r = new JLD_GoogleDocs_RegistryRepository;
 *  $r->init( array(	'gs_user' 		=> 'user name',
 *						'gs_password'	=> 'password',
 *						'gs_document'	=> 'id of spreadsheet document',
 *						'gs_worksheet'	=> "id of spreadsheet's worksheet",
 *                    
 *                 )
 *  );
 *
 *  $value = null;
 *  $type = null;
 *  $result = $r->getKey( $key, $value, $type );
 * 
 * </code>
 *
 * @author Jean-Lou Dupont
 * @package GoogleDocs
 * @subpackage RegistryRepository
 * @version $Id: RegistryRepository.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once 'JLD/Object/Object.php'; //includes also the 'JLD_System_Exception' class
require_once 'JLD/Cache/Cache.php';
require_once 'JLD/Registry/RegistryRepository.php';
require_once 'JLD/System/Exception.php';

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Gdata/ClientLogin.php';
require_once 'Zend/Exception.php';

class JLD_GoogleDocs_RegistryRepository extends JLD_RegistryRepository
{
	const thisVersion = '$Id: RegistryRepository.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $';
	
	/**
	 * Mandatory configuration parameters list
	 */
	static $configParametersList = array(
		// Username
		array( 'r' => true, 'key' => 'gs_user' ),
		// Password
		array( 'r' => true, 'key' => 'gs_password' ),
		// Document		
		array( 'r' => true, 'key' => 'gs_document' ),		
		// Worksheet
		array( 'r' => true, 'key' => 'gs_worksheet' ),
	);
	/**
	 */
	var $gs_service = null;
	var $gs_client = null;
	var $gs_spreadsheetService = null;
	var $list_feed_allrows;
	
	public function __construct( ) 
	{
		
	}
	/**
	 * Initialization of the configuration parameters
	 *
	 * @param mixed $parameters Configuration Array
	 */
	public function init( $parameters )
	{
		if ( ($hint = $this->digestConfiguration( self::$configParametersList, $parameters )) !== true)
			throw new JLD_System_Exception( 'Missing parameter: '.$hint );

		// let the client deal with any potential error
		$this->initGdoc( );
		$this->refresh( );
	}
	/**
	 * Init the Gdoc service
	 */
	protected function initGdoc()
	{
		try 
		{
			$this->gs_service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
			$this->gs_client = Zend_Gdata_ClientLogin::getHttpClient( $this->gs_user, $this->gs_password, $this->gs_service);
			$this->gs_spreadsheetService = new Zend_Gdata_Spreadsheets($this->gs_client);
			var_dump( $this->gs_spreadsheetService );
		}
		catch( Zend_Exception $e )
		{
			throw new JLD_System_Exception(); 
		}
		
		return true;
	}
	/**
	 * Reads in a complete worksheet for the specified document.
	 */
	protected function readCompleteWorksheet()
	{
		$query = new Zend_Gdata_Spreadsheets_ListQuery();
		$query->setSpreadsheetKey( $this->gs_document );
		$query->setWorksheetId( $this->gs_worksheet );
		try 
		{
			// this reads all the rows of the spreadsheet
			// up until the first empty row.
			$this->list_feed_allrows = $spreadsheetService->getListFeed( $query );
		}
		catch(Zend_Exception $e)
		{ 
			throw new JLD_System_Exception(); 
		}
		
		return true;
	}
	/**
	 * Iterates through the list_feed object
	 * and extracts all [key;value] pairs + type.
	 */
	protected function extractVariables()
	{
		if (empty( $this->list_feed_allrows ))
			return false;
		
		// clear the array just to start fresh.
		unset( $this->data );
		
		try 
		{
			// This iterates through all the rows.	
			foreach( $this->list_feed_allrows->entries as &$entry )
			{
				$cellRaw = $entry->getCustom();
	
				// the first column should be the 'key'
				$name0 = strtolower( $cellRaw[0]->getColumnName() );
				$key = $cellRaw[0]->getText();

				// the second column should be the 'value'
				$name1 = strtolower( $cellRaw[1]->getColumnName() );
				$value = $cellRaw[1]->getText();
				
				// the third column should be the 'type'
				$name2 = strtolower( $cellRaw[2]->getColumnName() );
				$type = $cellRaw[2]->getText();
				
				if ( ($name0!=='key') || ($name1!=='value') || ($name2!=='type') )
					throw new JLD_System_Exception( JLD_GoogleDocs_RegistryRepository_WRONGFORMAT );
					
				//@todo check key redefinition?
				$this->data[ $key ] = array( 'v' => $value, 't' => $type );
			}
		} 
		catch( Zend_Exception $e )
		{
			throw new JLD_System_Exception( JLD_GoogleDocs_RegistryRepository_ROWDATA );
		}
		
		return true;
	}
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// INHERITED METHODS
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * Fetches a fresh & complete copy of the registry.
	 */
	public function refresh()
	{
		$this->readCompleteWorksheet();
		$this->extractVariables();
		
	}

}//end class
//</source>