/**
 * GoogleGears widget
 *  Displays an image according to the availability status
 *  of Gears on the browser.
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.widget;

import org.jldupont.system.Logger;

public class GoogleGears 
	extends ImgAnchorLink {

	// Image URLs
	final static String img_installed       = "gears_logo.gif";
	final static String img_not_installed   = "gears_logo_grey.gif";
	
	// Tooltips
	final static String title_installed     = "GoogleGears available"; 
	final static String title_not_installed = "Install GoogleGears";
	
	// Href
	final static String href_installed      = "#"; 
	final static String href_not_installed  = "http://gears.google.com/";
	
	public GoogleGears() {
		super();
		setup();
	}
	
	private void setup() {
		boolean status = isGearsInstalled();
		Logger.log("Gears: " + status );
		
		// image
		String imgUrl = status ? img_installed:img_not_installed;
		this.setImgUrl(imgUrl);
		
		// tooltip
		String title = status ? title_installed:title_not_installed; 
		this.setTitle(title);

		// href
		String href = status ? href_installed:href_not_installed; 
		this.setHref(href);
		
	}
	
	private static native boolean isGearsInstalled() /*-{
		try {
			return $wnd.google.gears.factory != null;
		} catch (e) {
			return false;
		}
	}-*/;
	
}//end
