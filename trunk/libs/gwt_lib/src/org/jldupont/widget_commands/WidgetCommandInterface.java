/**
 * WidgetCommandInterface
 * @author Jean-Lou Dupont
 */
package org.jldupont.widget_commands;

import com.google.gwt.user.client.ui.Widget; 

public interface WidgetCommandInterface {

	/**
	 * Setter
	 * @param w
	 */
	public void setWidget(Widget w);
	
	/**
	 * Clears the widget's content
	 */
	public void clearWidget();
	
}//end
