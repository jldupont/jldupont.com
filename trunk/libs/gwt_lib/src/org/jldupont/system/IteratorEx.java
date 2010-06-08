/**
 * IteratorEx
 *  An extended iterator interface
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.system;

import java.util.Iterator;

public interface IteratorEx 
	extends Iterator<Object> {

	/**
	 * This method shall be called *prior* to using
	 * the interface.
	 */
	public void reset();
	
}
