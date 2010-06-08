<?php
/**
 * PHING task which helps manage the /r PEAR REST structure.
 *
 * 	<taskdef classname='JLD.PearTools.phing.ChannelReleasesTask' name='releases' />
 *	<releases	channelroot="${channel.root}" 
 *				channelname="${channel.name}" 
 *				channeluri="${channel.uri}" 
 *				channelrest="${channel.rest}" 
 *				channeltags="${channel.tags}" 
 *				packagename="${package.name}" 
 *				packageversion="${package.version}" 
 *				packagestability="${package.stability}" 
 *				packagedependencies="${package.dependencies}" 
 *	/>
 * @author Jean-Lou Dupont
 * @package PearTools
 * @subpackage phing
 * @version 1.1.0
 * @id $Id: ChannelReleasesTask.php 282 2007-11-26 02:44:05Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/Channel.php";
require_once "JLD/PearTools/ChannelReleases.php";

class ChannelReleasesTask extends JLD_PhingTools_Task
{
	// Attributes interface
	// Name of category to add + package name
	public function setChannelRoot( $val ) { $this->__set('channel_root', $val); }
	public function setChannelUri( $val ) { $this->__set('channel_uri', $val); }
	public function setChannelName( $val ) { $this->__set('channel_name', $val); }	
	public function setChannelRest( $val ) { $this->__set('base_rest', $val); }	
	public function setChannelTags( $val ) { $this->__set('base_tags', $val); }	
	public function setPackageName( $val ) { $this->__set('package_name', $val ); }		
	public function setPackageVersion( $val ) { $this->__set('package_version', $val ); }			
	public function setPackageStability( $val ) { $this->__set('package_stability', $val ); }
	public function setPackageDependencies( $val ) { $this->__set('package_dependencies', $val ); }				
	
    /**
     * The init method: Do init steps.
     */
    public function init() {}

    /**
     * The main entry point method.
     */
    public function main() 
	{
		$cr = JLD_PearTools_ChannelReleases::singleton();

		// make this object's variables
		// available to the ChannelReleases object.
		$cr->initVars( $this->vars );
		
		$result = $cr->createVersionFile( );
		if (!$result)
			throw new BuildException( 'version.xml could not be created' );

		$result = $cr->createDepsFile( );
		if (!$result)
			throw new BuildException( 'deps.$version.txt could not be created' );

		$result = $cr->createAllReleasesFile( );
		if (!$result)
			throw new BuildException( '"allreleases.xml" file could not be created' );
    }

}
//</source>