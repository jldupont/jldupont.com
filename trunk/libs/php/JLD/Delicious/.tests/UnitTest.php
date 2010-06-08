<?php
/**
 *  JLD_Delicious package unit tests
 *
 *  @author Jean-Lou Dupont
 *  @version @@package-version@@
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__)."/../DeliciousPosts.php";
require_once dirname(__FILE__)."/../DeliciousPost.php";

class UnitTest extends PHPUnit_Framework_TestCase
{
	static $feed1 = "jldupont/my-mindmaps";
	static $feed2 = "jldupont/my-diagrams";	
	
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
		$o = new JLD_DeliciousPosts( self::$feed1 );
		$r = $o->run();
		
		foreach( $o as $post ) {
			echo "post: " . $post->title . "\n";
			$this->assertEquals( $post instanceof JLD_DeliciousPost, true );
		}		
						
	}

	public function test2()
	{
		$o = new JLD_DeliciousPosts( self::$feed2 );
		$r = $o->run();
		
		foreach( $o as $post ) {
			echo "post: " . $post->title . "\n";
			$this->assertEquals( $post instanceof JLD_DeliciousPost, true );
		}		
						
	}
	
}// end UnitTest class