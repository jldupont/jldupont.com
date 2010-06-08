<?php
/**
 *  JLD_Gliffy package unit tests
 *
 *  @author Jean-Lou Dupont
 *  @version 1.0.0
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__)."/../../Delicious/DeliciousPosts.php";
require_once dirname(__FILE__)."/../../Delicious/DeliciousPost.php";
require_once dirname(__FILE__)."/../Gliffy.php";
require_once dirname(__FILE__)."/../Gliffy_Delicious.php";
require_once dirname(__FILE__)."/../Gliffy_PictureRepresentation.php";

class UnitTest extends PHPUnit_Framework_TestCase
{
	static $feed = "jldupont/my-diagrams";
	
	var $o = null;
	
	/*
	 * GENERIC setup and teardown
	 */		
    protected function setUp()
    {
    	$this->o = new JLD_DeliciousPosts( self::$feed );
    	$this->o->run();
    }
 	protected function tearDown()
	{
		$this->o = null;
	}
	public function test1()
	{
		foreach( $this->o as $post ) {
		
			$g = JLD_Gliffy::newFromDeliciousPost( $post );
			$this->assertEquals( $g instanceof JLD_Gliffy, true );

			$url = $g->getUrl();
			echo "url: ".$url."\n";
		}
	}

	public function testIterator() {
	
		foreach( $this->o as $post ) {
		
			$g = JLD_Gliffy::newFromDeliciousPost( $post );
			$this->assertEquals( $g instanceof JLD_Gliffy, true );
			
			$i = $g->getPictureIterator();
			$title = $g->title;
			
			foreach( $i as $index => $picRepr ) {

				$this->assertEquals( $picRepr instanceof JLD_Gliffy_PictureRepresentation, true );
				$t  = $picRepr->title;
				$id = $picRepr->id;
				$ext= $picRepr->ext;
				$size=$picRepr->size;
				 
				echo "\nRepresention for $title - $index - size $size - ext $ext";				
			}
			
			
		}
	
	
	}
	
}// end UnitTest class