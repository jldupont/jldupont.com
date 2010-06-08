/**
 * Command interface
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.command;

public interface CommandInterface {

	/**
	 * Execute the command chain
	 */
	public CommandStatus run( CommandParameters p );
	
	/**
	 * Verifies if the completion is pending
	 * @return boolean
	 */
	public boolean isPending();
	
	/**
	 * Returns the status code
	 * @return boolean
	 */
	public boolean getStatusCode();
	
	/**
	 * Returns the exit code
	 * Only valid if the 'pending' status is false
	 * 
	 * @return boolean
	 */
	public int getExitCode();
	
	/**
	 * Sets the next command in the chain
	 *  Used for the 'normal' execution path.
	 * 
	 * @param me Command - used for callback path
	 * @param next Command
	 */
	public void setNext( CommandInterface me, CommandInterface next );
	
	/**
	 * Used by command class to propagate command chain status
	 * @param s
	 */
	public void setStatus( CommandStatus s );
	
	/**
	 * The parameter name to look for when
	 *  being called
	 *  
	 * @param paramName
	 */
	public void setParameterName( String paramName );
	
}//end