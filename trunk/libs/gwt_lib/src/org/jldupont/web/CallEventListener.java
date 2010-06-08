/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.web;

import com.google.gwt.user.client.EventListener;

public interface CallEventListener 
	extends EventListener {

	public void addCallListener(CallListener s);
	public void removeCallListener(CallListener s);
	
}//end
