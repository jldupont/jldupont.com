<?php
/*
	PEAR Channel Tool: parses the 'categories' folder of the REST interface.
	@author: Jean-Lou Dupont
	$Id: Categories.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $
	
	The main purpose of these classes is to be able to add a release
	to a 'packagesinfo.xml' file of a given REST category.
	
	!!!OBSOLETE!!!
*/
//<source lang=php>

require_once "PEAR/XMLParser.php";
require_once 'JLD/Object/Object.php';
require_once 'JLD/PearTools/Channel.php';
require_once 'JLD/Directory/Directory.php';
require_once 'JLD/PearTools/Xml.php';

// use a class for namespace management.
class JLD_PearTools_Categories extends JLD_Object implements Iterator
{
	const thisVersion = '$Id: Categories.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $';
	static $baseCategories = '/c';
	
	var $channel = null;
	var $categories = array();
	var $raw;
	
	static $thisDir;
	
	//
	var $rootPath = null;
	var $baseREST = null;
	var $restPathC = null;
	
	public function getAll() 	{ return $this->categories; }
	
	// Iterator Interface
	public function current()	{ return $this->cats->current(); }
	public function key()		{ return $this->cats->key(); }
	public function next()		{ return $this->cats->next(); }
	public function rewind()	{ return $this->cats->rewind(); }
	public function valid()		{ return $this->cats->valid(); }	
	
	public function __construct( $version ) 
	{
		return parent::__construct( $version );		
	}
	public static function singleton()
	{
		return parent::singleton( __CLASS__, self::thisVersion );	
	}
	/**
	 */
	public function init( &$channel )
	{
		self::$thisDir = dirname( __FILE__ );
		$this->channel = $channel;
		$this->rootPath = $this->channel->getRootPath();
		$this->baseREST = $this->channel->getRESTPath();
		$this->restPathC = $this->rootPath . $this->baseREST . self::$baseCategories;
		$this->raw = $this->readAll();
	}
	/**
	 */
	protected function readAll()
	{
		// no need to get resursive here.
		$raw = JLD_Directory::getDirectoryInformation( $this->restPathC, $this->restPathC, true, true );
		
		// strip off leading /
		foreach( $raw as &$e )
		{
			$e['name'] = substr( $e['name'], 1);
			$this->cats[] = $e['name'];
		}
		
		return $raw;
	}
	/**
	 */
	public function & getPackageInfoObject( $categoryName )
	{
		$file = null;
		// get the 'packageinfo.xml' file from the desired category.
		$contents = $this->getPackageInfoFile( $categoryName, $file );
		if (empty( $contents ))
			return null;
		
		return new JLD_SimplePackagesinfo( $categoryName, $contents, $file );		
	}	
	public function updatePackageInfoFile( &$pif )
	{
		$cname = $pif->getCategoryName();
		return $this->writePackageInfoFile( $cname, $pif->get() );
	}
	/**
	 */
	public function existsCategory( $categoryName )	
	{
		return (in_array( $categoryName, $this->cats ));
	}
	/**
	 */
	public function getPackageInfoFile( $cName, &$file )
	{
		$file = $this->restPathC.'/'.$cName.'/packagesinfo.xml';
		return @file_get_contents( $file );
	}
	/**
	 */
	protected function writePackageInfoFile( $cName, &$contents )
	{
		$file = $this->restPathC.'/'.$cName.'/packagesinfo.xml';
		$len = strlen( $contents );
		$bytes_written = @file_put_contents( $file, $contents );
		return ( $bytes_written === $len );
	}
	/**
	 * Need to scan through all packagesinfo.xml files to find the match...
	 */
	public function findCategoryNameForPackageName( $packageName, &$msg )	
	{
		$msg = 'package not found OR release marker not found.';
		if (empty( $this->cats ))
		{
			$msg = 'no categories found';
			return false;
		}
		$result = false;
		foreach ( $this->cats as &$cat )
		{
#			echo __METHOD__." cat: ".$cat."\n";
			
			$o = $this->getPackageInfoObject( $cat );
			if (!is_object( $o ))
			{
				$msg = 'error reading "packageinfo.xml"';
				return false;
			}
			$r = $o->existsReleaseMarker( $packageName );
			if ($r === true)
			{
				$result = $cat;
				break;
			}
		}
		
		return $result;
	}

}

