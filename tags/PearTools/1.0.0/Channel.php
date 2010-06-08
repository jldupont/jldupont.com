<?php
/*
	PEAR Channel Tool: parses 'channel.xml' file given the root directory.
	Command Line Utility
	@author: Jean-Lou Dupont
	$Id: Channel.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $
*/
//<source lang=php>

require_once "PEAR/XMLParser.php";
require_once 'JLD/PearTools/PearObject.php';

// use a class for namespace management.
class JLD_PearTools_Channel extends JLD_PearObject
{
	const thisVersion = '$Id: Channel.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $';
	const tpl = '/Templates/channel.xml.tpl';
	
	// Template related
	static $magic_words = array(
	'$name$' 	=> 'name',
	'$alias$'	=> 'alias',
	'$uri$'		=> 'uri',
	);
	
	// REST related
	static $baseREST = '/rest';
	static $baseTAGS = '/tags';
	static $file_name = 'channel.xml';
	static $rest_directories = array( '/c', '/m', '/p', '/r', '/tags' );
	
	// filesystem absolute path to channel
	var $dir = null; 
	
	var $contents = null;
	var $data = null; // parsed file.
	
	public function __construct( $version ) 
	{
		return parent::__construct( $version );
	}
	public static function singleton()
	{
		return parent::singleton( __CLASS__, self::thisVersion );	
	}
	public function getURI() { return $this->getVar('name'); }  // !!!
	public function getName() { return $this->getVar('name'); }	
	public function getAlias() { return $this->getVar('alias'); }		
	public function getRootPath() { return $this->dir; }
	public function getRESTPath() { return self::$baseREST; }
	public function getTAGSPath() { return self::$baseTAGS; }	
		
	/**
	 *
	 * @input provide the root path of the channel.
	 */
	function init( $rootPath )
	{
		$this->dir = $rootPath;

		$this->setVar( 'file', $this->dir.'/'.self::$file_name );
		
		$this->contents = @file_get_contents( $this->getVar('file') );
		if (empty( $this->contents ))
			return false;
			
		$name = $this->parse( $this->contents );
		$this->setVar( 'alias', @$this->data['suggestedalias'] );
		return $name;
	}
	/**
	 */
	protected function parse( &$contents )
	{
		$parser = new PEAR_XMLParser;
		$result = $parser->parse( $contents );
		if (!$result)
			return false;
		$this->data = $parser->getData();
		
		if (isset($this->data['name']))
		{
			$this->setVar('name', $this->data['name'] );
			return $this->data['name'];
		}
			
		return null;
	}
	
	/**
	 * This method creates the 'channel.xml' file based on the
	 * template.
	 *
	 * The parameters must have been set beforehand
	 */
	public function create( )
	{
		$tpl = $this->getTemplate( self::tpl );
		if (empty( $tpl ))
			return false;
		$this->replaceMagicWords( $tpl, self::$magic_words );
		$this->setVar( 'tpl', $tpl );
		
		return true;
	}
	/**
	 */
	public function write()
	{
		$contents = $this->getVar( 'tpl' );	
		$file = $this->getVar('file');
		$bytes_written = @file_put_contents( $file, $contents );
		return (strlen( $contents ) === $bytes_written );
	}
	/**
	 * Creates the REST directory structure
	 */
	public function createRest()
	{
		$base = $this->getVar('path');
		foreach( self::$rest_directories as $dir )
		{
			if (is_dir( $dir ))
				continue;
			if (mkdir( $base.$dir ) === false)
				return false;
		}		
		return true;	
	}	
}
//</source>