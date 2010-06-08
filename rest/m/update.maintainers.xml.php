<?php
/*
	PEAR Channel Update Tool: update 'allmaintainers.xml' file
	Command Line Utility
	@author: Jean-Lou Dupont
	$Id: update.maintainers.xml.php 18 2007-10-01 00:06:06Z JeanLou.Dupont $
	
	Execute from within the SVN directory in REST/m directory
*/
//<source lang=php>

$template_file_name = 'maintainers.xml.tpl';
$template_line = '<h xlink:href="/rest/m/$maintainer">$maintainer</h>'."\n";
$target_file_name	= 'allmaintainers.xml';
$replacement 		= '$maintainer';

// ------------------------------
$cdir = dirname( __FILE__ );
$tplFN = $cdir.'/'.$template_file_name;
$target = $cdir.'/'.$target_file_name;
// ------------------------------

// Get template file contents
$tpl = file_get_contents( $tplFN );

// Now read all the directories which effectively
// constitutes the list of categories
$files = @scandir( $cdir );

// get rid of the . and .. entries
$dirs = null;
foreach( $files as &$file )
{
	if ( ( '.' == $file ) || ( '..' == $file ))
		continue;
	if ( substr($file,0,1) === '.' )
		continue;
		
	// make sure we have a directory
	$path = $cdir."/".$file;
	$info = @filetype( $path );
	if ( 'dir' !== $info )
		continue;
		
	$dirs[] = $file;
}

// go through the director list to produce
// the replacement string for $contents
$contents = null;
if (empty( $dirs ))
{
	echo "Nothing to do!\n";	
	die(1);
}

foreach( $dirs as $dir )
{
	$c = str_replace( $replacement, $dir, $template_line );
	$contents .= $c;
}

#echo $contents; // debug

// now, replace $contents in the template file
$new_contents = str_replace('$contents', $contents, $tpl );

// Finally, write the template in the target file
$path = $cdir.'/'.$target_file_name;
$bytesWritten = file_put_contents( $path, $new_contents );

$ok = (strlen($new_contents) === $bytesWritten);
$msg = $ok ? "Success!":"Failure to write target file!";

echo $msg;
//</source>