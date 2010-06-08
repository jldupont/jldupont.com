<?php
/**
 * @author Jean-Lou Dupont
 * @package JLD
 * @subpackage AutoLoader
 * @version $Id: AutoLoader.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

require 'JLD/Object/Object.php';
require 'JLD/Cache/Cache.php';
require 'JLD/Directory/Directory.php';

class JLD_AutoLoader extends JLD_Object
{
	const thisCacheKeyVar = 'liste';
	const thisVersion = '$Id: AutoLoader.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $';
	
	static $expiry = 86400; //1day.
	static $cList = array();
	
	public function __construct() 
	{
		spl_autoload_register( array( __CLASS__, 'load' ) );		
		self::initFromCache();
	}
	public static function singleton()
	{
		return parent::singleton( __CLASS__, self::thisVersion );	
	}
	public static function getList()
	{
		return self::$cList;	
	}
	public static function add( $classe, $filepath )
	{
		self::$cList[ $classe ] = $filepath;
	}
	protected static function initFromCache()
	{
		$fake = JLD_Cache_Manager::isFake();
		
		// if the cache is fake, then go the the lengthy process...
		if ($fake === true)
			return self::initFromFileSystem();
			
		$cache = JLD_Cache_Manager::getCache();
		
		$key = self::getCacheKey( self::thisCacheKeyVar );

		self::$cList = $cache->get( $key );
		
		// this handles first time initialization OR
		// cache entry expiration
		if (self::$cList === false)
		{
			self::initFromFileSystem();
			self::updateCache();
		}
	}
	/**
	 * Goes through the 'JLD/' folder hierarchy
	 */
	public static function initFromFileSystem()
	{
		$pear = JLD_Directory::getPearIncludePath();
		if (empty( $pear ))
			die(__CLASS__.': pear include path not found.');
		
		$dirs = JLD_Directory::getDirectoryInformation( $pear.'/JLD', $pear, true, true );

		self::$cList = self::getFiles( $pear, $dirs );		
	}
	protected static function getFiles( &$base, &$dirs )
	{
		if (empty( $dirs ))
			return null;
			
		$files = array();
		foreach( $dirs as $dir )
		{
			$file = $base.'/'.$dir.'.php';			
			$r = @file_exists( $file );
			if (!$r)
				continue;
			$files[$dir] = $base.'/'.$dir.'.php';
		}

		return $files;
	}
	protected static function updateCache()
	{
		$cache = JLD_Cache_Manager::getCache();
		$key = self::getCacheKey( self::thisCacheKeyVar );
		$cache->set( $key, self::$cList, self::$expiry );
	}
	public static function load( $classe ) 
	{
		if (!isset( self::$cList[ $classe ]))
			return false;
		
		@require self::$cList[ $classe ];
		
		return class_exists( $classe );
	}
} // end class

//</source>