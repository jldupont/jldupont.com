<?php
/**
 * @author Jean-Lou Dupont
 */
require 'HTTP/Request.php';
$a = &new HTTP_Request('http://api.feedburner.com/awareness/1.0/GetFeedData?uri=http://feeds.feedburner.com/jldupont/Mediawiki');
$a->sendRequest();
//echo $a->getResponseBody();

$xml = new SimpleXMLElement( $a->getResponseBody() );

//var_dump( $xml );
/**
object(SimpleXMLElement)#5 (3) {
  ["@attributes"]=>
  array(1) {
    ["stat"]=>
    string(2) "ok"
  }
  ["comment"]=>
  object(SimpleXMLElement)#6 (0) {
  }
  ["feed"]=>
  object(SimpleXMLElement)#7 (2) {
    ["@attributes"]=>
    array(2) {
      ["id"]=>
      string(7) "1433336"
      ["uri"]=>
      string(18) "jldupont/Mediawiki"
    }
    ["entry"]=>
    object(SimpleXMLElement)#8 (1) {
      ["@attributes"]=>
      array(4) {
        ["date"]=>
        string(10) "2008-02-24"
        ["circulation"]=>
        string(1) "8"
        ["hits"]=>
        string(2) "23"
        ["reach"]=>
        string(1) "1"
      }
    }
  }
} 
 */
$circulation = $xml->feed->entry['circulation'];
$hits = $xml->feed->entry['hits'];
$reach = $xml->feed->entry['reach'];
$date = $xml->feed->entry['date'];
