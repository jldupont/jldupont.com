<?php
/**
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @subpackage SortTask
 * @version 1.9.0
 * @Id $Id: FileSetSorter.php 304 2007-12-17 02:17:29Z JeanLou.Dupont $
 */
//<source lang=php> 
require 'DataTypeSorter.php';

class FileSetSorter extends DataTypeSorter
{
	static $keyMap = array(
		'mtime', 'ctime'
	);
	
	var $sObj = null;
	var $tObj = null;
	var $tId = null;
	var $ds = null;
	var $root = null;
	var $srcFiles = array();
	var $srcDirs = array();
	var $project;
	
	public function __construct( &$prj, &$obj, $tid )
	{
		$this->project = $prj;
		$this->sObj = $obj;
		$this->tId = $tid;
	}
	
	/**
	 * Main method
	 */
	public function sort( &$ref, $key, $dir )
	{
		$this->ref = $ref;
		$this->key = $key;
		$this->dir = $dir;
		
		$this->init();
		
		$this->doSort();
		
		$this->finalize();
	}
	/**
	 * Sort by 'modification' time
	 */
	protected function helper_mtime( &$file )
	{
		$abs_path = $this->root.'/'.$file;		
		return @filemtime( $abs_path );
	}
	/**
	 * Sort by 'creation' time
	 */
	protected function helper_ctime( &$file )
	{
		$abs_path = $this->root.'/'.$file;
		return @filectime( $abs_path );
	}
	/**
	 * 
	 */
	protected function doSort( )
	{
		$helper = 'helper_'.$this->key;
		$fliste = array();
		$dliste = array();
		
		// first, sort the files
		if (!empty( $this->srcFiles ))		
		{
			foreach( $this->srcFiles as $file )
			{
				$tag = $this->$helper( $file );
				$fliste[] = array( 'n' => $file, 't' => $tag );
			}
			$this->doRealSort( $fliste);
		}
		// next, sort the directories
		if (!empty( $this->srcDirs ))
		{
			foreach( $this->srcDirs as $dir )
			{
				$tag = $this->$helper( $dir );
				$dliste[] = array( 'n' => $dir, 't' => $tag );
			}
			$this->doRealSort( $dliste);			
		}
		
		$this->srcFiles = array();
		foreach( $fliste as $index => &$fileElement )
			$this->srcFiles[] = $fileElement['n'];

		$this->srcDirs = array();
		foreach( $dliste as $index => &$fileElement )
			$this->srcDirs[] = $fileElement['n'];
	}
	/**
	 * Verifies if the $key is supported
	 */
	public function checkKey( $key )
	{
		return in_array( $key, self::$keyMap );		
	}		
	/**
	 * 
	 */
	protected function init()
	{
		$fs = $this->sObj;
		
        $ds = $fs->getDirectoryScanner( $this->project );
		$this->root = $fs->getDir( $this->project );
        $this->srcFiles = $ds->getIncludedFiles();
        $this->srcDirs  = $ds->getIncludedDirectories();
	}
	protected function finalize()
	{
		$this->ds = $this->createDirectoryScanner( $this->srcFiles, $this->srcDirs );		
		
		$this->tObj = new FileSetSorterShell( $this->root, $this->tId, $this->ds );
		$this->project->addReference( $this->tId, $this->tObj );
	}	
	protected function createDirectoryScanner( &$srcFiles, &$srcDirs )
	{
		return new DirectoryScanner_FS( $this->root, $srcFiles, $srcDirs );
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * 
	 * 
	 */
	protected function & doRealSort( &$files )
	{
		if (empty( $files ))
			return array();
		if ($this->dir == 'u')
			usort( $files, array( __CLASS__, 'sortHelperUp' ));
		else
			usort( $files, array( __CLASS__, 'sortHelperDown' ));		
	}
	/**
	 * Custom sorting function
	 */
	 protected static function sortHelperUp( &$a, &$b )
	 {
	 	if ($a['t'] == $b['t'])
			return 0;
	 	return ($a['t'] > $b['t']) ? 1:-1;
	 }
	/**
	 * Custom sorting function
	 */
	 protected static function sortHelperDown( &$a, &$b )
	 {
	 	if ($a['t'] == $b['t'])
			return 0;
	 	return ($a['t'] < $b['t']) ? 1:-1;
	 }

} // end class

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

class FileSetSorterShell extends FileSet
{
	var $id = null;
	var $ds = null;
	var $root = null;
	
	public function __construct( &$root, &$id, &$ds )
	{
		$this->root = $root;
		$this->ds = $ds;
		$this->id = $id;
	}
    function getDir(Project $p)
	{
		return $this->root;
	}	
    function getDirectoryScanner(Project $p)
	{
		return $this->ds;
	}	
}

class DirectoryScanner_FS
{
	var $includedFiles = array();
	var $includedDirs = array();
	var $root = null;
	
	public function __construct( &$root, &$srcFiles, &$srcDirs )
	{
		$this->root = $root;
		$this->includedFiles = $srcFiles;
		$this->includedDirs = $srcDirs;
	}
    function getIncludedDirectories() 
	{
		return $this->includedDirs;
    }
    function getIncludedFiles() 
	{
		return $this->includedFiles;		
    }
}

//</source>