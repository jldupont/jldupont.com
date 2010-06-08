<?php
/**
 * @author Jean-Lou Dupont
 * @package PearTools
 * @version @@package-version@@
 * @id $Id: Package.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php>

require_once 'JLD/Object/Object.php';

// use a class for namespace management.
class JLD_PearTools_Package extends JLD_Object
{
	const thisVersion = '$Id: Package.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $';
	
	var $file;
	var $data = null;
	var $raw;
	var $valid = false;
	
	public function __construct( &$contents, $filename )
	{
		$this->raw = $contents;
		$this->file = $filename;
		
		if (empty( $contents ))
			$this->raw = $this->readFile( $filename );
		
		$this->parse( );
	}
	public function readFile( $filename )
	{
		return @file_get_contents( $filename );
	}
	public function updateFile()
	{
		$this->updateDateTime();
		$bytes_written = @file_put_contents( $this->file, $this->raw );
		return (strlen($this->raw) === $bytes_written ) ? true:false;
	}
	/**
	 */
	protected function & parse( )
	{
		if ( empty($this->raw ))
			return false;
		$parser = new PEAR_XMLParser;
		$this->valid = $parser->parse( $this->raw );
		if (!$this->valid)
			return false;
		$this->data = $parser->getData();
	}
	/**
	 * Returns TRUE if the parsed package appears valid
	 */
	public function isValid()
	{
		return $this->valid;
	}
	/**	
	 * Returns the package release version
	 */
	public function getVersion()
	{
		return @$this->data['version']['release'];
	}
	/**	
	 * Returns the package release stability
	 */
	public function getStability()
	{
		return @$this->data['stability']['release'];
	}
	/**
	 * Returns the package name
	 */
	public function getName()
	{
		return @$this->data['name'];
	}
	/**
	 */
	public function getSummary()
	{
		return @$this->data['summary'];
	}
	/**
	 */
	public function getDescription()
	{
		return @$this->data['description'];
	}
	/**
	 */
	public function getChangelog()
	{
		return @$this->data['changelog']['release']['notes'];	
	}	 	
	/**
	 */
	public function getRaw()	{ return $this->raw; }
	/**	
	 */
	public function getData()	{ return $this->data; }
	/**	
	 */
	protected function updateDateTime()
	{
		$p_date = '/\<'.'date\>'.'.*'.'\<\/date\>'.'/siU';
		$p_time = '/\<'.'time\>'.'.*'.'\<\/time\>'.'/siU';
		
		$date = '<'.'date'.'>'.gmdate("Y-m-d").'</date'.'>';
		$time = '<'.'time'.'>'.gmdate("H:i:s").'</time'.'>';
		
		$this->raw = preg_replace( $p_date, $date, $this->raw );
		$this->raw = preg_replace( $p_time, $time, $this->raw );
	}
	public function getSerializedDependencies()
	{
		return serialize( @$this->data['dependencies'] );
	}
}
//</source>
/*
array(16) {
  ["attribs"]=>
  array(6) {
    ["packagerversion"]=>
    string(5) "1.7.0"
    ["version"]=>
    string(3) "2.0"
    ["xmlns"]=>
    string(35) "http://pear.php.net/dtd/package-2.0"
    ["xmlns:tasks"]=>
    string(33) "http://pear.php.net/dtd/tasks-1.0"
    ["xmlns:xsi"]=>
    string(41) "http://www.w3.org/2001/XMLSchema-instance"
    ["xsi:schemaLocation"]=>
    string(150) "http://pear.php.net/dtd/tasks-1.0 http://pear.php.net/dtd/tasks-1.0.xsd    http://pear.php.net/dtd/package-2.0 http://pear.php.net/dtd/package-2.0.xsd"
  }
  ["name"]=>
  string(9) "Directory"
  ["channel"]=>
  string(27) "jldupont.googlecode.com/svn"
  ["summary"]=>
  string(10) "Summary..."
  ["description"]=>
  string(14) "Description..."
  ["lead"]=>
  array(4) {
    ["name"]=>
    string(15) "Jean-Lou Dupont"
    ["user"]=>
    string(8) "jldupont"
    ["email"]=>
    string(21) "jldupont@jldupont.com"
    ["active"]=>
    string(3) "yes"
  }
  ["date"]=>
  string(10) "2007-11-07"
  ["time"]=>
  string(8) "16:01:15"
  ["version"]=>
  array(2) {
    ["release"]=>
    string(5) "1.0.0"
    ["api"]=>
    string(5) "1.0.0"
  }
  ["stability"]=>
  array(2) {
    ["release"]=>
    string(6) "stable"
    ["api"]=>
    string(6) "stable"
  }
  ["license"]=>
  array(2) {
    ["attribs"]=>
    array(1) {
      ["uri"]=>
      string(26) "http://www.php.net/license"
    }
    ["_content"]=>
    string(11) "PHP License"
  }
  ["notes"]=>
  string(6) "$notes"
  ["contents"]=>
  array(1) {
    ["dir"]=>
    array(2) {
      ["attribs"]=>
      array(1) {
        ["name"]=>
        string(1) "/"
      }
      ["file"]=>
      array(1) {
        ["attribs"]=>
        array(3) {
          ["baseinstalldir"]=>
          string(13) "JLD/Directory"
          ["name"]=>
          string(13) "Directory.php"
          ["role"]=>
          string(3) "php"
        }
      }
    }
  }
  ["dependencies"]=>
  array(1) {
    ["required"]=>
    array(2) {
      ["php"]=>
      array(2) {
        ["min"]=>
        string(5) "5.0.0"
        ["max"]=>
        string(5) "6.0.0"
      }
      ["pearinstaller"]=>
      array(1) {
        ["min"]=>
        string(7) "1.4.0a2"
      }
    }
  }
  ["phprelease"]=>
  array(1) {
    ["installconditions"]=>
    array(1) {
      ["os"]=>
      array(1) {
        ["name"]=>
        string(1) "*"
      }
    }
  }
  ["changelog"]=>
  array(1) {
    ["release"]=>
    array(2) {
      ["license"]=>
      array(2) {
        ["attribs"]=>
        array(1) {
          ["uri"]=>
          string(26) "http://www.php.net/license"
        }
        ["_content"]=>
        string(11) "PHP License"
      }
      ["notes"]=>
      string(0) ""
    }
  }
}
*/