<?php
/**
 * @author Jean-Lou Dupont
 * @package PearTools
 * @version 1.2.0
 * @id $Id: ChannelReleases.php 278 2007-11-24 00:48:12Z JeanLou.Dupont $
 */
//<source lang=php>
require_once "PEAR/XMLParser.php";
require_once 'JLD/PearTools/PearObject.php';
require_once 'JLD/PearTools/Channel.php';
require_once 'JLD/PearTools/Package.php';
require_once 'JLD/Directory/Directory.php';


// use a class for namespace management.
class JLD_PearTools_ChannelReleases extends JLD_PearObject
{
	const thisVersion = '$Id: ChannelReleases.php 278 2007-11-24 00:48:12Z JeanLou.Dupont $';

	// Template related
	const tpl_v = '/Templates/version.xml.tpl';
	const tpl_r = '/Templates/allreleases.xml.tpl';	

	// relative to the REST structure; this is standard.
	static $baseReleases = '/r';
		
	public function __construct( $version ) 
	{
		return parent::__construct( $version );
	}
	public static function singleton()
	{
		return parent::singleton( __CLASS__, self::thisVersion );	
	}
	/**
	 * The required variables must have been set prior to calling this method.
	 * (see initVars in PearObject)
	 */
	public function createVersionFile( )
	{
		$tpl = $this->getTemplate( self::tpl_v );
		if (empty( $tpl ))
			return false;
		
		$c = $this->replaceMagicWords2( $tpl );
		
		$v = $this->package_version;
		
		$file =  $this->buildFileSystemRestPath( self::$baseReleases );
		$file .= '/'.$this->package_name.'/'."$v.xml";
		
		return $this->writeFile( $file, $c );
	}
	/**
	 * Create deps.$version.txt file
	 * Must read & parse source package.xml file,
	 * extract 'depedencies' section and serialize.
	 */
	public function createDepsFile( )
	{
		$v = $this->package_version;
		$file  = $this->buildFileSystemRestPath( self::$baseReleases );
		$file .= '/'.$this->package_name."/deps.$v.txt";
		return $this->writeFile( $file, $this->package_dependencies );
	}
	/**
	 *
	 */
	public function getAllFilesFromRest()
	{
		$path = $this->buildFileSystemRestPath( self::$baseReleases ).'/'.$this->package_name;
		$raw = JLD_Directory::getDirectoryInformation( $path, $path, true, false );		
		return $raw;
	}
	/**
	 * Scans the package's /r REST directory for all package names.
	 */
	public function getAllPackagesFileNames()
	{
		$raw = $this->getAllFilesFromRest();
		$pattern = '/package\.(.*)\.xml/ui';

		$all = array();

		if ( !empty( $raw ) )
			foreach( $raw as &$e )
			{
				$r = preg_match( $pattern, $e['name'], $m );
				if (( $r === false ) || ( $r === 0 ))
					continue;
				$all[] = $e['name'];
			}
			
		return $all;
	}
	/**
	 * Scans the package's /r REST directory and finds 
	 * all releases associated with a package. This is accomplished by reading in
	 * all the files of the pattern 'package.$version.xml'
	 */
	public function getAllReleases( &$version_list = null )
	{
		$names = $this->getAllPackagesFileNames();
		if (empty( $names ))
			return false;
			
		$baseDir = $this->buildFileSystemRestPath( self::$baseReleases ).
					'/'.$this->package_name.'/';
		
		$all = array();
		$c = null; // dummy.			
		foreach( $names as $name )
		{
			$p = new JLD_PearTools_Package( $c, $baseDir.$name );
			if (!$p->isValid())
				throw new Exception( 'invalid package.xml file: '.$baseDir.$name );
				
			$v = $p->getVersion();
			if (is_array( $version_list ))
				$version_list[] = $v;
			
			$e['v'] = $v;
			$e['s'] = $p->getStability();
			
			$all[] = $e;
			
			$p = null;
		}
		return $all;
	}
	/**
	 * Verifies the existence of a given release.
	 */
	public function releaseExists( $version )
	{
		$liste = array();
		$this->getAllReleases( $liste );
		if (empty( $liste ))
			return false;
		return (in_array( $version, $liste ));
	}
	/**
	 * Creates the 'allreleases.xml' file taking into account
	 * all the releases of a particular package.
	 */
	public function createAllReleasesFile()
	{
		$all = $this->getAllReleases();
		
		// sort: this step is crucial to ensure the PEAR upgrade process works.
		$sorted = $this->krsort( $all );
		
		// format in XML
		$xml = $this->expandList('r', $sorted, 1 );
		$this->__set( 'all_releases', $xml );

		// replace in template
		$tpl = $this->getTemplate( self::tpl_r );
		$c = $this->replaceMagicWords2( $tpl );

		// write the file to the filesystem
		$file = $this->buildFileSystemRestPath( self::$baseReleases ).
					'/'.$this->package_name.'/allreleases.xml';
					
		return $this->writeFile( $file, $c );			
	}
	/**
	 * Sorts the releases list in reverse version order 
	 * based on the 'v' field
	 */
	protected function & krsort( &$all )
	{
		$sorted = array();
		
		if (empty( $all ))
			return false;
			
		// starts by re-tagging each entry
		foreach	( $all as &$e )
			$sorted[ $e['v'] ] = $e;
			
		uksort( $sorted, array( __CLASS__, 'c_krsort' ));
		
		return $sorted;
	}
	/**
	 * Custom sorting function
	 */
	 protected static function c_krsort( $a, $b )
	 {
	 	return -version_compare( $a, $b );
	 }
}//end class
//</source>