/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.browser;

public class URLParamsList 
	extends ParamsList {
	
	String liste = null;
	
	/**
	 * Constructor
	 */
	public URLParamsList( ) {
		super();
	}

	/*===================================================================
	 * PROTECTED
	 ===================================================================*/
	
	/**
	 * Extracts the params from the "liste" provided
	 * e.g. liste =  u=username&tf=tag1+tag2&pf=companies*
	 */
	protected void extractParams( ) {
		
		int index = this.liste.indexOf( '?' );
		if ( index == -1 )
			return;
		
		// first, split off where the ? is
		String s = new String( this.liste.substring(index +1).trim() );
		if ( s.length() == 0 )
			return;
		
		// "explode" string through the & char
		String[] tuples = s.split("&");
		
		int i,j=0;
		String[] bits;
		
		// "explode" string through the = char
		for ( i=0; i<tuples.length && j<MAX_PARAMS; i++ ) {

			bits = tuples[i].split("=");
			if ( bits.length != 2 )
				continue;
			
			this.params[j++] = new Param( bits[0], bits[1] );
		}
		
		this.num_params = j;
		
	}//extractParams

	/*===================================================================
	 * NATIVE
	 ===================================================================*/
	
	/**
	 * GWT loads the application through an iframe
	 */
	protected void getListe() {
		
		this.liste = new String( _getListe() );
	}
	
	private native String _getListe()  /*-{
	  return parent.window.location.href;
	}-*/;
	
}//endclass