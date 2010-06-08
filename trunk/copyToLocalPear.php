<?php
/*
	Copies the source directory to the local PEAR directory.
	
	@author: Jean-Lou Dupont
	
	Aptana:  "${project_loc}/$DIR/copyToLocalPear.php"  "${resource_loc}"
	
	- Execute the above Aptana/Eclipse 'external tools' command using PHP.
	- Select target resource
*/

// PEAR dependancy
require 'JLD/Directory/Directory.php';

if (!isset( $argv[1]))
{
	echo "Requires a directory path as input!\n";	
	die(0);
}

$sdir = $argv[1];

if (!is_dir( $sdir ))
{
	echo "Requires a valid directory path as input!\n";	
	die(0);
}

// we need the parent directory path too in order to properly
// place the source files in the local PEAR directory.
$pdir = dirname( $sdir );
$pearDir = JLD_Directory::getPearIncludePath();

echo 'Source directory: '.$sdir."\n";
echo 'Parent directory: '.$pdir."\n";
echo 'PEAR directory: '.$pearDir."\n";

$files = JLD_Directory::getDirectoryInformationRecursive( &$sdir, &$pdir, true );

#var_dump( $files );
#die;

$bpname = basename($pdir);

// push target directory on the stack top;
$bname = basename( $sdir );
array_unshift( $files, array( 'name' => $bname, 'type' => 'dir'));

foreach( $files as $file )
{
	$dir = $pearDir.'/'.$bpname.'/'.$file['name'];
	// create target directory if necessary.
	if ( ($file['type'] == 'dir'))
	{
		if (!is_dir( $dir ) )
		{
			echo 'Creating directory: '.$dir." ... ";
			$result = mkdir( $dir );
			echo ($result) ? 'success':'failure';
			echo "\n";
			if (!$result)
				die(0);
		}	
		continue;
	}
	// not a directory, then it is a file.
	$sfile = $pdir.'/'.$file['name'];
	$dfile = $pearDir.'/'.$bpname.'/'.$file['name'];
	echo 'Copying file: '.$file['name']." ... ";
	$result = @copy( $sfile, $dfile );
		echo ($result) ? 'success':'failure';
		echo "\n";
		if (!$result)
			die(0);
}

