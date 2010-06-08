<?php
/**
 * PHING task which returns the current date
 *
 * 	<taskdef classname='JLD.PhingTools.DateTask' name='date' />
 *	<date propertyname="property_name_for_output_result" />
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.13.0
 * @Id $Id: DateTask.php 301 2007-12-12 17:48:24Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class DateTask extends JLD_PhingTools_Task
{
	// Attributes interface
	public function setPropertyName( $val ) { $this->__set('property_name', $val); }	
	
    /**
     * The init method: Do init steps.
     */
    public function init() {}

    /**
     * The main entry point method.
     */
    public function main() 
	{
		$this->project->setProperty( $this->property_name, date( DATE_RFC822 ) );
    }

}
//</source>