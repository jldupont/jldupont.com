<?php
/**
 * @author Jean-Lou Dupont
 * @package JLD
 * @subpackage api
 * @version $Id: Api.php 910 2009-05-22 16:46:07Z JeanLou.Dupont $
 */
//<source lang=php> 

/** 
 * This file is the entry point for all API queries. 
 */

// Initialise common code
require 'JLD/Cache/Cache.php';
require 'JLD/Registry/Registry.php';
require 'JLD/Request/Request.php';
require 'JLD/Api/Api_main.php';


/* Construct an ApiMain with the arguments passed via the URL. What we get back
 * is some form of an ApiMain, possibly even one that produces an error message,
 * but we don't care here, as that is handled by the ctor.
 */
$processor = new JLD_api_main( JLD_Request::singleton() );

// Verify that the API has not been disabled
if ( !$processor->isEnabled() )
{

}

// Process data & print results
$processor->execute();

//</source>