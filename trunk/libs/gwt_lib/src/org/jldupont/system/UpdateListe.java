/**
 * UpdateListe interface
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.system;

public interface UpdateListe {

	/**
	 * Clears the list
	 */
	public void clear();
	
	/**
	 * appends key at tail
	 * @param key String
	 */
	public void addItem(String key);
	
}//end