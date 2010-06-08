<?php
/**
 * PHING task which renames files and directories
 *
 * 	<taskdef classname='JLD.PhingTools.RenameTask' name='rename' />
 *	
 * <rename  src='desired-path-to-rename' dest="desired-new-path" replace=["true"|"false"] /> 
 *
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version @@package-version@@
 * @Id $Id: RenameTask.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class RenameTask extends JLD_PhingTools_Task
{
	const thisTask = 'Rename';
	
	protected $replace = false;
	
	public function setSrc( $val )     { $this->__set('src',  $val); }
	public function setDest( $val )    { $this->__set('dest', $val); }
	public function setReplace( $val ) { $this->replace = ($val==="true");   }
    /**
     * The main entry point method.
     */
    public function main() 
	{
		if ( $this->src === null )
			throw new BuildException(self::thisTask.": missing 'src' property.");
			
		if ( $this->dest === null )
			throw new BuildException(self::thisTask.": missing 'dest' property.");
			
		$isdir  = is_dir( $this->dest );
		$isfile = is_file($this->dest );
		 
		if (( $isdir || $isfile) && $this->replace) {
			if ($isdir) {
				@rmdir( $this->dest );
				$this->log( self::thisTask.": removed directory [".$this->dest."]" );
			}
				
			if ($isfile) {
				@unlink( $this->dest );
				$this->log( self::thisTask.": removed file [".$this->dest."]" );
			}				
		} 

		if (is_dir($this->dest))
			throw new BuildException(self::thisTask.': cannot rename: destination is an existing directory.');
			
		if (is_file($this->dest))
			throw new BuildException(self::thisTask.': cannot rename: destination is an existing file.');
			
		$this->log( "Renaming [".$this->src."] to [".$this->dest."]" );
		
		if (!rename( $this->src, $this->dest ))
			throw new BuildException(self::thisTask.': cannot rename');
    }
}// end class
//</source>
