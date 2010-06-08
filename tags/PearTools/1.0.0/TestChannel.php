<?php
/*
	@author: Jean-Lou Dupont
	$Id: TestChannel.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $
*/
//<source lang=php>
require 'JLD/PearTools/Channel.php';

$c = JLD_PearTools_Channel::singleton();

// DON'T CHANGE THE DIRECTORY PATH OF THIS FILE OR ELSE THIS WON'T WORK
$root = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );

$c->init( $root );

echo 'Channel name: '.$c->getURI();

//</source>