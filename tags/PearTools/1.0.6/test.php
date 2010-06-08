<?php

class XClass
{
	public function __construct( &$i )
	{
		$a = unserialize( $i );
	
		$b = $this->toXML( 'f', $a );
		
		var_dump( $b );
	}

	/**
	 * Expand list
	 */
	protected function expandList( $top, &$liste, $level )
	{
		$r = null;
		if (!is_array(current( $liste )))
		{
			foreach( $liste as $tag => &$value )
			{
				$r .= $this->openTag( $tag, $level );
				$r .= $value;
				$r .= $this->closeTag( $tag, $level, true );
			}
			return $r;
		}
		foreach( $liste as &$entry )
		{
			$r .= $this->openTag( $top, $level );
			$r .= $this->expandList( $top, $entry, $level+1 );
			$r .= $this->closeTag( $top, $level, false );			
			
		}
		return $r;
	}
	protected function openTag( $tag, $level, $attribs = null )
	{
		$r  = "\n".str_repeat("\t", $level );	
		$r .= '<'.$tag;
		if (!empty( $attribs ))
			foreach( $attribs as $key => &$value )
				$r .= " $key=".'"'.$value.'"';
		$r .= '>';		
		return $r;
	}
	protected function closeTag( $tag, $level, $shortClose = true )
	{
		if (!$shortClose)
			$r  = "\n".str_repeat("\t", $level );			
		$r .= '</'.$tag.'>';
		return $r;
	}
	protected function toXMLlist( $tag, &$liste, $level )
	{
		if (empty( $liste ))
			return null;
		$r = null;
		foreach( $liste as &$e )
		{
			$r .= $this->openTag( $tag, $level );
			$r .= $e;
			$r .= $this->closeTag( $tag, $level );
		}
		return $r;
	}
	/**
	 * Generates an XML file from a XML-ish array structure.
	 */
	protected function toXML( $top, $s, $level = 0 )
	{
		$r = null;
		if ($level === 0)
			$r .= '<'.'?xml version="1.0" encoding="UTF-8" ?>'."\n";

		$_attribs = null;
		if ( is_array( $s ) && (key( $s ) === 'attribs') )
		{
			$_attribs = current( $s );
			array_shift( $s );
		}
		// are we traversing a array of children?
		if ( !is_numeric( $top ) )
			$r .= $this->openTag( $top, $level, $_attribs );					

		if ( is_array( $s ) && (key( $s ) === '_content') )
			$s = current( $s );

		$shortClose = false;
		if (!is_array( $s ))
		{
			$r .= $s;
			$shortClose = true;
		}
		
		while( is_array( $s ) && !empty( $s ) )
		{
			$t =     key( $s );
			$c = current( $s );
		
			$r .= $this->toXML( $t, $c, $level+1 );
			
			// NEXT		
			array_shift( $s );					
		};
		// are we traversing a array of children?
		if ( !is_numeric( $top ))
			$r .= $this->closeTag( $top, $level, $shortClose );
		
		return $r;

	}//function
}

