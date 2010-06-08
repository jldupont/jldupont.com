<?php
/**
 *  JLD_Delicious package unit tests
 *
 *  @author Jean-Lou Dupont
 *  @version 1.0.0
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__)."/../DeliciousPosts.php";
require_once dirname(__FILE__)."/../DeliciousPost.php";

class UnitTest extends PHPUnit_Framework_TestCase
{
	static $feed = "jldupont/my-diagrams";
	
	/*
	 * GENERIC setup and teardown
	 */		
    protected function setUp()
    {
    }
 	protected function tearDown()
	{
	}
	public function test1()
	{
		$o = new JLD_DeliciousPosts( self::$feed );
		$r = $o->run();
		
		foreach( $o as $post )
			$this->assertEquals( $post instanceof JLD_DeliciousPost, true );		
						
	}

}// end UnitTest class