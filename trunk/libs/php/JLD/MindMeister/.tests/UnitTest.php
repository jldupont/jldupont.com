<?php
/**
 *  JLD_MindMeister package unit tests
 *
 *  @author Jean-Lou Dupont
 *  @version @@package-version@@
 */

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__)."/../MindMeister.php";
require_once dirname(__FILE__).'/config.php';

class UnitTest extends PHPUnit_Framework_TestCase
{
	var $mm;
	
	/*
	 * GENERIC setup and teardown
	 */		
    protected function setUp()
    {
    	global $api_key, $secret_key;
    	$this->mm = new JLD_MindMeister( $api_key, $secret_key );
    }
 	protected function tearDown()
	{
	}
	/*
			object(SimpleXMLElement)#110 (2) {
			  ["@attributes"]=>
			  array(1) {
			    ["stat"]=>
			    string(4) "fail"
			  }
			  ["err"]=>
			  object(SimpleXMLElement)#115 (1) {
			    ["@attributes"]=>
			    array(2) {
			      ["code"]=>
			      string(2) "96"
			      ["msg"]=>
			      string(33) "The passed signature was invalid."
			    }
			  }
			}
	 */
	public function disabled_testWrongKey() {

		global $api_key, $secret_key;
    	$mm = new JLD_MindMeister( $api_key, 'secret_key' );
	
		$r = $mm->getfrob( );
		
		$this->assertEquals( $r instanceof JLD_MindMeister_err, true );
	}
	/*
		 object(SimpleXMLElement)#111 (2) {
		  ["@attributes"]=>
		  array(1) {
		    ["stat"]=>
		    string(2) "ok"
		  }
		  ["frob"]=>
		  string(16) "c997510c7862e6d4"
		}
	 */
	public function disabled_testGetFrob()
	{
		$r = $this->mm->getfrob( );
		
		$this->assertEquals( $r instanceof JLD_MindMeister_frob, true );
	}

	public function testGetAuth()
	{
		$r = $this->mm->getfrob( );
		
		$a = $this->mm->auth( array( 'frob' => $r, 'perms' => 'delete' )  );
		
		var_dump( $a );
		#$this->assertEquals( $r instanceof JLD_MindMeister_frob, true );
	}
	
	
}// end UnitTest class