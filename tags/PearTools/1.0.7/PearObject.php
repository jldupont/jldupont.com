<?php
/**
 * @package PearTools
 * @author Jean-Lou Dupont
 * @version $Id: PearObject.php 269 2007-11-22 01:52:40Z JeanLou.Dupont $
 */
//<source lang=php> 
require_once 'JLD/Object/Object.php';

abstract class JLD_PearObject extends JLD_Object
{
	var $vars = array();

	// Magic Words: used for filling the templates
	static $magic_words = array(
		'$channel_name$'	=> 'channel_name',	 // for categories.xml
		'$all_categories$'	=> 'all_categories', // for categories.xml
		'$category_name$'	=> 'category_name',  // for categories.xml
		'$category1_name$'	=> 'category1_name', // for categories.xml		
		'$base_rest$'		=> 'base_rest',      // for categories.xml
		'$base_categories$'	=> 'base_categories',// for categories.xml		
		'$base_packages$'	=> 'base_packages',  // 
		'$base_releases$'	=> 'base_releases',  // for packagesinfo.xml
		'$package_name$'	=> 'package_name',	 // for packagesinfo.xml
		'$package_name_L$'	=> 'package_name',	 // for info.xml		
		'$all_releases$'	=> 'all_releases',	 // for packagesinfo.xml	
		
		'$package_version$'   => 'package_version',// for $version.xml
		'$package_stability$' => 'package_stability',// for $version.xml			
		'$channel_uri$'		=> 'channel_uri',	// for $version.xml
		'$base_tags$'		=> 'base_tags',		// for $version.xml
		
		'$all_packages$'	=> 'all_packages',	// for packages.xml
	);

	static $std_magic_words = array(
		'$tab$'		=> "\t",
		'$newline$' => "\n",
	);
	/**
	 * Adds a specific REST directory in the filesystem
	 */
	public function addRestDirectory( $which, $name )
	{
		$dir = $this->buildFileSystemRestPath($which).'/'.$name;

		if (is_dir( $dir ))
			return true;
			
		$result = @mkdir( $dir );	
			
		return $result;
	}
	/**
	 *
	 */
	public function buildFileSystemRestPath( $which )
	{
		return $this->channel_root.$this->base_rest.$which;
	}
	public function getVar( $var ) { return @$this->vars[$var]; }
	public function setVar( $var, $value ) { return $this->vars[$var] = $value; }
	
