/**
 * CommandStatus
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.command;

//import org.jldupont.system.JLD_Object;

public class CommandStatus {

	/**
	 * Exit codes
	 */
	public final static int TIMEOUT = 1;
	
	/**
	 * Pending status
	 */
	boolean pending = false;
	
	/**
	 * Exit code
	 */
	int exitCode = 0;

	/**
	 * StatusCode
	 */
	boolean statusCode = false;
	
	/**
	 * Message
	 */
	String message = null;
	
	/*===================================================================
	 * Constructors 
	 ===================================================================*/
	
	/**
	 * Constructor for OK state
	 */
	public CommandStatus() {
		this.exitCode = 0;
		this.statusCode = true;
		this.pending = false;
	}
	
	public CommandStatus( int exitCode, String msg ) {
		setup( exitCode, msg );
	}
	
	/**
	 * Constructor used in error state
	 * @param exitCode
	 */
	public CommandStatus( int exitCode ) {
		this.exitCode = exitCode;
		this.statusCode = false;
		this.pending = false;
	}
	
	public CommandStatus( boolean pending, boolean statusCode ) {
		this.pending = pending;
		this.statusCode = statusCode;
	}

	/**
	 * Constructor for pending state
	 * @param pending
	 */
	public CommandStatus( boolean pending ) {
		this.pending = pending;
	}
	
	/**
	 * Constructor for error state
	 * @param msg
	 */
	public CommandStatus( String msg ) {
		this.pending = false;
		this.exitCode = 0;
		this.statusCode = false;
		this.message = new String(msg);
	}

	/**
	 * Constructor for error state
	 * @param msg
	 */
	public CommandStatus( String msg, boolean status, int code ) {
		this.pending = false;
		this.exitCode = code;
		this.statusCode = status;
		this.message = new String(msg);
	}
	
	private void setup( int exitCode, String msg ) {
		this.exitCode = exitCode;
		this.message = msg;
	}
	
	/*===================================================================
	 * Pending 
	 ===================================================================*/

	public boolean isPending() {
		return this.pending;
	}
	
	public void setPending() {
		this.pending = true;
	}
	
	public void setPending( boolean p ) {
		this.pending = p;
	}
	
	/*===================================================================
	 * ExitCode 
	 ===================================================================*/
	
	public int getExitCode() {
		return this.exitCode;
	}
	
	public void setExitCode( int code ) {
		this.exitCode = code;
	}

	/*===================================================================
	 * StatusCode 
	 ===================================================================*/
	
	public boolean getStatusCode() {
		return this.statusCode;
	}
	public void setStatusCode( boolean code ) {
		this.statusCode = code;
	}
	
	/*===================================================================
	 * Message 
	 ===================================================================*/
	
	public void setMessage( String msg ) {
		this.message = msg;
	}
	
	public String getMessage() {
		return this.message;
	}

	/*===================================================================
	 * State 
	 ===================================================================*/
	public void setState(boolean pending, boolean status) {
		this.pending = pending;
		this.statusCode = status;
	}
}//end
