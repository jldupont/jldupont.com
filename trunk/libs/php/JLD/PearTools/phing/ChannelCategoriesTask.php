<?php
/**
 *
 * PHING task to add/update the categories /c PEAR REST structure.
 *
 * Example: 
 *			<taskdef classname='JLD.PearTools.phing.CategoriesTask' name='categories' />
 *			<categories	channelroot="${channel.root}" 
 *						catname="${package.category}" 
 *						packagename="${package.name}"
  *						packagesummary="${package.summary}"
 *						packagedescription="${package.description}"  
 *						packagereleaseversion="${package.version}" 
 *						packagereleasestability="${package.stability}" />
 * 
 * @author Jean-Lou Dupont
 * @package PearTools
 * @subpackage phing
 * @version @@package-version@@
 * @id $Id: ChannelCategoriesTask.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/Channel.php";
require_once "JLD/PearTools/ChannelCategories.php";

class ChannelCategoriesTask extends JLD_PhingTools_Task
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
	public function setPackageSummary( $val ) { $this->__set('package_summary', $val ); }	
	public function setPackageDescription( $val ) { $this->__set('package_description', $val ); }		
	public function setPackageReleaseVersion( $val ) { $this->__set('package_version', $val ); }			
	public function setPackageReleaseStability( $val ) { $this->__set('package_stability', $val ); }

					
		
    /**
     * The init method: Do init steps.
     */
    public function init() {}

    /**
     * The main entry point method.
     */
    public function main() 
	{
		$cs = JLD_PearTools_ChannelCategories::singleton();
		
		$cs->initVars( $this->vars );
		$cs->readAll();
		
		// 2- add the category to the 'categories.xml' file
		try
		{
			$result = $cs->updateCategories();
		}
		catch (Exception $e)
		{
			throw new BuildException( $e->getMessage() );
		}
		// 3- update 'info.xml'
		$result = $cs->updateCategoryInfo( $this->__get('category_name') );
		if (!$result)
			throw new BuildException( 'could not update "info.xml" file in the REST structure' );

		// 4- update 'packagesinfo.xml'
		$result = $cs->updatePackagesInfo(	);
		if (!$result)
			throw new BuildException( 'could not update "packagesinfo.xml" file in the REST structure' );
		
    }
}
//</source>