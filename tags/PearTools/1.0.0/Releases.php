<?php
/*
	PEAR Channel Tool

	@author: Jean-Lou Dupont
	$Id: Releases.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $
*/
//<source lang=php>

require_once "PEAR/XMLParser.php";
require_once 'JLD/Object/Object.php';

// use a class for namespace management.
class JLD_PearTools_Releases extends JLD_Object
{
	const thisVersion = '$Id: Releases.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $';
	static $baseReleases = '/r';	

	static $thisDir = null;	

	var $channel;
	var $rootPath;
	var $baseREST;
	var $restPathR;
	var $channelURI;
	
	public function __construct( $version ) 
	{
		return parent::__construct( $version );
	}
	public static function singleton()
	{
		return parent::singleton( __CLASS__, self::thisVersion );	
	}
	public function init( &$channel )
	{
		$this->channel = $channel;

		self::$thisDir = dirname( __FILE__ );
		$this->channel = $channel;
		$this->rootPath = $this->channel->getRootPath();
		$this->baseREST = $this->channel->getRESTPath();
		$this->restPathR = $this->rootPath . $this->baseREST . self::$baseReleases;
		$this->channelURI = $this->channel->getURI();
	}
	/**
	 */
	public function createVersionFile( $packageName, $version, $stability, &$msg )
	{
		$msg = '';
		
		$file = $this->genFilePath( $packageName ).'/'."$version.xml";
		
		$tpl = $this->getTemplate( 'version' );
		if (empty( $tpl ))
		{
			$msg = 'error reading "version.xml.tpl" template file';
			return false;
		}
		
		$this->replaceMagicWords( $tpl, $this->channelURI, $packageName, $version, $stability );
		
		$msg = 'error writing '."$version.xml file";
		$len = strlen( $tpl );
		$bytes_written = @file_put_contents( $file, $tpl );

		return ($len === $bytes_written);
	}
	public function genFilePath( $packageName )
	{
		return $this->restPathR . '/' . strtolower( $packageName );
	}
	protected function getTemplate( $name )
	{
		$file = self::$thisDir."/Templates/$name.xml.tpl";
		$contents = @file_get_contents( $file );
		return $contents;
	}
	protected function replaceMagicWords( &$tpl, $channel, $packageName, $version, $stability = 'stable' )
	{
		$tpl = str_replace( '$packageH$', $packageName, $tpl);
		$tpl = str_replace( '$packageL$', strtolower($packageName), $tpl);		
		$tpl = str_replace( '$channel$', $channel, $tpl);
		$tpl = str_replace( '$version$', $version, $tpl);
		$tpl = str_replace( '$stability$', $stability, $tpl);		
						
	}
	public function generateDepsFile( $packageName, $version, &$contents, &$msg )
	{
		$msg = '';
		
		$file = $this->genFilePath( $packageName ).'/'."deps.$version.txt";
		
		$msg = 'error writing '."deps.$version.txt file";
		$len = strlen( $contents );
		$bytes_written = @file_put_contents( $file, $contents );

		return ($len === $bytes_written);
	}

	const markerPattern = '<!--$release:%packagename%$-->';
	const releasePattern = "\t\t\t<r>\n\t\t\t\t<v>%version%</v>\n\t\t\t\t<s>%stability%</s>\n\t\t\t</r>\n";
	
	public function updateAllReleasesFile( $packageName, $version, $stability, &$msg )
	{
		$msg = '';
		
		$file = $this->genFilePath( $packageName ).'/'."allreleases.xml";
		
		$msg = 'getting contents of "allreleases.xml" file.';
		$contents = @file_get_contents( $file );
		if (empty( $contents ))
			return false;
		
		$msg = 'marker pattern not found';;
		$result = $this->existsMarker( $contents, $packageName );
		if ( !$result)
			return false;
		
		$this->addRelease( $contents, $packageName, $version, $stability );
		
		$msg = 'error writing "allreleases.xml" file';
		$len = strlen( $contents );
		$bytes_written = @file_put_contents( $file, $contents );

		return ($len === $bytes_written);
	}
	/**
	 * This method constitutes the main raison d'etre of this whole file.
	 */
	protected function addRelease( &$c, $packageName, $version, $stability = 'stable' )
	{
		$r = str_replace('%version%', $version, self::releasePattern );
		$r = str_replace('%stability%', $stability, $r );
		$this->replaceMarker( $c, $packageName, $r );
	}
	protected function replaceMarker( &$c, &$packageName, &$replacement )
	{
		$p = str_replace('%packagename%', $packageName, self::markerPattern );
		$r = $p."\n".$replacement;
		$c = str_replace( $p, $r, $c );		
	}	
	protected function existsMarker( &$c, $packageName )
	{
		$p = str_replace('%packagename%', $packageName, self::markerPattern );
		$r = strpos( $c, $p );
		
		return ($r === false) ? false:true;
	}
}
//</source>