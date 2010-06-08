<?php
/**
 * @package PearTools
 * @author Jean-Lou Dupont
 * @version $Id: ChannelPackages.php 263 2007-11-19 15:02:17Z JeanLou.Dupont $
 */
//<source lang=php>

require_once "PEAR/XMLParser.php";
require_once 'JLD/PearTools/PearObject.php';
require_once 'JLD/PearTools/Channel.php';
require_once 'JLD/Directory/Directory.php';


// use a class for namespace management.
class JLD_PearTools_ChannelPackages extends JLD_PearObject
{
	const thisVersion = '$Id: ChannelPackages.php 263 2007-11-19 15:02:17Z JeanLou.Dupont $';

	// Template related
	const tpl_i = '/Templates/info.xml.tpl2';
	const tpl_ap= '/Templates/packages.xml.tpl';	

	// relative to the REST structure; this is standard.
	static $basePackages = '/p';
	var $rootPath  = null;
	var $baseREST  = null;
	var $restPathP = null;
		
	public function __construct( $version ) 
	{
		return parent::__construct( $version );
	}
	public static function singleton()
	{
		return parent::singleton( __CLASS__, self::thisVersion );	
	}
	/**
	 * Creates the 'info.xml' file based on a template.
	 */
	public function	createPackageInfoFile(  )
	{
		$this->__set( 'base_releases', '/r' );
		
		$tpl = $this->getTemplate( self::tpl_i );
		$c = $this->replaceMagicWords2( $tpl );

		$file = $this->buildFileSystemRestPath( self::$basePackages ).'/'.$this->package_name.'/info.xml';
		return $this->writeFile( $file, $c );
	}
	/**
	 * Gets all the directory names from the base /p REST structure.
	 */
	public function getAllDirsFromRest()
	{
		$path = $this->buildFileSystemRestPath( self::$basePackages );
		$raw = JLD_Directory::getDirectoryInformation( $path, $path, true, true /*only dirs*/ );		
		return $raw;
	}
	/**
	 * Reads all the packages from the filesystem's REST structure.
	 */
	public function createPackagesFile()
	{
		$all = $this->getAllDirsFromRest();
		if (empty( $all ))	
			return false;
		
		// strips off leading / and re-formats.
		foreach( $all as &$e)
			$s[] = substr( $e['name'],1 );
		
		$xml = $this->toXMLList( 'p', $s, 1 );
		$this->__set( 'all_packages', $xml );
		
		// templating
		$tpl = $this->getTemplate( self::tpl_ap );
		$c = $this->replaceMagicWords2( $tpl );
		
		// write the file to the filesystem
		$file = $this->buildFileSystemRestPath( self::$basePackages ).
					'/packages.xml';
					
		return $this->writeFile( $file, $c );			
	}
}
//</source>