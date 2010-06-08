/**
 * CallbackResponseObject
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.web;

import org.jldupont.system.JLD_Object;

public class CallbackResponseObject 
	extends JLD_Object {

	final static String thisClass = "org.jldupont.web.CallbackResponseObject";
	
	/**
	 * Error codes
	 */
	public final static int NA      = 0;
	public final static int OK      = 1;
	public final static int TIMEOUT = 2;
	
	/**
	 * Status Code
	 *  true:  callback performed
	 *  false: callback not performed
	 */
	boolean status = false;
	
	/**
	 * Error code
	 */
	int errorCode = NA;
	
	/**
	 * Response object
	 */
	Object o = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	/**
	 * Used to create a 'call complete' object with a response code
	 */
	public CallbackResponseObject(int code) {
		super( thisClass, "default_id", true );
		this.status    = true;
		this.errorCode = code;	
	}

	
	public CallbackResponseObject(Object o) {
		super( thisClass, "default_id", true );
		setup();
		this.o = o;
	}

	public CallbackResponseObject() {
		super( thisClass, "default_id", true );
		setup();		
	}
	public CallbackResponseObject(String id) {
		super( thisClass, id, true );
		setup();
	}
	
	private void setup() {
		this.o = null;
		this.status    = false;
		this.errorCode = NA;	
	}

	/*===================================================================
	 * PUBLIC INTERFACE
	 * 
	 *  CommandStatus
	 ===================================================================*/
	/**
	 * Returns the status
	 *  true:  callback performed
	 *  false: callback not performed
	 *  
	 * @return boolean
	 */
	public boolean getStatus() {
		return this.status;
	}
	
	public int getErrorCode() {
		return this.errorCode;
	}
	
	public boolean isError() {
		return ( this.status == true && (this.errorCode != OK ) );
	}
	
	public void setResponseObject(Object o) {
		this.o = o;
	}
	
	public Object getResponseObject() {
		return this.o;
	}
	/*===================================================================
	 * Recycling
	 ===================================================================*/
	public void _clean() {
		super._clean();
		this.setup();
	}
	
}//end
