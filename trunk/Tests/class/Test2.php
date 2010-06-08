<?php

class A
{
	var $va = 'var a';
	static $a = 'static a';
	
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
	var $vb = 'var b';
	static $b = 'static b';
		
	public function __call( $method, $args )
	{
		echo __METHOD__."\n";
	}
	public static function staticMethod()
	{
		echo __METHOD__."\n";		
	}	

}

$vars_a = get_class_vars( 'A' );
var_dump( $vars_a );

$vars_b = get_class_vars( 'B' );
var_dump( $vars_b );

$a = new A;
$b = new B;

$vars_a = get_object_vars(  $a  );
var_dump( $vars_a );

$vars_b = get_object_vars( $b  );
var_dump( $vars_b );
