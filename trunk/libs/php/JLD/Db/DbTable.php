<?php
/**
 * @author Jean-Lou Dupont
 * @package JLD
 * @subpackage DbTable
 * @version $Id: DbTable.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

abstract class JLD_Db_Table extends JLD_Object
{
	var $db = null;
	var $table_name = null;
	
	var $fields = array();
	
	/**
	 *
	 */
	public function __construct( $name ) 
	{
		$this->table_name = $name;
		
	}
	/**
	 * Returns the fields of the table.
	 */
	public function & getFields()
	{
		$result = @mysql_query("SHOW COLUMNS FROM ".$this->table_name );
		if ($result === false) 
			return false;
			
		$this->fields = array();
		
		if (@mysql_num_rows($result) > 0) 
		{
		    while ($row = @mysql_fetch_assoc($result)) 
				$this->fields[] = $row;	
		}
		@mysql_free_result($result);		
		return $this->fields;	
	}
	/**
	 *
	 */
	public function getRowById( $id ) 
	{
		$result = @mysql_query("SELECT FROM ".$this->table_name." WHERE id=$id" );
		
		// we are supposed to only get 1 hit.
		$row = @mysql_fetch_assoc($result);
		@mysql_free_result($result);
		
		return $row;
	}

	/**
	 *
	 */
	public function getRowByKey( $key ) {}

	/**
	 * Mimics AmazonS3's get by prefix function.
	 */
	public function getRowsByPrefix( $prefix ) 
	{
		$eprefix = mysql_real_escape_string($prefix).'%';
		
		$result = @mysql_query("SELECT FROM ".$this->table_name." WHERE key=".$eprefix );
		if ($result === false)
			return false;

		$this->rows = array();
		
		if (@mysql_num_rows($result) > 0) 
		{
		    while ($row = @mysql_fetch_assoc($result)) 
				$this->rows[] = $row;	
		}
		@mysql_free_result($result);		
		return $this->rows;	
		
	}
	/**
	 *
	 */
	public function insertRow( &$rowData ) {}
	
	/**
	 *
	 */
	public function deleteRowById( $id ) 
	{
		$result = @mysql_query("DELETE FROM ".$this->table_name." WHERE id=$id" );
		return $result;		
	}

	/**
	 *
	 */
	public function deleteRowByKey( $key ) {}
	
	/**
	 *
	 */
	public function deleteExpired()
	{
		$result = @mysql_query("DELETE FROM ".$this->table_name." WHERE expired=1" );
		return $result;		
	}
	
} // JLD_Db_Table

//</source>