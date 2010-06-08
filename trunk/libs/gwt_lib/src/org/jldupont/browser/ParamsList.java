/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.browser;

import org.jldupont.system.JLD_Object;

import java.util.Iterator;

// no generics in GWT1.4
abstract public class ParamsList 
	extends JLD_Object
	implements Iterator<Object> {
	
	/**
	 * Constant 
	 */
	static int MAX_PARAMS = 10;
	
	int index = 0;
	
	int num_params = 0;
	
	Param[] params;

	/**
	 * Constructor
	 */
	public ParamsList( ) {
		
		this.params = new Param[MAX_PARAMS];
		getListe();
		extractParams();
	}

	/*===================================================================
	 * ITERATOR 
	 ===================================================================*/
	
	public boolean hasNext() {
		
		if ( this.params == null )
			return false;
		
		return ( this.index < this.num_params );
	}
	
	public Object next() {
		
		if ( this.params == null )
			throw new NullPointerException("empty params list");
		
		return this.params[ this.index++ ];
	}
	//TODO if necessary...
	public void remove() {
		
	}
	
	public void rewind() {
		this.index = 0;
	}
	/*===================================================================
	 * ABSTRACT
	 ===================================================================*/
	
	abstract protected void extractParams( );

	abstract protected void getListe();
	
}//endclass