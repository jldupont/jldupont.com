/**
 * CommandParameter interface
 * 
 * @author Jean-Lou Dupont
 * 
 * Integers must be passed in String format
 * 
 */
package org.jldupont.command;

public interface CommandParameters {

	public Object getParameter( String key );
	
	public void   setParameter( String key, Object o );
	
}//end
