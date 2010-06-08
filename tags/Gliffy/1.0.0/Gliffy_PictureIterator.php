<?php
/**
 * Gliffy_PictureIterator
 * Iterator for the Gliffy class which iterates
 * over all the 'picture' representations
 *
 * @author Jean-Lou Dupont
 * @version 1.0.0
 * @package Gliffy
 */

class JLD_Gliffy_PictureIterator
	implements Iterator { 

	var $src = null;
	
	/*********************************************************
	 * 				PUBLIC interface
	 ********************************************************/	
	public function __construct( $obj ) {
	
		if ( !( $obj instanceof JLD_Gliffy ) )
			throw new Exception( __METHOD__.": requires an object instance of class JLD_Gliffy");
			
		$this->src = $obj;
	}
	
	
	/*********************************************************
	 * 				ArrayIterator Interface
	 ********************************************************/
	var $index = 0;
	/**
	 * Returns the count of possible representations
	 */
	public function count() {

		return $this->src->getPictureRepresentationCount();
	}
	/**
	 * Returns a JLD_Gliffy_PictureRepresentation object instance
	 */
	public function current() {

		$url   = $this->src->getUrl( $this->src->getPictureRepresentationByIndex( $this->index ) );
		$id    = $this->src->id;
		$title = $this->src->title;
		
		return JLD_Gliffy_PictureRepresentation::newFromUrl( $title, $id, $url );
	}
	/**
	 * We know that the 'key' is really just an index
	 */
	public function key() {

		return $this->src->getPictureRepresentationByIndex( $this->index );
	}
	public function next() {

		$this->index++;
		return $this;	
	}
	public function rewind() {

		$this->index = 0;
		return $this;	
	}
	public function valid() {

		return ( $this->index < $this->src->getPictureRepresentationCount() );
	}
	
} // end class definition
