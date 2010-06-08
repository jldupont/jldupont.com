<?php
/**
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version @@package-version@@
 * @Id $Id: DataTypeSorter.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
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