<?php

require_once "HTTP/Request.php";
require_once "Net/URL2.php";

$url =& new Net_URL2("http://mediawiki.googlecode.com/svn");

echo "url protocol: ".$url->protocol."\n";
echo "url host: ".$url->host."\n";
echo "url port: ".$url->port."\n";

$req =& new HTTP_Request;
$req->setURL($url->protocol . "://" . $url->host . ":" . $url->port . "/channel.xml");
$req->sendRequest();

echo $req->getResponseCode();

require_once 'PEAR/ChannelFile.php';
$chan = new PEAR_ChannelFile;
echo 'XML: ';
if (!$chan->fromXmlString($req->getResponseBody()))
{
	echo 'channel.xml invalid';
	die;
}
echo "OK \n";
echo "CHANNEL SEMANTIC: ";
if (!$chan->validate()) 
{
	echo 'channel.xml invalid';
	die;
}
echo "OK \n";


echo "server: ".$chan->getServer();