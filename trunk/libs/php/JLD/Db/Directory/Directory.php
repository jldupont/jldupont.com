<?php
/**
 * @author Jean-Lou Dupont
 * @package JLD
 * @subpackage Directory
 * @version $Id: Directory.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

/**
 * This is a static class.
 */
class JLD_Directory
{

	/**
	 *
	 *
	 * Format of the returned information:
	 *
		e.g.
		array (
				0 =>
				array (
				'name' => '.',
				'type' => 'dir',
				'mtime' => 1186483435,
				),
				1 =>
				array (
				'name' => '..',
				'type' => 'dir',
				'mtime' => false,			# NOTE HERE
				),
				2 =>
				array (
				'name' => '.htaccess',
				'type' => 'file',
				'mtime' => 1181832196,
				),
				3 =>
				array (
				'name' => 'AdminSettings.php',
				'type' => 'file',
				'mtime' => 1178738087,
				),
			...
	 */
	public static function getDirectoryInformation( &$dir, &$base, 
													$filterDots = false, 
													$justDirs = false
													)
	{
		$files = @scandir( $dir );
			
		$thisDir = self::getRelativePath( $dir, $base );
		
		if (empty( $files ))
			return null;
		
		foreach( $files as $index => &$file )
		{
			$info = @filetype( $dir.'/'.$file );

			// filter all entries beginning with a '.'
			// This is useful for getting rid of '.', '..' and '.*' entries
			// such as '.svn'.
			if ( ('.' == $file[0]) && $filterDots )
			{
				unset( $files[ $index ] );
				continue;
			}
			// just keep the directories if we are asked to.
			if ( ($info !== 'dir' ) && $justDirs)
			{
				unset( $files[ $index ] );
				continue;
			}
			
			if ( '.' == $file )	$info = 'dir';
			if ( '..' == $file )$info = 'dir';

			$filename = $file;
			$mtime = @filemtime( $dir.'/'.$file );
		
			if ( $file != '.' && $file != '..' && $thisDir != '/' )
				$filename = $thisDir.'/'.$filename;

			$file = array( 'name' => $filename, 'type' => $info , 'mtime' => $mtime );
		}
	
		return $files;
	}
	/**
		Returns the filename (directory name really) correspondig to '..'
	 */
	public static function getDotDotFile( &$dir, &$base )
	{
		$d = str_replace( "\\", '/', $dir );

		$pathInfo = pathinfo( $d );
		
		$p = $pathInfo['dirname'];		

		// now remove the base.
		$s = self::getRelativePath( $p, $base );

		// make sure we haven't reached the root.
		if (empty($s))
			return '/';
			
		return $s;
	}

	public static function getRelativePath( &$dir, &$base )
	{
		$d = str_replace( "\\", '/', $dir );

		return substr( $d, strlen($base)+1 );
	}

	public static function getDirectoryTimestamp( &$dir )
	{
		return @filemtime( $dir );	
	}

	/**
	 */
	public static function getIncludePath( $fragment )
	{
		$liste = get_include_path();
		$a = explode( PATH_SEPARATOR, $liste );
		
		foreach( $a as $e )
			if (strpos( strtolower( $e ), $fragment ) !== false)
				return $e;
				
		return null;
	}

	/**
	 * There can only be one 'entry' in the include path
	 * with the keyword 'pear' (or 'PEAR') in it.
	 *
	 * This shouldn't be a problem with most standard installs.
	 */
	public static function getPearIncludePath()
	{
		return self::getIncludePath( 'pear' );
	}

	/**
	 * There can only be one 'entry' in the include path
	 * with the keyword 'phing' (or 'phing') in it.
	 *
	 * This shouldn't be a problem with most standard installs.
	 */
	public static function getPhingIncludePath()
	{
		return self::getIncludePath( 'phing' );		
	}

	public static function getDirectoryInformationRecursive( $dir, &$base, 
													$filterDots = false, 
													$justDirs = false )
	{
		$all_files = array();
		
		$files = self::getDirectoryInformation( $dir, $base, $filterDots, $justDirs );
		if (!empty( $files ))
			foreach ( $files as $file )
			{
				if ( (($file['type'] == 'dir') && $justDirs) || !$justDirs )
					$all_files[] = $file;
					
				if ( $file['type'] == 'dir' )
				{
					$other_files = self::getDirectoryInformationRecursive( $base.'/'.$file['name'], $base, $filterDots, $justDirs );
					if (!empty( $other_files))
						$all_files = array_merge( $all_files, $other_files );
				}
			}	
		return $all_files;
	}

} // end class
//</source> 