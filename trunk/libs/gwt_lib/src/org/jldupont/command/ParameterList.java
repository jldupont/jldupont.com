/**
 * ParameterList
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.command;

import org.jldupont.system.StdListe;
import org.jldupont.system.Logger;

public class ParameterList 
	extends StdListe 
	implements CommandParameters {

	final static String thisClass = "org.jldupont.command.ParameterList";
	
	/*===================================================================
	 * CONSTRUCTORS  
	 ===================================================================*/
	public ParameterList() {
		super( thisClass, "default_id" );
	}
	
	public ParameterList(String id ) {
		super( thisClass, id );
	}
	
	/*===================================================================
	 * CommandParameters interface
	 ===================================================================*/
	
	public Object getParameter( String key ) {
		Logger.logDebug(this.classe+".getParameter: getting key["+key+"]");		
		return this.get(key);
	}
	
	public void   setParameter( String key, Object o ) {
		Logger.logDebug(this.classe+".setParameter: setting key["+key+"]");
		this.put(key, o);
	}

	
	/*===================================================================
	 * RECYCLING  
	 ===================================================================*/
	public void _clean() {
		super._clean();
	}
	
}//endclass
