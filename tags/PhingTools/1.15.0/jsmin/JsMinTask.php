<?php
/**
 * PHING task to 'minify' a Javascript script
 *
 * 	<taskdef classname='JLD.PhingTools.jsmin.JsMinTask' name='jsmin' />
 *	This task extends the system's CopyTask.
 * 
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.15.0
 * @Id $Id: JsMinTask.php 907 2009-05-19 14:59:10Z JeanLou.Dupont $
 */
//<source lang=php> 

include_once 'jsmin.php';
include_once 'phing/tasks/system/CopyTask.php';

class JsMinTask extends CopyTask
{
    protected function validateAttributes() {
    
        if ($this->file === null && count($this->filesets) === 0) {
            throw new BuildException("JsMinTask. Specify at least one source - a file or a fileset.");
        }

        if ($this->destFile !== null && $this->destDir !== null) {
            throw new BuildException("Only one of destfile and destdir may be set.");
        }

        if ($this->destFile === null && $this->destDir === null) {
            throw new BuildException("One of destfile or destdir must be set.");
        }

        if ($this->file !== null && $this->file->exists() && $this->file->isDirectory()) {
            throw new BuildException("Use a fileset to copy directories.");
        }

        if ($this->destFile !== null && count($this->filesets) > 0) {
            throw new BuildException("Cannot concatenate multple files into a single file.");
        }

        if ($this->destFile !== null) {
            $this->destDir = new PhingFile($this->destFile->getParent());
        }
    }

	/**
	 * Copied from 'CopyTask.php'
	 */
    protected function doWork() {
		
		// These "slots" allow filters to retrieve information about the currently-being-process files		
		$fromSlot = $this->getRegisterSlot("currentFromFile");
		$fromBasenameSlot = $this->getRegisterSlot("currentFromFile.basename");	

		$toSlot = $this->getRegisterSlot("currentToFile");
		$toBasenameSlot = $this->getRegisterSlot("currentToFile.basename");	
		
        $mapSize = count($this->fileCopyMap);
        $total = $mapSize;
        if ($mapSize > 0) {
            $this->log("Minifying ".$mapSize." file".(($mapSize) === 1 ? '' : 's')." to ". $this->destDir->getAbsolutePath());
            // walks the map and actually copies the files
            $count=0;
            foreach($this->fileCopyMap as $from => $to) {
                if ($from === $to) {
                    $this->log("Skipping self-copy of " . $from, $this->verbosity);
                    $total--;
                    continue;
                }
                $this->log("From ".$from." to ".$to, $this->verbosity);
                try { // try to copy file
				
					$fromFile = new PhingFile($from);
					$toFile = new PhingFile($to);
					
                    $fromSlot->setValue($fromFile->getPath());
					$fromBasenameSlot->setValue($fromFile->getName());

					$toSlot->setValue($toFile->getPath());
					$toBasenameSlot->setValue($toFile->getName());
					
                    $this->fileUtils->copyFile($fromFile, $toFile, $this->overwrite, $this->preserveLMT, $this->filterChains, $this->getProject());

					// perform ''minification'' once all other things are done on it.
					$this->minify( $toFile );
			
                    $count++;
                } catch (IOException $ioe) {
                    $this->log("Failed to minify " . $from . " to " . $to . ": " . $ioe->getMessage(), Project::MSG_ERR);
                }
            }
        }

        // handle empty dirs if appropriate
        if ($this->includeEmpty) {
            $destdirs = array_values($this->dirCopyMap);
            $count = 0;
            foreach ($destdirs as $destdir) {
                $d = new PhingFile((string) $destdir);
                if (!$d->exists()) {
                    if (!$d->mkdirs()) {
                        $this->log("Unable to create directory " . $d->__toString(), Project::MSG_ERR);
                    } else {
                        $count++;
                    }
                }
            }
            if ($count > 0) {
                $this->log("Copied ".$count." empty director" . ($count == 1 ? "y" : "ies") . " to " . $this->destDir->getAbsolutePath());
            }
        }
    }
	/**
	 * Minification method.
	 */
	protected function minify( &$file )
	{
		$contents = @file_get_contents( $file );
		try
		{
			$code = JSMin::minify( $contents );
		}
		catch( Exception $e )
		{
			throw new BuildException( "Minify error" );	
		}
		$bytes_written = @file_put_contents( $file, $code );
		if ( $bytes_written != strlen( $code ) )
			throw new BuildException( "Write error" );
	}

}
//</source>