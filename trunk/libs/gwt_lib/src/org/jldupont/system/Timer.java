/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.system;

import org.jldupont.system.JLD_Object;

public class Timer 
	extends com.google.gwt.user.client.Timer {

	JLD_Object target;
	
	String id = null;
	
	public Timer() {
		super();
	}
	
	public Timer(String id) {
		super();
		this.id = id;
	}
	/**
	 * When the timer expires, this method is called.
	 */
	public void run() {
		
		target.timerExpiredEvent();
	}
	/**
	 * setTarget
	 * @param target
	 */
	public void setTarget(JLD_Object target) {
		this.target = target;
	}
}
