/**
 * LoggableRuntimeException
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.system;

public class LoggableRuntimeException 
	extends RuntimeException {

	final static long serialVersionUID = 0l;
	
	public LoggableRuntimeException(String msg) {
		super(msg);
		Logger.log(msg);
	}
	
}//end
