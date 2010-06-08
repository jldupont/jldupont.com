/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.browser;

import java.lang.Object;

public class Param 
	extends Object {
	
	String name;
	String value;
	
	public Param( String name, String value ) {
		this.name = new String( name );
		this.value = new String( value );
	}
	
	public String getName() {
		return this.name;
	}
	
	public String getValue() {
		return this.value;
	}
}//endclass