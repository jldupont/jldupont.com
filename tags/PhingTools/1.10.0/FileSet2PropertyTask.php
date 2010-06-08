<?php
/**
 * PHING task which acts on a FileSet object to:
 *  1) [optionally] Apply a regex pattern to each file-name
 *  2) Build a coma separated list out of the filename list of the FileSet
 *
 * 	<taskdef classname='JLD.PhingTools.FileSet2PropertyTask' name='fs2prop' />
 *	
 * <fs2prop fsid='reference-to-fileset-object' prop='name-of-property' >
 *  <regex pattern='regex-pattern' replace='replacement-string' />
 *  ...
 * </fs2prop>
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.10.0
 * @Id $Id: FileSet2PropertyTask.php 307 2007-12-18 02:25:36Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class FileSet2PropertyTask extends JLD_PhingTools_Task
{
	var $regexes = array();
	var $liste = array();
	var $processed_liste = null;
	var $fs = null;
	
	public function setFsId( $val ) { $this->__set('fsid', $val); }	
	public function setProp( $val ) { $this->__set('prop', $val); }
	
	/**
	 * Creates a < regex> tag
	 */
	public function createRegex()
	{
        $num = array_push( $this->regexes, new Regex());
        return $this->regexes[$num-1];
	}
    /**
     * The main entry point method.
     */
    public function main() 
	{
		if ( $this->fsid === null )
			throw new BuildException('FileSet2Property: missing source fileset id.');

		$refs = $this->project->getReferences();
		if (isset( $refs[ $this->fsid ] ))
			$this->fs = $refs[ $this->fsid ];

		if ( !is_object( $this->fs ))
			throw new BuildException('FileSet2Property: fileset id given does not point to a valid object.');

		if ( !$this->fs instanceof FileSet )
			throw new BuildException('FileSet2Property: fileset id given does not point a a valid FileSet object.');		
			
		$prop = $this->__get( 'prop' );
		if ( empty( $prop ) )
			throw new BuildException('FileSet2Property: requires a valid "prop" attribute.');				

		// builds the initial list out of the FileSet object
		$this->toList();

		// applies all regex expressions in turn to all elements of the list
		$this->applyRegexes();

		// converts the list to a string
		$this->toProp();

		// sets the requested property
		$this->project->setProperty($this->prop, $this->processed_liste );						
    }
	/**
	 * Applies the regexes to all elements in the list
	 */
	protected function applyRegexes()
	{
		if (empty( $this->regexes ))
			return;
			
		if (empty( $this->liste ))
			return;
		
		foreach( $this->liste as &$e )
			foreach( $this->regexes as &$regex )
				$regex->apply( $e );
	}
	/**
	 * Iterates through the FileSet object and an array
	 */
	protected function toList()
	{
        $project = $this->getProject();
		$fs = $this->fs;				

        $ds = $fs->getDirectoryScanner($project);
        $fromDir  = $fs->getDir($project);
        $srcFiles = $ds->getIncludedFiles();
        $srcDirs  = $ds->getIncludedDirectories();
		
		$this->liste = array_merge( $srcFiles, $srcDirs );
	}
	/**
	 * Makes a coma separated list out of the file array
	 */
	protected function toProp()
	{
		// clear empty entries
		foreach( $this->liste as $index =>&$e )
			if (empty( $e ))
				unset( $this->liste[ $index] );
			
		$this->processed_liste = implode( ',' , $this->liste );
	}	 
}// end class

/**
 * Helper class
 */
class Regex
{
	var $pattern = null;
	
	public function setPattern( $val )
	{
		$this->pattern = $val;	
	}
	public function setReplace( $val )
	{
		$this->replace = $val;
	}
	public function apply( &$e )
	{
		$e = preg_replace( $this->pattern, $this->replace, $e );
	}	
}// end class
//</source>