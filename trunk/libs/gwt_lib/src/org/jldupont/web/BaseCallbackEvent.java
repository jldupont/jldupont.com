/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.web;

import com.google.gwt.core.client.JavaScriptObject;

public interface BaseCallbackEvent {
	public void handleCallbackEvent(int id, JavaScriptObject obj);
}
