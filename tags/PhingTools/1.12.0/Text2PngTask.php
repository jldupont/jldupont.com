<?php
/**
 * PHING task 
 *
 * 	<taskdef classname='JLD.PhingTools.Text2PngTask' name='txt2png' />
 *	
 *  <txt2png text="" file= "" font="" />
 *
 * @author Jean-Lou Dupont
 * @package PhingTools
 * @version 1.12.0
 * @Id $Id: Text2PngTask.php 307 2007-12-18 02:25:36Z JeanLou.Dupont $
 */
//<source lang=php> 

require_once "JLD/PhingTools/Task.php";

class Text2PngTask extends JLD_PhingTools_Task
{
	const thisName = 'Text2PngTask';
	
	public function setText( $val ) { $this->__set('text', $val); }	
	public function setFile( $val ) { $this->__set('file', $val); }
	public function setFont( $val ) { $this->__set('font', $val); }
	public function setBg( $val ) { $this->__set('bg', $val); }		
	public function setColor( $val ) { $this->__set('color', $val); }			
	
    /**
     * The main entry point method.
     */
    public function main() 
	{
		$this->validateParameters();
		
		if ($this->loadFont() === false)
			throw new BuildException( self::thisName.': error loading font.');

		if ($this->convert() === false)
			throw new BuildException( self::thisName.': error creating image file.');		
    }
	/**
	 *
	 */
	protected function validateParameters()
	{
		$text = $this->__get('text');
		if ( empty( $text ))
			throw new BuildException( self::thisName.': missing text attribute.');

		$file = $this->__get('file');
		if ( empty( $file ))
			throw new BuildException( self::thisName.': missing file attribute.');

		$font = $this->__get('font');
		if ( empty( $font )) 
			throw new BuildException( self::thisName.': missing font OR font file-path attribute.');
			
	} 
	/**
	 * 
	 */
	protected function convert( )
	{
		$h = imagefontheight( $this->font );
		$w = imagefontwidth( $this->font );
		$l = strlen( $this->text )+2;
		
		$im = imagecreate( $w*$l, $h );
		
		// background
		$this->getColor( $this->bg, $br, $bg, $bb );
		imagecolorallocate( $im, $br, $bg, $bb );
		
		// textcolor
		$this->getColor( $this->color, $tr, $tg, $tb );		
		$textcolor = imagecolorallocate($im, $tr, $tg, $tb );		
		
		imagestring( $im, $this->font, $w, 0, $this->text, $textcolor );
		
		$result = imagepng($im, $this->file );		
		imagedestroy( $im );
		return $result;
	}
	/**
	 * 
	 */
	protected function loadFont()
	{
		if (!is_numeric( $this->font ))
			$this->loadFontFile();

		if ( $this->font === false )			
			return false;
			
		return ( $this->font <= 5);		
	}
	/**
	 *
	 */	
	protected function loadFontFile()
	{
		$result = false;
		$fpath = false;
		$pA = explode( PATH_SEPARATOR, get_include_path() );
		foreach( $pA as $path )
		{
			$fpath = $path.'/'.$this->font;
			if (file_exists( $fpath ))
				{ $result = true; break; }
		}
		if ( $result === false )
			throw new BuildException( self::thisName.': invalid font file.' );
			
		$this->font = imageloadfont( $fpath );
	}
	/**
	 *
	 */	
	protected function getColor( $rgb, &$r, &$g, &$b, $dr = 255, $dg = 255, $db = 255 )
	{
		$bits = explode( ',', $rgb);
		$r = isset( $bits[0] ) ? $bits[0]: $dr ;
		$g = isset( $bits[1] ) ? $bits[1]: $dg ;
		$b = isset( $bits[2] ) ? $bits[2]: $db ;
		
		#echo __METHOD__." r:".$r." g:".$g." b:".$b."\n";			
	}	 
}// end class

//</source>