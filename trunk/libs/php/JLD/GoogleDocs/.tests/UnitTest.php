<?php
/**
 * Unit Tests for GoogleDocs package
 *
 * @package GoogleDocs
 * @author Jean-Lou Dupont
 * @version $Id: UnitTest.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
 
//<source lang=php>

require_once 'PHPUnit/Framework.php';

error_reporting( E_ALL | E_STRICT );

require dirname(dirname(__FILE__)).'/RegistryRepository.php';

class UnitTest extends PHPUnit_Framework_TestCase
{
	static $gs_user;
	static $gs_password;
	static $gs_document;
	static $gs_worksheet;
	
	var $r;
	
	public function testInit()
	{
		$this->r = new JLD_GoogleDocs_RegistryRepository;
		
		$params = array( 
						'gs_user' 		=> self::$gs_user,
						'gs_password'	=> self::$gs_password,
						'gs_document'	=> self::$gs_document,
						'gs_worksheet'	=> self::$gs_worksheet,
		);
		$r = true;
		try 
		{
			$this->r->init( $params );
		} catch( Exception $e )
		{
			$r= false;
		}
		$this->assertEquals( true, $r );
	}	
	
}//end class

require_once 'UnitTest.config.php';

//</source>