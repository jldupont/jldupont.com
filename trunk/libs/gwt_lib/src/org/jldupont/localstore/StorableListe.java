/**
 * StorableListe
 *  List that can be easily locally stored
 *  
 * @author Jean-Lou Dupont
 */
package org.jldupont.localstore;

import org.jldupont.system.Liste;
import org.jldupont.system.Logger;

import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONParser;

public class StorableListe 
	extends Liste 
	implements LocalObjectStoreInterface {

	final static String thisClass = "org.jldupont.localstore.StorableListe";

	/*===================================================================
	 * CONSTRUCTORS
	 ===================================================================*/
	public StorableListe(String classe, String id, boolean recyclable) {
		super(classe,id, true);
	}
	public StorableListe(String id) {
		super(thisClass, id, true);
	}
	
	/*===================================================================
	 * PUBLIC - LocalObjectStoreInterface
	 ===================================================================*/
	/**
	 * Meant to be used as "unique" key in the database
	 */
	public String getKey() {
		return getName();
	}
	
	/**
	 * Class type of the object
	 *  Used to "create" the object from its representation
	 */
	public String getType() {
		return getClasse();
	}
	
	/**
	 * Object representation in TEXT format
	 *  The object's true representation is opaque to the LocalStore 
	 */
	public String getTextRepresentation() {
		Logger.logDebug(thisClass+".getTextRepresentation");
		return this.liste.toString();		
	}
	
	/**
	 * Once an object of the target type is instantiated,
	 *  this method can be used to initialize the object
	 *  according to the representation. 
	 *  
	 *  TODO check if the 'null' values get discarded
	 */
	public void createFromTextRepresentation(String s) {
		Logger.logDebug(thisClass+".createFromTextRepresentation");		
		this.liste = (JSONObject) JSONParser.parse( s );
	}
	
	/*===================================================================
	 * PUBLIC - ObjectStoreInterface
	 ===================================================================*/
	public void _clean() {
		super._clean();
	}
	
}//end