$i = <<<EOT
a:2:{s:7:"attribs";a:4:{s:5:"xmlns";s:48:"http://pear.php.net/dtd/rest.categorypackageinfo";s:9:"xmlns:xsi";s:41:"http://www.w3.org/2001/XMLSchema-instance";s:11:"xmlns:xlink";s:28:"http://www.w3.org/1999/xlink";s:18:"xsi:schemaLocation";s:103:"http://pear.php.net/dtd/rest.categorypackageinfo   http://pear.php.net/dtd/rest.categorypackageinfo.xsd";}s:2:"pi";a:5:{i:0;a:2:{s:1:"p";a:7:{s:1:"n";s:9:"ImageLink";s:1:"c";s:28:"mediawiki.googlecode.com/svn";s:2:"ca";a:2:{s:7:"attribs";a:1:{s:10:"xlink:href";s:23:"/rest/c/ParserFunctions";}s:8:"_content";s:15:"ParserFunctions";}s:1:"l";s:0:"";s:1:"s";s:35:"Provides configurable image anchors";s:1:"d";s:71:"Provides a clickable image link using an image from NS_IMAGE namespace.";s:1:"r";a:1:{s:7:"attribs";a:1:{s:10:"xlink:href";s:17:"/rest/r/imagelink";}}}s:1:"a";a:1:{s:1:"r";a:2:{s:1:"v";s:5:"1.1.1";s:1:"s";s:6:"stable";}}}i:1;a:2:{s:1:"p";a:7:{s:1:"n";s:12:"ParserPhase2";s:1:"c";s:28:"mediawiki.googlecode.com/svn";s:2:"ca";a:2:{s:7:"attribs";a:1:{s:10:"xlink:href";s:23:"/rest/c/ParserFunctions";}s:8:"_content";s:15:"ParserFunctions";}s:1:"l";s:0:"";s:1:"s";s:51:"Provides 'parser cache friendly' page manipulation.";s:1:"d";s:14:"description...";s:1:"r";a:1:{s:7:"attribs";a:1:{s:10:"xlink:href";s:20:"/rest/r/parserphase2";}}}s:1:"a";a:1:{s:1:"r";a:2:{s:1:"v";s:5:"1.1.0";s:1:"s";s:6:"stable";}}}i:2;a:2:{s:1:"p";a:7:{s:1:"n";s:15:"ParserFunctions";s:1:"c";s:28:"mediawiki.googlecode.com/svn";s:2:"ca";a:2:{s:7:"attribs";a:1:{s:10:"xlink:href";s:23:"/rest/c/ParserFunctions";}s:8:"_content";s:15:"ParserFunctions";}s:1:"l";s:0:"";s:1:"s";s:42:"Tim Starling's MediaWiki Parser Functions.";s:1:"d";s:44:"See [http://MediaWiki.org] for more details.";s:1:"r";a:1:{s:7:"attribs";a:1:{s:10:"xlink:href";s:23:"/rest/r/parserfunctions";}}}s:1:"a";a:1:{s:1:"r";a:2:{s:1:"v";s:5:"1.0.0";s:1:"s";s:6:"stable";}}}i:3;a:2:{s:1:"p";a:7:{s:1:"n";s:9:"ParserExt";s:1:"c";s:28:"mediawiki.googlecode.com/svn";s:2:"ca";a:2:{s:7:"attribs";a:1:{s:10:"xlink:href";s:23:"/rest/c/ParserFunctions";}s:8:"_content";s:15:"ParserFunctions";}s:1:"l";s:0:"";s:1:"s";s:30:"Collection of parser functions";s:1:"d";s:44:"See .doc.wikitext files for more information";s:1:"r";a:1:{s:7:"attribs";a:1:{s:10:"xlink:href";s:17:"/rest/r/parserext";}}}s:1:"a";a:1:{s:1:"r";a:2:{s:1:"v";s:5:"1.1.0";s:1:"s";s:6:"stable";}}}i:4;a:2:{s:1:"p";a:7:{s:1:"n";s:18:"PageAfterAndBefore";s:1:"c";s:28:"mediawiki.googlecode.com/svn";s:2:"ca";a:2:{s:7:"attribs";a:1:{s:10:"xlink:href";s:23:"/rest/c/ParserFunctions";}s:8:"_content";s:15:"ParserFunctions";}s:1:"l";s:0:"";s:1:"s";s:55:"Gets the page that preceedes or succeedes a given page.";s:1:"d";s:44:"See .doc.wikitext files for more information";s:1:"r";a:1:{s:7:"attribs";a:1:{s:10:"xlink:href";s:26:"/rest/r/pageafterandbefore";}}}s:1:"a";a:1:{s:1:"r";a:2:{s:1:"v";s:5:"1.0.2";s:1:"s";s:6:"stable";}}}}}
EOT;

$r = new XClass( $i );
