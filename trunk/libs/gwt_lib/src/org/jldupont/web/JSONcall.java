/**
 * @author Jean-Lou Dupont
 * 
 * @example 
 * 		JSONcall jscb = new JSONcall("my_id");
 * 		jscb.setTimeout = 2000;  //from BaseCall
 * 		jscb.setUrl = "http://del.icio.us/feeds/json/tagsFetcher/jldupont";
 * 		jscb.addParam( "callback", "some_callback_function" );
 * 
 */
package org.jldupont.web;

import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Element;
import org.jldupont.system.Logger;

public class JSONcall 
	extends BaseCall {

	final static String thisClass = "org.jldupont.web.JSONcall";
	
	/**
	 * static counter for tracking usage count
	 */
	static int count = 0;
	
	/**
	 * This instance id
	 *  Derived from static counter
	 */
	int id;
	
	/**
	 * The id used for 
	 *  the script element generated
	 */
	int scriptElementIdCounter = 0;
	
	/**
	 * DOM element
	 */
	Element eScript = null;
	
	/**
	 * The DOM element id
	 *  for the generated script tag
	 */
	String scriptElementId = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public JSONcall( String classe, String id ) {
		super(classe,id);
		setup();
	}
	public JSONcall(String id) {
		super(thisClass, id);
		setup();
	}
	public JSONcall() {
		super(thisClass,"default_id");
		setup();		
	}
	private void setup() {
		setRecyclable();
		this.id = count++;
	}
	/*===================================================================
	 * PUBLIC 
	 ===================================================================*/
	public int getUniqueId() {
		return this.id;
	}
	
	/*===================================================================
	 * TEMPLATE METHOD pattern 
	 ===================================================================*/
	/**
	 * doFetch
	 * @see org.jldupont.web.BaseCall#doFetch()
	 */
	protected void doCall( String complete_url ) {
		
		setupScriptElement( complete_url );
	}
	/**
	 * @see org.jldupont.system.JLD_Object#_clean()
	 */
	public void _clean() {
		deleteScriptElement();
	}
	/**
	 * @see org.jldupont.system.JLD_Object#_exId()
	 */
	protected String _exId() {
		return String.valueOf( this.id );
	}
	
	/*===================================================================
	 * PRIVATE 
	 ===================================================================*/
	/**
	 * setupScriptElement
	 * 
	 * - If the script element already exists on the page, get rid of it.
	 * - Create a new script tag with a new id
	 */
	private void setupScriptElement( String cUrl ) {

		Logger.log(thisClass + ".setupScriptElement");
		
		// get rid of any previous script tag.
		//  This assumes that this object instance was recycled properly of course.
		if ( this.scriptElementId != null ) {
			Logger.log(this.classe+".setupScriptElement: script tag element exists with id[" + this.scriptElementId + "]" );
			deleteScriptElement();
		}		
		// create a new script tag element
		this.scriptElementId = "JSONcall" + String.valueOf(this.scriptElementIdCounter++);
		// Logger.log(this.classe+".setupScriptElement: creating script tag with id[" + this.scriptElementId + "]" );
		// Logger.log(thisClass + ".setupScriptElement: *before* injectScript");
		injectScript( this.scriptElementId, cUrl );
		// Logger.log(thisClass + ".setupScriptElement: *after* injectScript");		
		
		// hope everything went ok...
		this.eScript = DOM.getElementById( this.scriptElementId );
		if ( this.eScript == null ) {
			String msg = new String( thisClass+".setupScriptElement: ERROR creating tag element with id[" + this.scriptElementId + "]"  );
			Logger.log( msg );
			throw new RuntimeException(msg);
		}
		
	}
	/**
	 * Deletes a script tag element from the wrapper
	 */
	private void deleteScriptElement( ) {
	
		if ( this.eScript != null ) {
			Element parent = DOM.getParent( this.eScript );
			DOM.removeChild( parent, this.eScript );
			Logger.log("JSONcall.deleteScriptElement: deleted script tag ");
			this.eScript = null;
		}
	}
	

	/*===================================================================
	 * NATIVE methods 
	 ===================================================================*/
	/**
	 * Only way to check if loading was successful is for the
	 *  callback to execute. This must be implemented in derived classes.
	 */
	private native void injectScript( String script_id, String src ) /*-{
	
		var script = $wnd.document.createElement("script");
		script.setAttribute("src", src);
		script.setAttribute("id", script_id);
		script.setAttribute("type", "text/javascript");
       
		$wnd.document.getElementsByTagName("head")[0].appendChild(script);
		
	}-*/;
	
}//end class
