<?php
/**
 * PHING task which helps updating the 'package.xml' file in a project directory.
 *
 * @author Jean-Lou Dupont
 * @package PearTools
 * @subpackage phing
 * @version @@package-version@@
 * @id $Id: PackageUpdateTask.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/Package.php";

class PackageUpdateTask extends JLD_PhingTools_Task
{
	// Attributes interface
	public function setPropertyPackageFile( $val ) { $this->__set('propertyPackageFile', $val ); }	
		
    /**
     * The init method: Do init steps.
     */
    public function init() {}

    /**
     * The main entry point method.
	 * This method creates the 'channel.xml' file based on the
	 * given parameters.
     */
    public function main() 
	{
		$contents = null;
		$p = new JLD_PearTools_Package( $contents, $this->propertyPackageFile );
		if (!$p->isValid())
			throw new BuildException( 'package file appears invalid' );

		$result = $p->updateFile();
		if (!$result)
			throw new BuildException( 'package file could not be updated' );
    }
}
