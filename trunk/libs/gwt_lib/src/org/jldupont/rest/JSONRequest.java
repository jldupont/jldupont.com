package org.jldupont.rest;

import com.google.gwt.json.client.JSONValue;

import org.jldupont.system.Liste;

/**
 * JSONRequest
 * 
 * @author Jean-Lou Dupont
 *
 */
public class JSONRequest 
	extends Liste {

	final static String thisClass = "org.jldupont.rest.JSONRequest";
	
	/*===================================================================
	 * Constructors 
	 ===================================================================*/
	public JSONRequest() {
		super( thisClass, "default_id" );
		setup();
	}

	public JSONRequest(String id) {
		super( thisClass, id );
		setup();
	}
	
	private void setup() {
	}
	
	/*===================================================================
	 * PUBLIC 
	 ===================================================================*/
	
	/**
	 * setReqParam
	 * @param key
	 * @param value
	 */
	public void setReqParam(String key, boolean value) {
		this.put( key, value );
	}
	/**
	 * setReqParam
	 * @param key
	 * @param value
	 */
	public void setReqParam(String key, int value) {
		this.put( key, value );
	}
	/**
	 * setReqParam
	 * @param key
	 * @param value
	 */
	public void setReqParam(String key, long value) {
		this.put( key, value );
	}
	/**
	 * setReqParam
	 * @param key
	 * @param value
	 */
	public void setReqParam(String key, JSONValue value) {
		this.put( key, value );
	}
	
	/**
	 * 
	 * @param key
	 * @return Object
	 */
	public Object getReqParam(String key) {
		return this.get(key);
	}
	
	/**
	 * Returns a JSON representation of the object
	 * @return JSONstring
	 */
	public String toJSON() {
		return this.toString();
	}

	/*===================================================================
	 * Recycle 
	 ===================================================================*/
	public void _clean() {
		super._clean();
	}
	
}//end
