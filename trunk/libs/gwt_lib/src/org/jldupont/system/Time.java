/**
 * Time
 * @author Jean-Lou Dupont
 */
package org.jldupont.system;

public class Time {

	public static native int getTime() /*-{
		var currentTime = new Date();
		return currentTime.getTime();
	}-*/;

	/**
	 * Converts any number of date formats to
	 * long integer since 1/1/1970
	 * 
	 * @param input String
	 * @return
	 */
	public static native int parse(String input) /*-{
		return Date.parse( input );
	}-*/;
	
	public static native int getRFC1123Time(int ts) /*-{
		return Date.toUTCString( ts );
	}-*/;
	
	
}//end
