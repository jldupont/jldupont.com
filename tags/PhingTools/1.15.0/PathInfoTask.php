<?php
/**
 * PHING task which returns the 'pathinfo' of a file resource
 *
 * 	<taskdef classname='JLD.PhingTools.PathInfoTask' name='pathinfo' />
 *	
 * <pathinfo file='desired-file' 
 *			[prop_dirname   ='property-name-for-dirname-info' ]
 *			[prop_basename  ='property-name-for-basename-info' ]
 *			[prop_extension ='property-name-for-extension-info' ]
 *			[prop_filename  ='property-name-for-filename-info'  ] 
 *			[prop-version   ='property-name-for-version-info']
 * />
 * All output properties are optional. 
 *
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.15.0
 * @Id $Id: PathInfoTask.php 907 2009-05-19 14:59:10Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class PathInfoTask extends JLD_PhingTools_Task
{
	const thisTask = 'PathInfo';
	
	public function setFile( $val ) { $this->__set('file', $val); }	
	public function setDirname( $val ) { $this->dirname = $val; }	
	public function setBasename( $val ) { $this->basename = $val; }	
	public function setExtension( $val ) { $this->extension = $val; }	
	public function setFilename( $val ) { $this->filename = $val; }			
	public function setVersion( $val ) { $this->version = $val; }
	
    /**
     * The main entry point method.
     */
    public function main() 
	{
		if ( $this->file === null )
			throw new BuildException(self::thisTask.': missing file property.');

		$path_parts = @pathinfo( $this->file );
		if (!is_array( $path_parts ))
			throw new BuildException(self::thisTask.': pathinfo error.');		

		// work with basename because 'filename' property
		// does not work well with directory names that contain '.'
		$version = '';
		$basename = $path_parts['basename'];
		$result1 = preg_match("/\-(.*?)\-/",    $basename, $matches1 );
		$result2 = preg_match("/\-([\.0-9]+)/", $basename, $matches2 );
		if (1===$result1)
			$version = $matches1[1];
		if (1===$result2)
			$version = $matches2[1];
			
		$version = trim( $version, " ." );
		if (isset( $this->dirname ))
			$this->project->setProperty($this->dirname, $path_parts['dirname'] );									
		if (isset( $this->basename ))
			$this->project->setProperty($this->basename, $basename );									
		if (isset( $this->extension ))
			$this->project->setProperty($this->extension, $path_parts['extension'] );									
		if (isset( $this->filename ))
			$this->project->setProperty($this->filename, $path_parts['filename'] );
		if (isset( $this->version ))
			$this->project->setProperty($this->version, $version );
			
    }
}// end class
//</source>