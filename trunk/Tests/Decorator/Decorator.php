<?php

class base {

	var $rc = null;
	
	var $classe = null;

	protected function init() {
		static $init = false;
	
		if ( $init )
			return;

		echo __METHOD__." called \n";
			
		$this->classe = get_class( $this );
					
		$this->rc = new ReflectionClass( $this->classe );
		
		$init = true;
	}

	public function __call( $method, $args ) {
	
		$this->init();

		$_method = '_' . $method;
		
		$_methodExists = $this->rc->hasMethod( $_method );
		
		if ( !$_methodExists )
			throw new Exception("method _$method not defined" );
			
		$this->executeBefore( $_method );
			
		return $this->$_method( $args );
	}

	protected function executeBefore( &$_method ) {
	
		$beforeMethod = $this->getBefore( $_method );
		if ( $beforeMethod === null )
			return;
			
		return $this->$beforeMethod();
	}
	
	protected function getBefore( &$_method ) {
	
		$rm = new ReflectionMethod( $this->classe, $_method );
	
		$doc = $rm->getDocComment();
		
		$result = preg_match( '/\@before\((.*)\)/siU', $doc, $match );
		if ( ( $result === 0 ) or ( $result === false ) )
			return null;
			
		return $match[1];
	}
	
}

class test extends base {

	/**
	 * @before(before)
	 */
	public function _method() {
		echo __METHOD__." called\n";	
	}

	public function before() {
		echo __METHOD__." called\n";
	}

	
	public function _method2() {
		echo __METHOD__." called\n";	
	}

}//end


$obj = new test;

$obj->method();
$obj->method();
$obj->method2();