class JLD_SimplePackagesinfo
{
	const markerPattern = '<!--$release:%packagename%$-->';
	const releasePattern = "\t\t\t<r>\n\t\t\t\t<v>%version%</v>\n\t\t\t\t<s>%stability%</s>\n\t\t\t</r>\n";
	
	var $raw = null;
	var $file = null;
	var $category = null;
	
	public function __construct( $categoryName, &$s, &$file )
	{
		$this->category = $categoryName;
		$this->raw = $s;	
		$this->file = $file;
	}
	public function getCategoryName() { return $this->category; }
	public function getFileName() { return $this->file; }
	public function existsReleaseMarker( &$packageName )
	{
		$p = str_replace('%packagename%', $packageName, self::markerPattern );
		$r = strpos( $this->raw, $p );
		
		return ($r === false) ? false:true;
	}
	/**
	 * This method constitutes the main raison d'etre of this whole file.
	 */
	public function addRelease( $packageName, $version, $stability = 'stable' )
	{
		$r = str_replace('%version%', $version, self::releasePattern );
		$r = str_replace('%stability%', $stability, $r );
		$this->replaceMarker( $packageName, $r );
	}
	public function replaceMarker( &$packageName, &$replacement )
	{
		$p = str_replace('%packagename%', $packageName, self::markerPattern );
		$r = $p."\n".$replacement;
		$this->raw = str_replace( $p, $r, $this->raw );		
	}	
	/**
	 */
	public function & get(){ return $this->raw; }
	
} // end class


// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

// not used at the moment.

class JLD_Packagesinfo extends JLD_PearTools_Xml
{
static $map = array(
// Elements found in 'packagesinfo.xml'
'f|attribs'		=> array( 'type' => 'attr',	'tpl' => '<f %attribs% >%n%%contents%%n%' ),
'f|pi' 			=> array( 'type' => 'br',	'tpl' => '%t%<pi>%n%%contents%%n%%t%</pi>%n%'),
'f|pi|p' 		=> array( 'type' => 'br',	'tpl' => '%t%%t%<p>%n%%contents%%n%%t%%t%</p>%n%'),	
'f|pi|p|n' 		=> array( 'type' => 'leaf',	'tpl' => '%t%%t%%t%<n>%contents%</n>%n%'),								
'f|pi|p|c'		=> array( 'type' => 'leaf',	'tpl' => '%t%%t%%t%<c>%contents%</c>%n%'),								
'f|pi|p|ca'		=> array( 'type' => 'attr',	'tpl' => '%t%%t%%t%<ca %attribs%>%contents%</ca>%n%'),
'f|pi|p|l'		=> array( 'type' => 'leaf', 'tpl' => '%t%%t%%t%<l>%contents%</l>%n%'),
'f|pi|p|s'		=> array( 'type' => 'leaf', 'tpl' => '%t%%t%%t%<s>%contents%</s>%n%'),
'f|pi|p|d'		=> array( 'type' => 'leaf', 'tpl' => '%t%%t%%t%<d>%contents%</d>%n%'),
'f|pi|p|r'		=> array( 'type' => 'attr', 'tpl' => '%t%%t%%t%<r %attribs% />%n%'),
'f|pi|a' 		=> array( 'type' => 'br', 	'tpl' => '%t%%t%<a>%n%%contents%%n%%t%%t%</a>%n%'),
'f|pi|a|r'		=> array( 'type' => 'br', 	'tpl' => '%t%%t%%t%<r>%n%%contents%%n%%t%%t%%t%</r>%n%'),
'f|pi|a|r|v'	=> array( 'type' => 'leaf', 'tpl' => '%t%%t%%t%%t%<v>%contents%</v>%n%'),
'f|pi|a|r|s' 	=> array( 'type' => 'leaf', 'tpl' => '%t%%t%%t%%t%<s>%contents%</s>%n%'),
);

	public function __construct( &$contents )
	{
		$this->raw = $contents;
		$this->data = $this->parse( $contents );
	}
	protected function parse( &$contents )
	{
		$parser = new PEAR_XMLParser;
		$result = $parser->parse( $contents );
		if (!$result)
			return false;
		return $parser->getData();
	}
	public function getRaw() { return $this->data; }
	public function getXML()
	{
		$this->emap = self::$map;
		$result = $this->get( 'f', $this->data );
		$result .= "\n<"."/f>\n";
		return $result;
	}
	
}// end class

//</source>