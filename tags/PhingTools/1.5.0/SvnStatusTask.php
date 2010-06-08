<?php
/**
 * PHING task which performs 'svn status' on a specific 'resource'
 *
 * 	<taskdef classname='JLD.PhingTools.SvnStatus' name='svn_status' />
 *	<svn_status path="whatever.resource" 
 *           	result="whatever.property.name.to.hold.result" />
 *
 * The SVN command must be available in the environment path.
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.5.0
 * @id $Id: SvnStatusTask.php 285 2007-11-27 01:10:05Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class SvnStatusTask extends JLD_PhingTools_Task
{
	// Attributes interface
	// Name of category to add + package name
	public function setPath( $val ) { $this->__set('path', $val); }
	public function setResult( $val ) { $this->__set('result', $val); }		
	
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
		
		$result = exec("svn status $path");
		$this->project->setProperty( $this->result, $result );
    }
}
//</source>