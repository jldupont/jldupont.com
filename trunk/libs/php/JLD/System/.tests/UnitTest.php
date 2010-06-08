<?php
require_once 'PHPUnit/Framework.php';

require dirname(dirname( __FILE__ )).'/Exception.php';

class UnitTest extends PHPUnit_Framework_TestCase
{

	public function testExceptionCreation()
	{
		$e = new JLD_System_Exception( 'message',  LOG_EMERG );

		$this->assertEquals( true, is_object( $e ) );
	}
	
}
