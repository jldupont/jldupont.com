<?php
/**
 * PHING task 
 * Finds the specified directory by 'going up' the directory hierarchy.
 * The 'source' directory to find is actually specified as a 'fragment' 
 * i.e. not the full path (or else, what's the point?).
 * E.g. to find the directory fragment 'trunk', use:
 * <finddirtask dir="${project.basedir}"
 *				source="trunk"
 *				result="path.trunk" />
 *
 * 	<taskdef classname='JLD.PhingTools.FindDirTask' name='finddirtask' />
 *	
 *  <finddirtask	dir="directory-where-to-start-the-search"
 *					source="directory-to-find" 
 * 					result="absolute-path-if-found" />
 *
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.14.0
 * @Id $Id: FindDirTask.php 335 2008-02-11 18:09:08Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";
require_once "JLD/Validate/Validate.php";

class FindDirTask extends JLD_PhingTools_Task
{
	const thisName = 'FindDirTask';

/**
 * m: mandatory parameter
 * s: sanitization required
 * l: which parameters to pick from list
 * d: default value
*/
	
	static $params = array(
		'dir' 	 => array( 'm' => true, 'l' => true, 't' => 'dir' ),	
		'source' => array( 'm' => true, 'l' => true, 't' => 'string' ),
		'result' => array( 'm' => true, 'l' => true, 't' => 'string' ),		
	);
	
	// Attributes interface
	public function setDir( $val )		{ $this->__set('dir', $val); }		
	public function setSource( $val )	{ $this->__set('source', $val); }	
	public function setResult( $val )	{ $this->__set('result', $val); }	
		
    /**
     * The main entry point method.
     */
    public function main() 
	{
		$parameters = JLD_Validate::initFromObject( $this, self::$params );
		$result = JLD_Validate::doListSanitization( $parameters, self::$params );
		
		if ( !is_array( $result ))
			throw new BuildException( self::thisName.': missing parameter '.$result );
			
		$key = JLD_Validate::doSanitization( $parameters, self::$params );

		if ( $key !== true)
			throw new BuildException( 
				self::thisName.': parameter '.$key.' of wrong type. Expecting "'.self::$params[$key]['t'].'"' );		

		$path = $this->findDir( $this->dir, $this->source );

        $project = $this->getProject();					
		$this->project->setProperty( $this->result, $path);
    }
	/**
	 *
	 */
	protected function findDir( $dir, &$dir_name )
	{
		$result = null;
		do {
			if ( !is_dir( $dir ) )
				break;
				
			$path = $dir.'/'.$dir_name;
			if ( is_dir( $path ))
			{
				$result = $path;
				break;
			} 
			
			// go 1 level up
			$newdir = realpath( $dir.'/../' );
			// did we reach the top?
			if ( $newdir == $dir )
				break;
			$dir = $newdir;
			
		} while(true);
		
		return $result;
	} 

}// end class

//</source>