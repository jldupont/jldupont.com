/**
 * CallEventObject
 * 
 * @deprecated
 * @author Jean-Lou Dupont
 */
package org.jldupont.web;

import java.util.EventObject;
import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.json.client.JSONObject;


public class CallEventObject 
	extends EventObject {

	final static long serialVersionUID = 0l;
	
	JavaScriptObject jsObj = null;

	JSONObject jsonObj;
	
	/**
	 * Timeout indicator
	 */
	boolean timeout = false;
	
	/**
	 * This constructor is used when a timeout event occured
	 * TODO get rid of 'source' parameter!
	 * @param source
	 */
	public CallEventObject( Object source ) {
		super( source );
		this.timeout = true;
	}
	
	public CallEventObject(Object source, JavaScriptObject obj) {
		super(source);
		this.jsObj = obj;
		this.jsonObj = new JSONObject( obj );
	}
	/**
	 * getJSONObject
	 * @return JSONObject
	 */
	public JSONObject getJSONObject() {
		return this.jsonObj;
	}
	/**
	 * getJSObject
	 * @return JavaScript Object
	 */
	public JavaScriptObject getJSObject() {
		return this.jsObj;
	}
	
	/**
	 * If a timeout occured whilst performing the operation
	 * @return boolean
	 */
	public boolean isTimeout() {
		return this.timeout;
	}
}//end
