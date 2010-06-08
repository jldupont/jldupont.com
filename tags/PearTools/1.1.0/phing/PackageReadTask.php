<?php
/**
 * PHING task which reads and parses in a 'package.xml' package file from the project directory.
 *
 *		<taskdef classname='JLD.PearTools.phing.PackageReadTask' name='packageread' />
 *		<!-- T11 -->
 *		<packageread	propertyPackageFile="${package.file}"
 *						propertyPackageName='package.name' 
 *						propertyPackageVersion='package.version' 
 *						propertyPackageStability='package.stability' 
 *						propertyPackageSummary='package.summary' 
 *						propertyPackageDescription='package.description'
 *						propertyPackageDeps='package.dependencies'
 *						propertyPackageChangelog='package.changelog'
 *		/>
 * @author Jean-Lou Dupont
 * @package PearTools
 * @subpackage phing
 * @version 1.1.0
 * @id $Id: PackageReadTask.php 282 2007-11-26 02:44:05Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/Package.php";

class PackageReadTask extends JLD_PhingTools_Task
{
	// Attributes interface
	// Property names that will contain the desired properties read from the package file.
	public function setPropertyPackageName( $val ) { $this->__set('propertyPackageName', $val ); }
	public function setPropertyPackageNameL( $val ) { $this->__set('propertyPackageNameL', $val ); }	
	public function setPropertyPackageVersion( $val ) { $this->__set('propertyPackageVersion', $val ); }	
	public function setPropertyPackageStability( $val ) { $this->__set('propertyPackageStability', $val ); }	
	public function setPropertyPackageSummary( $val ) { $this->__set('propertyPackageSummary', $val ); }		
	public function setPropertyPackageDescription( $val ) { $this->__set('propertyPackageDescription', $val ); }			
	public function setPropertyPackageFile( $val ) { $this->__set('propertyPackageFile', $val ); }	
	public function setPropertyPackageDeps( $val ) { $this->__set('propertyPackageDeps', $val ); }	
	public function setPropertyPackageChangelog( $val ) { $this->__set('propertyPackageChangelog', $val ); }	
		
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

		$this->project->setProperty($this->propertyPackageName, $p->getName() );		
		$this->project->setProperty($this->propertyPackageNameL, strtolower($p->getName() ));
		$this->project->setProperty($this->propertyPackageVersion, $p->getVersion() );				
		$this->project->setProperty($this->propertyPackageStability, $p->getStability() );						
		$this->project->setProperty($this->propertyPackageSummary, $p->getSummary() );						
		$this->project->setProperty($this->propertyPackageDescription, $p->getDescription() );
		$this->project->setProperty($this->propertyPackageDeps, $p->getSerializedDependencies() );		
		$this->project->setProperty($this->propertyPackageChangelog, $p->getChangelog() );				
    }
}
