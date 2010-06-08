<?php
/**
 * @author Jean-Lou Dupont
 * @package PearTools
 * @subpackage phing
 * @version $Id: ChannelFindTask.php 266 2007-11-19 15:49:52Z JeanLou.Dupont $
 *
 * This PHING task finds the root path of the channel.
 * This is accomplishes by 'moving up' the directory hierarchy
 * until the 'channel.xml' file is found or not.
 *
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/Channel.php";

class ChannelFindTask extends JLD_PhingTools_Task
{
	public function setRootPath( $val ) { $this->__set('rootpath', $val ); }	
	public function setStartPath( $val ) { $this->__set('startpath', $val ); }	
		
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
		$current = $this->__get('startpath');
		do
		{
			if (!is_dir( $current))
				throw new BuildException( 'path provided is not a directory or channel.xml can not be found' );
			if (is_file( $current.'/channel.xml' ))
				return ($this->project->setProperty( $this->rootpath, $current) );
			
			$current = dirname( $current );
			
		}while( true );
    }
}
