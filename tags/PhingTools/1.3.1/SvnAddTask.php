<?php
/**
 * PHING task which performs 'svn add' to a specific 'path'
 *
 * 	<taskdef classname='JLD.PhingTools.SvnAddTask' name='svn_add' />
 *	<svn_add path="${whatever.path}" />
 *
 * The SVN command must be available in the environment path.
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.3.1
 * @id $Id: SvnAddTask.php 279 2007-11-24 05:11:47Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class SvnAddTask extends JLD_PhingTools_Task
{
	// Attributes interface
	// Name of category to add + package name
	public function setPath( $val ) { $this->__set('path', $val); }
	
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
		echo exec("svn add $path")."\n";
    }
}
//</source>