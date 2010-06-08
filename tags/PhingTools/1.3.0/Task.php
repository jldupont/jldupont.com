<?php
/**
 * @author Jean-Lou Dupont
 * @package JLD
 * @sub-package PhingTools
 * @version 1.3.0
 * @Id $Id: Task.php 250 2007-11-15 18:13:35Z JeanLou.Dupont $
 */
//<source lang=php> 
require_once "phing/Task.php";

abstract class JLD_PhingTools_Task extends Task
{
	var $vars;
	/**
	 * Catch-all 'set' interface
	 */
	public function __set( $nm, $val )
	{
		return $this->vars[ $nm ] = $val;
	}
	/**
	 * Catch-all 'get' interface
	 */
	public function __get( $nm )
	{
		return @$this->vars[ $nm ];
	}
	/**
	 *
	 */
	public function getVars() { return $this->vars; }
	/**
	 * This interface does not seem to work with the current
	 * version of phing...
	 */
	/*
	public function __call( $method, $args )
	{
		$action = substr( $method, 0, 3);
		$var = substr( $method, 3 );
		
		if ($action === 'set')
			return $this->__set( $var, $args[0] );

		if ($action === 'get')
			return $this->__get( $var );
	
		throw new Exception( __CLASS__.': unknown method' );
	}
	*/
}
//</source>