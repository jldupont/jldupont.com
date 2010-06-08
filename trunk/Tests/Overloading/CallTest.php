<?php

class Test {

	public function __call( $method, $args ) {
		echo "calling method: $method \n";
	}

	public function method1() {
		echo __METHOD__."\n";	
	}
	
}

$obj = new Test;

$obj->method1();

$obj->method2();