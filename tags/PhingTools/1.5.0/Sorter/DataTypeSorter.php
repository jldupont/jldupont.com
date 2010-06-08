<?php
/**
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.5.0
 * @Id $Id: DataTypeSorter.php 305 2007-12-17 04:02:22Z JeanLou.Dupont $
 */
//<source lang=php> 
abstract class DataTypeSorter
{
	var $ref = null;
	var $key = null;
	var $dir = null;
	
	abstract public function checkKey( $key );
	
	abstract public function sort( &$obj, $key, $dir );
}
//</source>