package org.jldupont.system;

/**
 * Recycle interface
 * 
 * @author Jean-Lou Dupont
 */
public interface Recycle {

	/**
	 * Called by the ObjectPool class
	 * when an object is about to be offered
	 * to a requesting client.
	 */
	public void _clean();
}
