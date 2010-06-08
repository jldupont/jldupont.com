<?php
/**
 * JLD_MindMeister
 *  
 *
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @package MindMeister
 * @category OnlineTools
 * @example
 */
/*
 1) http://www.mindmeister.com/services/auth/?api_key=abc123
 2) http://www.mindmeister.com/services/auth/?api_key=abc123&perms=delete
 3) http://www.mindmeister.com/services/auth/?api_key=abc123&perms=delete&frob=123456
 4) http://www.mindmeister.com/services/auth/?api_key=abc123&perms=delete&frob=123456&api_sig=zxy987
 
 */
class JLD_MindMeister_method_auth
	extends JLD_MindMeister_Method {

	/**
	 * REST end-point for this method
	 */
	const REST = 'http://www.mindmeister.com/services/rest/?api_key=%api_key%&method=%method%&frob=%frob%&api_sig=%api_sig%';
	
	/**
	 * not really required
	 */
	const METHOD = 'mm.auth.getToken';
	
	/**
	 * Reference list for parameters
	 */
	static $refParams = array(
		'frob'  => array( 'm' => array( __CLASS__, 'checkClass' ),      'a' => 'JLD_MindMeister_frob' )
	);
	
	public function __construct( &$key, &$secret, &$args  ) {

		$this->setParam( 'method', self::METHOD );
		parent::__construct( $key, $secret, $args, self::REST );
	}
	/**
	 * Returns the reference list for parameters of this method
	 * Used in the base class for parameter verification
	 */
	protected function getRefList() {
	
		return self::$refParams;
	}
	
	public function execute() {
	
		$obj = parent::execute( );
		
		if ( isset( $obj->auth ))
			return JLD_MindMeister::factory( 'auth', $obj );

		if ( $obj instanceof JLD_MindMeister_err )
			return $obj;
			
		throw new Exception( "unknown return code" );
	}
	
}