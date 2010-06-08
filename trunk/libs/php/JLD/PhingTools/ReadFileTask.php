<?php
/**
 * PHING task 
 *
 * 	<taskdef classname='JLD.PhingTools.ReadFileTask' name='readfile' />
 *	
 *  <readfile file="" property="" />
 *
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version @@package-version@@
 * @Id $Id: ReadFileTask.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class ReadFileTask extends JLD_PhingTools_Task
{
	const thisName = 'ReadFileTask';
	
	public function setFile( $val )     { $this->__set('file', $val); }
	public function setProperty( $val ) { $this->__set('prop', $val); }	
	
    /**
     * The main entry point method.
     */
    public function main() 
	{
		$this->validateParameters();
	
		$file = $this->file;
		if (!file_exists( $file ))
			throw new BuildException( self::thisName.': file does not exist.');
			
		$contents = @file_get_contents( $file );
		
		$this->project->setProperty($this->prop, $contents );						
    }
	/**
	 *
	 */
	protected function validateParameters()
	{
		$file = $this->__get('file');
		if ( empty( $file ))
			throw new BuildException( self::thisName.': missing file attribute.');

		$prop = $this->__get('prop');
		if ( empty( $prop )) 
			throw new BuildException( self::thisName.': missing property attribute.');
			
	} 

}// end class

//</source>