	public function __set( $nm, $val )
	{
		return $this->vars[ $nm ] = $val;
	}
	public function __get( $nm )
	{
		return @$this->vars[ $nm ];
	}
	/**
	 * Batch initialization
	 */
	public function initVars( &$vars )
	{
		if (empty( $vars ))
			throw new Exception( 'initVars called with invalid parameter' );
		foreach( $vars as $name => &$value )
			$this->__set( $name, $value );
	}
	protected function getTemplate( $tpl )
	{
		$r = @file_get_contents(dirname(__FILE__).$tpl );
		// a template file can't be empty !!!
		if (empty( $r ))
			throw new Exception( 'error reading template file: '.$tpl );
		return $r;
	}
	protected function writeFile( $file, &$c )
	{
		$len = strlen( $c );
		$bytes_written = @file_put_contents( $file, $c );
		return ( $len === $bytes_written );
	}
	protected function replaceMagicWords( &$tpl )
	{
		foreach ( self::$magic_words as $mg => $varname )
		{
			$var = $this->getVar( $varname );
			$lowercase = strpos($mg, '_L');
			if ( $lowercase !== false )
				$var = strtolower( $var );

			$tpl = str_replace( $mg, $var, $tpl );
		}
		foreach ( self::$std_magic_words as $mg => $value )
			$tpl = str_replace( $mg, $value, $tpl );
	}
	protected function replaceMagicWords2( $tpl )
	{
		foreach ( self::$magic_words as $mg => $varname )
		{
			$var = $this->getVar( $varname );
			$lowercase = strpos($mg, '_L');
			if ( $lowercase !== false )
				$var = strtolower( $var );

			$tpl = str_replace( $mg, $var, $tpl );
		}
		foreach ( self::$std_magic_words as $mg => $value )
			$tpl = str_replace( $mg, $value, $tpl );
		
		return $tpl;
	}
	/**
	 * Generates an XML file from a XML-ish array structure.
	 */
	protected function toXML( $top, $s, $level = 0 )
	{
		$r = null;
		if ($level === 0)
			$r .= '<'.'?xml version="1.0" encoding="UTF-8" ?>'."\n";

		$_attribs = null;
		if ( is_array( $s ) && (key( $s ) === 'attribs') )
		{
			$_attribs = current( $s );
			array_shift( $s );
		}
		// are we traversing a array of children?
		if ( !is_numeric( $top ) )
			$r .= $this->openTag( $top, $level, $_attribs );					

		if ( is_array( $s ) && (key( $s ) === '_content') )
			$s = current( $s );

		$shortClose = false;
		if (!is_array( $s ))
		{
			$r .= $s;
			$shortClose = true;
		}
		
		while( is_array( $s ) && !empty( $s ) )
		{
			$t =     key( $s );
			$c = current( $s );
		
			$r .= $this->toXML( $t, $c, $level+1 );
			
			// NEXT		
			array_shift( $s );					
		};
		// are we traversing a array of children?
		if ( !is_numeric( $top ))
			$r .= $this->closeTag( $top, $level, $shortClose );
		
		return $r;

	}//function
	/**
	 * Expand list
	 */
	protected function expandList( $top, &$liste, $level )
	{
		$r = null;
		if (!is_array(current( $liste )))
		{
			foreach( $liste as $tag => &$value )
			{
				$r .= $this->openTag( $tag, $level );
				$r .= $value;
				$r .= $this->closeTag( $tag, $level, true );
			}
			return $r;
		}
		foreach( $liste as &$entry )
		{
			$r .= $this->openTag( $top, $level );
			$r .= $this->expandList( $top, $entry, $level+1 );
			$r .= $this->closeTag( $top, $level, false );			
			
		}
		return $r;
	}
	protected function openTag( $tag, $level, $attribs = null )
	{
		$r  = "\n".str_repeat("\t", $level );	
		$r .= '<'.$tag;
		if (!empty( $attribs ))
			foreach( $attribs as $key => &$value )
				$r .= " $key=".'"'.$value.'"';
		$r .= '>';		
		return $r;
	}
	protected function closeTag( $tag, $level, $shortClose = true )
	{
		if (!$shortClose)
			$r  = "\n".str_repeat("\t", $level );			
		$r .= '</'.$tag.'>';
		return $r;
	}
	protected function toXMLlist( $tag, &$liste, $level )
	{
		if (empty( $liste ))
			return null;
		$r = null;
		foreach( $liste as &$e )
		{
			$r .= $this->openTag( $tag, $level );
			$r .= $e;
			$r .= $this->closeTag( $tag, $level );
		}
		return $r;
	}
}//end class
//</source>
/*
array(2) {
  ["attribs"]=>
  array(4) {
    ["xmlns"]=>
    string(48) "http://pear.php.net/dtd/rest.categorypackageinfo"
    ["xmlns:xsi"]=>
    string(41) "http://www.w3.org/2001/XMLSchema-instance"
    ["xmlns:xlink"]=>
    string(28) "http://www.w3.org/1999/xlink"
    ["xsi:schemaLocation"]=>
    string(105) "http://pear.php.net/dtd/rest.categorypackageinfo     http://pear.php.net/dtd/rest.categorypackageinfo.xsd"
  }
  ["pi"]=>
  &array(2) {
    ["p"]=>
    array(7) {
      ["n"]=>
      string(9) "Directory"
      ["c"]=>
      string(27) "jldupont.googlecode.com/svn"
      ["ca"]=>
      array(2) {
        ["attribs"]=>
        array(1) {
          ["xlink:href"]=>
          string(18) "/rest/c/Filesystem"
        }
        ["_content"]=>
        string(10) "Filesystem"
      }
      ["l"]=>
      string(0) ""
      ["s"]=>
      string(50) "Helper class for manipulating directory structures"
      ["d"]=>
      string(0) ""
      ["r"]=>
      array(1) {
        ["attribs"]=>
        array(1) {
          ["xlink:href"]=>
          string(17) "/rest/r/directory"
        }
      }
    }
    ["a"]=>
    array(1) {
      ["r"]=>
      array(3) {
        [0]=>
        array(2) {
          ["v"]=>
          string(5) "1.0.2"
          ["s"]=>
          string(6) "stable"
        }
        [1]=>
        array(2) {
          ["v"]=>
          string(5) "1.0.1"
          ["s"]=>
          string(6) "stable"
        }
        [2]=>
        array(2) {
          ["v"]=>
          string(5) "1.0.0"
          ["s"]=>
          string(6) "stable"
        }
      }
    }
  }
}
*/