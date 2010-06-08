<?php

/**
 * Class Test
 * @author Jean-Lou Dupont 
 */
class Test {


}//end class

$rc = new ReflectionClass( 'Test' );

echo $rc->getDocComment();
