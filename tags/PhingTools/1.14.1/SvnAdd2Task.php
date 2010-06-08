<?php
/**
 * PHING task which performs 'svn add' to a specific 'path'.
 * Also check if the said 'path' is already in SVN.
 *
 * 	<taskdef classname='JLD.PhingTools.SvnAdd2Task' name='svnadd2' />
 *	<svnadd2 path="${whatever.path}" />
 *
 * The SVN command must be available in the environment path.
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.14.1
 * @id $Id: SvnAdd2Task.php 905 2009-05-19 14:32:48Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class SvnAdd2Task extends JLD_PhingTools_Task
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
		
		// checks first if the $path is already in SVN
		$result = exec("svn status \"$path\"");		
		if (strpos( $result, '?' ) !== false)
			echo exec("svn add \"$path\"")."\n";
    }
}
//</source>