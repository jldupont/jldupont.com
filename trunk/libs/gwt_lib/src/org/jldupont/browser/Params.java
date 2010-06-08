/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.browser;

import java.util.HashMap;

import org.jldupont.system.JLD_Object;
import org.jldupont.system.Factory;

/**
 * Resolves the parameter list from the given sources:
 * - the URL parameters
 * - the cookies parameters
 * 
 * Precedence is according to order of above list.
 * 
 */
public class Params 
	extends JLD_Object {
	
	/**
	 * Map of params
	 */
	HashMap<String,String> liste = null;
	
	public Params( ) {
		super( "org.jldupont.browser.Params", "id_browser_params" );
		setRecyclable();
		init();
	}
	/*===================================================================
	 * PUBLIC INTERFACE
	 ===================================================================*/
	
	public boolean containsKey( String key ) {
		return this.liste.containsKey(key);
	}
	
	public String get( String key ) {
		return (String) this.liste.get(key);
	}
	
	public void set( String key, String value ) {
		this.liste.put(key, value);
	}
	/*===================================================================
	 * PROTECTED 
	 ===================================================================*/
	
	/**
	 * Goes through both list at once in precedence 
	 *  and populates result "liste".
	 */
	protected void init() {

		URLParamsList 		uListe = null;
		CookieParamsList	cListe = null;
		Param p = null;
		
		this.liste = new HashMap<String,String>();
		
		uListe = (URLParamsList) Factory.create( "org.jldupont.browser.URLParamsList" );
		cListe = (CookieParamsList) Factory.create( "org.jldupont.browser.CookieParamsList" );
		
		while( uListe.hasNext() ) {
			p = (Param) uListe.next();
			// first occurence wins
			if ( this.liste.containsKey(p.getName()) == false ) {
				this.liste.put( p.getName() , p.getValue() );
			}
		}

		while( cListe.hasNext() ) {
			p = (Param) cListe.next();
			// first occurence wins
			if ( this.liste.containsKey(p.getName()) == false ) {
				this.liste.put( p.getName() , p.getValue() );
			}
		}
		
		// be nice... recycle!
		uListe.recycle();
		cListe.recycle();
	}//
	
} //end class
