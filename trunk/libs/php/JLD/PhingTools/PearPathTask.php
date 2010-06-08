<?php
/**
 * PHING task 
 * Returns the absolute path to the root of PEAR.
 *
 * 	<taskdef classname='JLD.PhingTools.PearPathTask' name='peartask' />
 *	
 *  <peartask property="" />
 *
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version @@package-version@@
 * @Id $Id: PearPathTask.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class PearPathTask extends JLD_PhingTools_Task
{
	const thisName = 'PearPathTask';
	
	// Attributes interface
	public function setProperty( $val ) { $this->__set('property', $val); }	
	
    /**
     * The main entry point method.
     */
    public function main() 
	{
		if ( is_null( $this->property ))
			throw new BuildException( self::thisName.': target property name must be specified.');

		$path = $this->findPearPath();
        $project = $this->getProject();					
		$this->project->setProperty( $this->property, $path);
    }
	/**
	 *
	 */
	protected function findPearPath()
	{
		$pathArray = explode( PATH_SEPARATOR, get_include_path() );
		
		if ( empty( $pathArray ))
			return null;
			
		foreach( $pathArray as &$e )
			if ( preg_match( '/pear/si', $e ) === 1 )
				return $e;
									
		return null;			
	} 

}// end class

//</source>