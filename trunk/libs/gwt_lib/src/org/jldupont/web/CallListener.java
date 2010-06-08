/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.web;

import com.google.gwt.user.client.Event;
import com.google.gwt.user.client.EventListener;

public interface CallListener 
	extends EventListener {
	
	public void fireCallEvent(CallbackResponseObject c);

	/**
	 * Declare here so to help derived classes 
	 */
	public void onBrowserEvent(Event event); 
	
}
