<?php
/**
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.14.0
 * @Id $Id: DataTypeSorter.php 306 2007-12-17 16:06:30Z JeanLou.Dupont $
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