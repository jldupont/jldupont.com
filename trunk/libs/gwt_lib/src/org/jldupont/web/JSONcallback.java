/**
 * JSONcallback
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.web;

public class JSONcallback 
	extends BaseCallback {
	
	private static final String thisClass = "org.jldupont.web.JSONcallback";
	
	public JSONcallback(String classe,String id) {
		super(classe,id);
	}
	public JSONcallback( String id ) {
		super(thisClass,id);
	}
	public JSONcallback() {
		super(thisClass,"default_id");
	}
	
}//end class
