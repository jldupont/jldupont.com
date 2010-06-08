<?php
/**
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.14.1
 * @Id $Id: DataTypeSorter.php 905 2009-05-19 14:32:48Z JeanLou.Dupont $
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