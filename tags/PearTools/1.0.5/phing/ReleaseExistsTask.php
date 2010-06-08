<?php
/**
 * @author Jean-Lou Dupont
 * @package PearTools 
 * @subpackage phing
 * @version $Id: ReleaseExistsTask.php 266 2007-11-19 15:49:52Z JeanLou.Dupont $
 *
 * PHING task which determines the package's release status.
 *
 	<taskdef classname='JLD.PearTools.phing.ReleaseExistsTask' name='release_exists' />
	<release_exists	
				channelroot="${channel.root}" 
				channelname="${channel.name}" 
				channeluri="${channel.uri}" 
				channelrest="${channel.rest}" 
				channeltags="${channel.tags}" 
				
				packagename="${package.name}" 
				packageversion="${package.version}" 
				packagestability="${package.stability}" 
				packagereleasestatus="package.releasestatus" />
				
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/ChannelReleases.php";

class ReleaseExistsTask extends JLD_PhingTools_Task
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
	public function setPackageReleaseStatus( $val ) { $this->__set('package_release_status', $val ); }				
	
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
		
		$result = $cr->releaseExists( $this->package_version );
		$this->project->setProperty( $this->package_release_status,	$result );		
    }

}
//</source>