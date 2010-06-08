<?php
require_once 'PHPUnit/Framework.php';

require_once 'JLD/Object/Object.php';

/*
 * Since the class in question is abstract, 
 * we need a 'real' class to work with.
 */
class Test extends JLD_Object
{
	var $somedata = null;
	
	public static function singleton()
	{
		return parent::singleton( __CLASS__ );
	}
	public function config( &$ref, &$params )
	{
		return $this->validateConfiguration( $ref, $params );
	}
}

class UnitTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
		$o = new Test;
 
         $this->assertEquals(true, is_object( $o ) );
    }
	public function testSingleton()
	{
		$o = Test::singleton();
		
        $this->assertEquals(true, is_object( $o ) );		
	}
	public function testSingleton2()
	{
		$o1 = Test::singleton();
		$o1->somedata = 'data1';
		
		$o2 = Test::singleton();
		$o2->somedata = 'data2';
		
        $this->assertEquals('data2', $o1->somedata );				
	}
	public function testVars1()
	{
		$o = Test::singleton();
		
        $this->assertEquals( false, $o->varExists('test') );
        $this->assertEquals( null, $o->__get('test') );		
	}
	public function testVars2()
	{
		$o = Test::singleton();
		$o->__set( 'test2', true );
		
        $this->assertEquals( true, $o->varExists('test2') );
        $this->assertEquals( true, $o->__get('test2') );		
	}
	public function testValidate1()
	{
		$o = Test::singleton();
		$list = array();
		
		$result = false;
		try
		{
			$o->config( $list, $list );	
		}
		catch(Exception $e)
		{
			$result = true;
		}
        $this->assertEquals( true, $result );		
	}
	public function testValidate2()
	{
		$o = Test::singleton();
		$ref = array(
			array( 'r' => true,  'key' => 'key1' ),
			array( 'r' => true,  'key' => 'key2' ),			
			array( 'r' => false,  'key' => 'key3' ),			
		);
		$liste = array(
			'key1' => 'key1',
			'key2' => 'key2',
		);
		$result = $o->config( $ref, $liste );
        $this->assertEquals( true, $result );				
	}
	public function testValidate3()
	{
		$o = Test::singleton();
		$ref = array(
			array( 'r' => true,  'key' => 'key1' ),
			array( 'r' => true,  'key' => 'key2' ),			
			array( 'r' => false,  'key' => 'key3' ),			
		);
		$liste = array(
			'key1' => 'key1',
		);
		$result = $o->config( $ref, $liste );
        $this->assertEquals( 'key2', $result );				
	}
	public function testValidate4()
	{
		$o = Test::singleton();
		$ref = array(
			array( 'r' => true,  'key' => 'key1' ),
			array( 'r' => true,  'key' => 'key2' ),			
			array( 'r' => false,  'key' => 'key3' ),			
		);
		$liste = array(
			'key1' => 'key1',
			'key2' => 'key2',			
			'key3' => 'key3',			
		);
		$result = $o->config( $ref, $liste );
        $this->assertEquals( true , $result );				
	}

}
