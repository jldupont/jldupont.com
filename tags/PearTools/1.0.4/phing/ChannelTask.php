<?php
/**
 * @author Jean-Lou Dupont
 * @package PearTools 
 * @subpackage phing
 * @version $Id: ChannelTask.php 265 2007-11-19 15:23:30Z JeanLou.Dupont $
 *
 * PHING task which reads in the current channel's parameters and makes
 *  those available in the current PHING project.
 *  Other tasks will use this object to manage the REST interface.
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/PearTools/Channel.php";

class ChannelTask extends JLD_PhingTools_Task
{
	// Attributes interface
	public function setPath( $val ) { $this->__set('path', $val ); }	
		
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
		$c = JLD_PearTools_Channel::singleton();
		$result = $c->init( $this->path );
		if (!$result)
			throw new BuildException( 'channel object could not be created because channel.xml was not found' );
		
		$this->project->setProperty( 'channel.uri',		$c->getURI() );
		$this->project->setProperty( 'channel.name',	$c->getName() );		
		$this->project->setProperty( 'channel.alias',	$c->getAlias() );				
		$this->project->setProperty( 'channel.tags',	$c->getTAGSPath() );				
		$this->project->setProperty( 'channel.rest',	$c->getRESTPath() );						
    }
}
//</source>