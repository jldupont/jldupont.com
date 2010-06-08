<?php
/**
 * PHING task which performs 'svn add' to a specific 'path'
 *
 * 	<taskdef classname='JLD.PhingTools.SvnPropsetTask' name='svn_propset' />
 *	<svn_propset path="${whatever.path}" 
 *           	propname="whatever.property.name" 
 *           	propval="whatever.property.value" />
 *
 * The SVN command must be available in the environment path.
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.3.0
 * @id $Id: SvnPropsetTask.php 279 2007-11-24 05:11:47Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class SvnPropsetTask extends JLD_PhingTools_Task
{
	// Attributes interface
	// Name of category to add + package name
	public function setPath( $val ) { $this->__set('path', $val); }
	public function setPropName( $val ) { $this->__set('propname', $val); }
	public function setPropVal( $val ) { $this->__set('propval', $val); }		
	
    /**
     * The init method: Do init steps.
     */
    public function init() {}

    /**
     * The main entry point method.
     */
    public function main() 
	{
		$path = $this->path; // shortcut
		$propname = $this->propname;
		$propval = $this->propval;
		
		echo exec("svn propset $propname $propval $path");
    }
}
//</source>