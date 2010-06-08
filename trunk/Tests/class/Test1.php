<?php

class A
{
	public function __call( $method, $args )
	{
		echo __METHOD__."\n";
	}
	
	public static function staticMethod()
	{
		echo __METHOD__."\n";		
	}	
}

class B extends A
{
	public function __call( $method, $args )
	{
		echo __METHOD__."\n";
	}
	public static function staticMethod()
	{
		echo __METHOD__."\n";		
	}	

}

$b = new B;
$b->test();

#
a::staticMethod();
b::staticMethod();
