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
 * />
 * All output properties are optional. 
 *
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.11.0
 * @Id $Id: PathInfoTask.php 316 2008-01-21 03:23:39Z JeanLou.Dupont $
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

		if (isset( $this->dirname ))
			$this->project->setProperty($this->dirname, $path_parts['dirname'] );									
		if (isset( $this->basename ))
			$this->project->setProperty($this->basename, $path_parts['basename'] );									
		if (isset( $this->extension ))
			$this->project->setProperty($this->extension, $path_parts['extension'] );									
		if (isset( $this->filename ))
			$this->project->setProperty($this->filename, $path_parts['filename'] );
    }
}// end class
//</source>