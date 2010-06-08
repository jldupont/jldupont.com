<?php
/**
 * PHING task to perform an 'svn add' to a batch of files
 *
 * 	<taskdef classname='JLD.PhingTools.SvnAddBatch' name='svnaddbatch' />
 *	This task is built upon the system's ReflexiveTask *but* without filterchain capabilities.
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.11.0
 * @Id $Id: SvnAddBatchTask.php 320 2008-01-23 03:04:12Z JeanLou.Dupont $
 */
//<source lang=php> 
require_once 'phing/Task.php';

class SvnAddBatchTask extends Task
{
	const thisTask = 'SvnAddBatch';

    /** Single file to process. */
    private $file;
    
    /** Any filesets that should be processed. */
    private $filesets = array();
    
    /** Alias for setFrom() */
    function setFile(PhingFile $f) {
        $this->file = $f;
    }
    
    /** Nested creator, adds a set of files (nested fileset attribute). */
    function createFileSet() {
        $num = array_push($this->filesets, new FileSet());
        return $this->filesets[$num-1];
    }

    /** Append the file(s). */
    function main() {
            
        if ($this->file === null && empty($this->filesets)) {
            throw new BuildException("You must specify a file or fileset(s) for the <svnaddbatch> task.");
        }
        
        // compile a list of all files to modify, both file attrib and fileset elements
        // can be used.
        
        $files = array();
        
        if ($this->file !== null) {
            $files[] = $this->file;
        }
        
        if (!empty($this->filesets)) {
            $filenames = array();
            foreach($this->filesets as $fs) {
                try {
                    $ds = $fs->getDirectoryScanner($this->project);
                    $filenames = $ds->getIncludedFiles(); // get included filenames
                    $dir = $fs->getDir($this->project);
                    foreach ($filenames as $fname) {
                        $files[] = new PhingFile($dir, $fname);
                    }
                } catch (BuildException $be) {
                    $this->log($be->getMessage(), Project::MSG_WARN);
                }
            }                        
        }
        
        $this->log("Applying <svnaddbatch> processing to " . count($files) . " files.");

        foreach($files as $file) 
		{
			// set the register slots
			
			$base = $file->getPath();
			$name = $file->getName();
			$path = $base.'/'.$name;
			
			// checks first if the $path is already in SVN
			$result = exec("svn status \"$path\"");		
			if (strpos( $result, '?' ) !== false)
				echo exec("svn add \"$path\"")."\n";
        }
                                
    }   
}
//</source>