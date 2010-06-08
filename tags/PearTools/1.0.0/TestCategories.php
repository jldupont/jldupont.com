<?php
/*
	@author: Jean-Lou Dupont
	$Id: TestCategories.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $
*/
//<source lang=php>
require 'JLD/PearTools/Channel.php';
require 'JLD/PearTools/Categories.php';

$c = JLD_PearTools_Channel::singleton();

// DON'T CHANGE THE DIRECTORY PATH OF THIS FILE OR ELSE THIS WON'T WORK
$root = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );

$c->init( $root );

echo 'Channel name: '.$c->getURI()."\n";

$cs = JLD_PearTools_Categories::singleton();
$cs->init( $c );

$cats = $cs->getAll();

#var_dump( $cats );
echo " category 'Amazon' exists? ".$cs->existsCategory('Amazon')."\n";

$pif = $cs->getPackageInfoObject( 'Amazon' );
$pif->addRelease( 'AmazonS3', '2.0' );
$r = $cs->updatePackageInfoFile( $pif );

echo "result: ".$r."\n";

#echo ' exists Amazon/AmazonS3: '.$cs->existsPackage( 'Amazon', 'AmazonS3' )."\n";
#$cs->addRelease( 'Amazon', 'AmazonS3', '2.0' );

//</source>