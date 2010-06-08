<?php
/**
 * Unit tests
 *
 * @author Jean-Lou Dupont
 * @package Cache
 * @version $Id: UnitTest.php 296 2007-12-01 19:48:08Z JeanLou.Dupont $
 */
require_once 'PHPUnit/Framework.php';

#ini_set("apc.enabled", "On");
#ini_set("apc.enable_cli", "1"); // this must be included in the command line php.ini file!

require dirname( dirname(__FILE__) ).'/Cache.php';

class UnitTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
		$o = JLD_Cache_Manager::singleton();
		
        $this->assertEquals(true, is_object( $o ) );
    }
    public function testGetCache()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		echo 'cache class: '.get_class( $c )."\n";
		
        $this->assertEquals(true, is_object( $c ) );
    }
    public function testAvailableCache()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getAvailableCaches();
		
		#var_dump( $c );
		
        $this->assertEquals(true, is_array( $c ) );
    }
    public function testIsFake()
    {
		$o = JLD_Cache_Manager::singleton();
		$r = $o->isFake();
		
        $this->assertEquals( false, $r );
    }
    public function testGetCacheInfo()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		$i = $c->getInfo();
		
		#var_dump( $i );
		
        $this->assertEquals( true, is_array( $i ) );
    }
	
    public function testGetInvalidKeyFromCache()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		$r = $c->get( 'invalidkey' );
		
        $this->assertEquals( false, $r );
    }
    public function testWriteValidToCache1()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		$r = $c->set( 'key1', 'value1' );
		
        $this->assertEquals( true, $r );
    }
    public function testWriteValidToCache2()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		$c->set( 'key1', 'value1' );
		$r = $c->get( 'key1' );	
		
        $this->assertEquals( 'value1', $r );
    }
    public function testReplace()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		$c->replace( 'key1', 'value2' );
		
		$r = $c->get( 'key1' );		
		
        $this->assertEquals( 'value2', $r );
    }
    public function testAddToExistingKey()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		// testing adding an already existing key.
		$r = $c->add( 'key1', 'value3' );
		
        $this->assertEquals( false, $r );
    }
    public function testAddNonExistingKey()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		$c->delete( 'key3' );
		
		// try adding a non-existing key
		$r = $c->add( 'key3', 'value2' );
		
        $this->assertEquals( true, $r );
    }
    public function testDeleteNonExistingKey()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		// try deleting a non-existing key
		/*$r =*/ $c->delete( 'key4' );
		$r = $c->get( 'key4' );
		
        $this->assertEquals( false, $r );
    }
    public function testDeleteExistingKey()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();
		
		$c->delete( 'key5' );		
		$c->add( 'key5', 'value5' );
		$c->delete( 'key5' );
		$r = $c->get( 'key5' );
		
        $this->assertEquals( false, $r );
    }

/*
    public function testGetCache2()
    {
		$o = JLD_Cache_Manager::singleton();
		$c = $o->getCache();

		$c->clearCache( );
				
		$r = $c->get( 'key1' );
		
        $this->assertEquals( false, $r );
    }
*/
	
}
