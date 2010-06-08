<?php

class Test {

	public function __get( $var ) {
		echo __METHOD__." var: $var \n";
	}

}

$v = Test::__doc__;
