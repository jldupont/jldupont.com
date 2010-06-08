<?php
/**
 * @package PearTools
 * @author Jean-Lou Dupont
 * @version $Id: ChannelCategories.php 262 2007-11-19 14:42:02Z JeanLou.Dupont $
 */
//<source lang=php>

require_once "PEAR/XMLParser.php";
require_once 'JLD/PearTools/PearObject.php';
require_once 'JLD/PearTools/Channel.php';
require_once 'JLD/Directory/Directory.php';


// use a class for namespace management.
class JLD_PearTools_ChannelCategories extends JLD_PearObject
{
	const thisVersion = '$Id: ChannelCategories.php 262 2007-11-19 14:42:02Z JeanLou.Dupont $';
	const tpl_c = '/Templates/categories.xml.tpl';
	const tpl_c2= '/Templates/categories.xml.tpl2';	
	
	const tpl_i = '/Templates/info.xml.tpl';	
	const tpl_p = '/Templates/packagesinfo.xml.tpl';  // top level
	const tpl_p2= '/Templates/packagesinfo.xml.tpl2'; // package instance
	const tpl_p3= '/Templates/packagesinfo.xml.tpl3'; // release instance
	
	// relative to the REST structure; this is standard.
	static $baseCategories = '/c';
	static $baseReleases = '/r'; // because of packagesinfo.xml file...
	var $rootPath  = null;
	var $baseREST  = null;
	var $restPathC = null;
	
	// categories
	var $cats = array();

