<?php
/**
 * ObjectCache class definition
 * 
 *
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package ObjectCache
 */
require 'PEAR.php';
require 'MDB2.php';
require 'JLD/ObjectCache/Interface.php';
require 'JLD/ObjectCache/Exception.php';
require 'JLD/ObjectCache/Defines.php';

class ObjectCache 
	implements	ObjectCache_Interface,
				ObjectCache_ManagementInterface
{
	/**
	 * The messages of this class
	 */
	static $msg;
	
	/**
	 * The 'expiry' timeout (in seconds)
	 */
	var $expiry = null;
	/**
	 * The DSN for the required database
	 */	
	var $dsn = null;
	/**
	 * The database name
	 */		 
	var $db = null;	 
	/**
	 * The table name in the database
	 */	
	var $table = null;	 
	/**
	 * The actual MDB2 object instance
	 */
	var $mdb2 = null;
		 
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// CONSTRUCTOR
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	public function __construct()
	{}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// PRODUCTION INTERFACE	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * @see ObjectCache_Interface::setExpiry
	 */
	public function setExpiry( $expiry )
	{
		
	}
	
	/**
	 * @see ObjectCache_Interface::set
	 */
	public function set( $key, $value )
	{
		
	}

	/**
	 * @see ObjectCache_Interface::get
	 */
	public function get( $key )
	{
		
	}
	
	/**
	 * @see ObjectCache_Interface::delete
	 */
	public function delete( $key )
	{
		
	}

	/**
	 * @see ObjectCache_Interface::clear
	 */
	public function clear( )
	{
		
	}
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// PRODUCTION INTERFACE	HELPERS
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	protected function init()
	{
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// MANAGEMENT INTERFACE	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * 
	 */
	public function init( $dsn, $databaseName, $tableName )
	{
		$this->dsn 	= $dsn;
		$this->db 	= $databaseName;
		$this->table= $tableName;
		
		return $this->initReal();
	}
	/**
	 * @see ObjectCache_ManagementInterface::initDatabaseStructure
	 */
	public function initDatabaseStructure( )
	{
		$this->initManagement();
			
	}
	/**
	 * @see ObjectCache_ManagementInterface::deleteDatabaseStructure
	 */
	public function deleteDatabaseStructure( )
	{
		$this->initManagement();		
	}
	/**
	 * @see ObjectCache_ManagementInterface::checkDatabaseStructure
	 */
	public function checkDatabaseStructure( )
	{
		$this->initManagement();		
	}
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// MANAGEMENT INTERFACE	HELPERS
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	protected function initReal()
	{
		// already initialized?
		if ( $this->mdb2 !== null )
		{ 
			$err = & ObjectCache::raiseError( MDB2_ERROR_NOT_FOUND,
            			null, null, 'no RDBMS driver specified');
			return $err;
		}
				
		$this->mdb2 =& MDB2::factory( $this->dsn );			
		if (PEAR::isError( $this->mdb2 )) 
		{
		    //die($mdb2->getMessage());
		}					
		// set the database to work on with
		$this->mdb2->setDatabase( $this->db );
		
	}
	protected function initManagement()
	{
		
		$this->mdb2->loadModule( 'Manager' );
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ERROR HANDLING
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    /**
     * This method is used to communicate an error and invoke error
     * callbacks etc.  Basically a wrapper for PEAR::raiseError
     * without the message string.
     *
     * @param   mixed  int error code
     *
     * @param   int    error mode, see PEAR_Error docs
     *
     * @param   mixed  If error mode is PEAR_ERROR_TRIGGER, this is the
     *                 error level (E_USER_NOTICE etc).  If error mode is
     *                 PEAR_ERROR_CALLBACK, this is the callback function,
     *                 either as a function name, or as an array of an
     *                 object and method name.  For other error modes this
     *                 parameter is ignored.
     *
     * @param   string Extra debug information.  Defaults to the last
     *                 query and native error code.
     *
     * @return PEAR_Error instance of a PEAR Error object
     *
     * @access  private
     * @see     PEAR_Error
     */
    private function &raiseError($code = null, $mode = null, $options = null, $userinfo = null)
    {
        $err =& PEAR::raiseError(null, $code, $mode, $options, $userinfo, 'ObjectCache_Error', true);
        return $err;
    }
    /**
     * Tell whether a value is a ObjectCache error.
     *
     * @param   mixed   the value to test
     * @param   int     if is an error object, return true
     *                        only if $code is a string and
     *                        $db->getMessage() == $code or
     *                        $code is an integer and $db->getCode() == $code
     *
     * @return  bool    true if parameter is an error
     *
     * @access  public
     */
    function isError($data, $code = null)
    {
        if (is_a($data, 'ObjectCache_Error')) {
            if (is_null($code)) {
                return true;
            } elseif (is_string($code)) {
                return $data->getMessage() === $code;
            } else {
                $code = (array)$code;
                return in_array($data->getCode(), $code);
            }
        }
        return false;
    }

}//end class
require 'JLD/ObjectCache/Messages.php';
//end file