<?php
/**
 * @author Jean-Lou Dupont
 * @package JLD
 * @subpackage Db
 * @version $Id: Db.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require 'JLD/Object/Object.php';

class JLD_Db extends JLD_Object
{
	const thisVersion = '$Id: Db.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $';
	
	var $server = null;
	var $name = null;
	var $user = null;
	var $password = null;
	
	var $conn = false;
	var $last_error = null;
	
	public function __construct() 
	{
		if ( !function_exists( 'mysql_connect' ) )
			die( __CLASS__.': requires mysql database module.' );
	}
	/**
	 * Returns the connection object.
	 */
	public function getConnection() { return $this->conn; }
	
	/**
	 * Sets the database connection parameters.
	 */
	public function setParameters( $server, $name, $user, $password )
	{
		$this->server = $server;
		$this->name = $name;
		$this->user = $user;
		$this->password = $password;
	}
	/**
	 * Opens a database
	 */
	public function open()
	{
		$this->conn = @mysql_connect(	$this->server, 
										$this->user, 
										$this->password, 
										true );
		
		if ($this->conn === false)
		{
			$this->last_error = mysql_error();
			return false;
		}
		$result = @mysql_select_db( $this->name, $this->conn );
		if ($result !== true )
		{
			$this->last_error = mysql_error();
			return false;			
		}
		
		return true;
	}
	/**
	 * Closes a database
	 */
	public function close()
	{
		if ( $this->conn !== false )
			@mysql_close( $this->conn );
	}
} // JLD_Db

//</source>