	public function __construct( $version ) 
	{
		return parent::__construct( $version );
	}
	public static function singleton()
	{
		return parent::singleton( __CLASS__, self::thisVersion );	
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * Reads all the categories from the directory structure 
	 * in the REST channel.
	 */
	public function readAll()
	{
		$path  = $this->buildFileSystemRestPath( self::$baseCategories );
		// no need to get resursive here.
		$raw = JLD_Directory::getDirectoryInformation(	$path, $path, true, true );
		
		// strip off leading /
		foreach( $raw as &$e )
		{
			$e['name'] = substr( $e['name'], 1);
			$this->cats[] = $e['name'];
		}
	}
	/**
	 * Returns TRUE if the specified category exists
	 * in the current instance.
	 */
	public function existsCategory( )	
	{
		return (in_array( $this->category_name, $this->cats ));
	}
	/**
	 * Updates the 'categories.xml' file according to the
	 * registered categories contained in the class instance.
	 */
	public function updateCategories()
	{
		if (empty( $this->cats ))
			return null;
			
		$tpl = $this->getTemplate( self::tpl_c );	
		$tpl2 = $this->getTemplate( self::tpl_c2 );	
		
		$this->__set( 'base_categories', self::$baseCategories );
		
		$result = '';
		foreach ($this->cats as $cat)
		{
			// we are not using 'category_name' in order
			// not to disrupt the rest of the class.
			$this->setVar( 'category1_name', $cat );
			
			// format one line
			$result .= $this->replaceMagicWords2( $tpl2 );	
		}
		// do replacement in the base template
		// the channel name parameter was already set.		
		$this->setVar( 'all_categories', $result );
		$final_result = $this->replaceMagicWords2( $tpl );

		// finally, write the file!		
		$file  = $this->buildFileSystemRestPath( self::$baseCategories ).'/categories.xml';
		return $this->writeFile( $file, $final_result );
	}
	/**
	 * Updates / creates the 'info.xml' file associated with a category.
	 * Make sure the category directory exists prior to using this method.
	 * <n>$category_name$</n>
	 * <c>$channel_name$</c>
	 * <a>$category_name$</a> <!-- alias? -->
	 * <d>$description$</d>
	 */
	public function updateCategoryInfo( $name )
	{
		$tpl = $this->getTemplate( self::tpl_i );	
		
		$this->__set( 'category_name', $name );
		$this->replaceMagicWords( $tpl );
		
		// write the file
		$file  = $this->buildFileSystemRestPath( self::$baseCategories ).'/'.$name.'/info.xml';		
		return $this->writeFile( $file, $tpl );
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	/**
	 * Updates / creates the 'packagesinfo.xml' file in the REST structure.
	 * Must read in the current file, parse it & then add the specified information.
	 */
	public function updatePackagesInfo(	) 
	{
		$this->__set( 'base_releases', '/r' );
		
		$file = $this->buildFileSystemRestPath( self::$baseCategories ).'/'.$this->category_name.'/packagesinfo.xml';
		$c = @file_get_contents( $file );

		// if the file does not exists, then create one
		// from the template.
		if (empty( $c ))
			return $this->createPackageInfo( );
		
		// create release section hoping we will succeed the operation that follows.
		$r = array( 'v' => $this->package_version, 's' => $this->package_stability );
		
		// the file exists... the 'fun' begins...
		$p = $this->parse( $c );
		
		$found_pi = false;
		// we need to insert a release in the specified package...
		foreach( $p as $key => &$value )
		{
			if ( !isset($key['pi']))
				continue;
			if ( !isset( $value['p'] ))
				continue;
			if ( !isset( $value['p']['n'] ))
				continue;
			$current_name = $value['p']['n'];
			if ($current_name !== $this->package_name)
				continue;
				
			$found_pi = true;
			// at this point, we should have found the proper <pi> section
			// to insert the release information into.
			if ( !isset( $value['a']['r']))
				throw new Exception( 'malformed "packagesinfo.xml" file: missing <r> section' );

			// push the entry at the top...
			array_unshift( $value['a']['r'], $r );
			// bail out
			break;
		}//end foreach
		
		if (!$found_pi)
			throw new Exception( 'error in "packagesinfo.xml" file: package instance not found or malformed' );
			
		// format the packageinfo file 
		$x = $this->toXML( 'f', $p );
		
		// finally, write the file!
		return $this->writePackageInfoFile( $this->category_name, &$x );
	}
	/**
	 * Creates a new packageinfo.xml file from scratch.
	 */
	public function createPackageInfo( )
	{
		$rs = $this->createReleaseSection( );
		$this->__set( 'all_releases', $rs );
				
		// we just have one release to take care.
		$pi = $this->createPackageInstance( );
			
		$c = $this->insertTopPackagesInfo( $pi );
		return $this->writePackageInfoFile( $this->category_name, $c );		
	}
	/**
	 * Creates a <pi> section.
	 * The <r> sections should be ready before using this method.
	 */
	public function createPackageInstance( )
	{
		$tpl = $this->getTemplate( self::tpl_p2 );
		return $this->replaceMagicWords2( $tpl );
	}
	/**
	 * Creates a release section for inclusion in
	 * the 'packageinfo.xml' file.
	 */
	public function createReleaseSection( )
	{
		$tpl = $this->getTemplate( self::tpl_p3 );
		return $this->replaceMagicWords2( $tpl );
	}
	/**
	 * Writes a complete 'packageinfo.xml' file.
	 */
	public function writePackageInfoFile( $categoryName, &$r )
	{
		$file= $this->buildFileSystemRestPath( self::$baseCategories ).'/'.$categoryName.'/packagesinfo.xml';
		return $this->writeFile( $file, $r );
	}	 
	/**
	 * Inserts $contents in the top level template
	 * for the 'packagesinfo.xml' file
	 */
	public function insertTopPackagesInfo( &$contents )
	{
		$tpl = $this->getTemplate( self::tpl_p );
		return str_replace( '$all_packages$', $contents, $tpl );
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * Parses an XML file.
	 */
	protected function parse( &$contents )
	{
		$parser = new PEAR_XMLParser;
		$result = $parser->parse( $contents );
		if (!$result)
			return false;
		return $parser->getData();
	}
		
}
//</source>
/* Example 'packagesinfo.xml' file

array(2) 
{
  
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
  array(2) {
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
    } //end p
	
    ["a"]=>
    array(1) {
      ["r"]=>
      array(2) {
        [0]=>
        array(2) {
          ["v"]=>
          string(5) "1.0.1"
          ["s"]=>
          string(6) "stable"
        }
        [1]=>
        array(2) {
          ["v"]=>
          string(5) "1.0.0"
          ["s"]=>
          string(6) "stable"
        }
      }
    }// end a
	
  }// end pi

}//end
*/
