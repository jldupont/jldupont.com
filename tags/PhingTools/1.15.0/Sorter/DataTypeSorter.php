<?php
/**
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.15.0
 * @Id $Id: DataTypeSorter.php 907 2009-05-19 14:59:10Z JeanLou.Dupont $
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