<?php
/**
 * PHING task which helps add/update the /p PEAR REST structure.
 *
 *	<echo>Adding package's directory and info.xml file in REST packages</echo>
 *	<taskdef classname='JLD.PearTools.phing.ChannelPackagesTask' name='packages' />
 *	<packages	channelroot="${channel.root}" 
 *				channelname="${channel.name}" 
 *				channeluri="${channel.uri}" 
 *				channelrest="${channel.rest}" 
 *				channeltags="${channel.tags}"
 *				catname="${package.category}" 
 *				packagename="${package.name}" />
 * @author Jean-Lou Dupont
 * @package PearTools
 * @subpackage phing
 * @version 1.0.11
 * @id $Id: ChannelPackagesTask.php 278 2007-11-24 00:48:12Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/ChannelPackages.php";

class ChannelPackagesTask extends JLD_PhingTools_Task
{
	// Attributes interface
	// Name of category to add + package name
	public function setChannelRoot( $val ) { $this->__set('channel_root', $val); }
	public function setChannelUri( $val ) { $this->__set('channel_uri', $val); }
	public function setChannelName( $val ) { $this->__set('channel_name', $val); }	
	public function setChannelRest( $val ) { $this->__set('base_rest', $val); }	
	public function setChannelTags( $val ) { $this->__set('base_tags', $val); }	
	
	public function setCategoryName( $val ) { $this->__set('category_name', $val ); }	
	public function setPackageName( $val ) { $this->__set('package_name', $val ); }		
	
    /**
     * The init method: Do init steps.
     */
    public function init() {}

    /**
     * The main entry point method.
     */
    public function main() 
	{
		$cp = JLD_PearTools_ChannelPackages::singleton();
		$cp->initVars( $this->vars );
		
		// 1- add the package's 'info.xml' file
		$result = $cp->createPackageInfoFile( );
		if (!$result)
			throw new BuildException( 'could not create the file "info.xml" for the package in the REST structure' );

		// 2- Updates/creates the file 'packages.xml'
		$result = $cp->createPackagesFile( );
		if (!$result)
			throw new BuildException( 'could not create the file "packages.xml" in the REST structure' );
    }

}
//</source>