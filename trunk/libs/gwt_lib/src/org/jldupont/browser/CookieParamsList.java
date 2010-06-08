/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.browser;

import java.util.Collection;
import java.util.Iterator;

import com.google.gwt.user.client.Cookies;


public class CookieParamsList 
	extends ParamsList {

	Collection<String> cookies = null;
	
	public CookieParamsList() {
		super();
	}
	
	protected void extractParams() {

		int j = 0;
		Iterator<String> i = this.cookies.iterator();
		String name = null;
		
		while( i.hasNext() && j<MAX_PARAMS ) {
			
			name = (String) i.next();
			
			this.params[j++] = new Param( name, Cookies.getCookie( name ) );
		}

		this.num_params = j;
	}
	/**
	 * Iterates through all the cookies
	 * 
	 * (non-Javadoc)
	 * @see org.jldupont.browser.ParamsList#getListe()
	 */
	protected void getListe() {
		
		this.cookies = Cookies.getCookieNames();
		
	}
	
}//endclass
