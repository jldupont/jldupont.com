<?php
/**
 * PHING task to 'trim' a string
 *
 * 	<taskdef classname='JLD.PhingTools.TrimTask' name='trim' />
 *	<trim	input="input_string"
 *			output="property_name_for_output_result" />
 * 
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.15.0
 * @Id $Id: TrimTask.php 907 2009-05-19 14:59:10Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class TrimTask extends JLD_PhingTools_Task
{
	// Attributes interface
	// Name of category to add + package name
	public function setInput( $val ) { $this->__set('trim_input', $val); }
	public function setOutput( $val ) { $this->__set('trim_output', $val); }	
	
    /**
     * The init method: Do init steps.
     */
    public function init() {}

    /**
     * The main entry point method.
     */
    public function main() 
	{
		$this->project->setProperty($this->trim_output, trim( $this->trim_input ) );
    }

}
//</source>