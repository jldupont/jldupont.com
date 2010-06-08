<?php
/*
	PEAR Channel Tool: parses the 'categories' folder of the REST interface.
	@author: Jean-Lou Dupont
	$Id: Xml.php 252 2007-11-15 18:27:16Z JeanLou.Dupont $
*/
//<source lang=php>
abstract class JLD_PearTools_Xml 
{
	var $emap;
	
	var $raw;
	var $data = null; 
	
	public function __construct()
	{
	}
	public function get( $level, &$data )
	{
		$result = '';
		
		if (empty( $data ))
			return null;
		
		foreach ( $data as $key => &$value )	
		{
			$h = $level.'|'.$key;
			$info = $this->emap[ $h ];
			$method = "type".$info['type'];
			$tpl = $info['tpl'];
			$this->$method( $h, $value, $tpl );
			$result .= $tpl;
		}
		
		return $result;
	}
	protected function typeBr( &$key, &$value, &$tpl ) 
	{
		foreach( $value as $k => &$v )
		{
			$result = $this->get( $key, $value );
			$this->replaceMagicWords( $tpl, $result );
		}
	}
	/**
	 * e.g. p|r
	 */
	protected function typeAttr( &$key, &$value, &$tpl ) 
	{
		// the top level element will come in a different order...
		if (isset( $value['attribs']))
			$attribs = $value['attribs'];
		else
			$attribs = $value;

		if (isset( $value['_content']))
			$_content = $value['_content'];
		else
			$_content = '';
		
		$a = '';
		if (!empty( $attribs ))
			foreach ( $attribs as $k => &$v )
				$a .= "$k = '$v' ";

		$this->replaceMagicWords( $tpl, $_content, $a  );
	}
	/**
	 * e.g. p|n
	 */
	protected function typeLeaf( &$key, &$value, &$tpl )
	{
		$this->replaceMagicWords( $tpl, $value );
	}
	/**
	 */
	protected function replaceMagicWords( &$tpl, &$contents, &$attribs = null )
	{
		$tpl = str_replace('%attribs%',	$attribs,	$tpl );		
		$tpl = str_replace('%contents%',$contents,	$tpl );		
		$tpl = str_replace('%n%', 		"\n", 		$tpl );
		$tpl = str_replace('%t%', 		"\t", 		$tpl );		
	}
	/**
	 */
	public function processCommands( $level, &$source, &$commands )
	{
		
	}
}
//</source>