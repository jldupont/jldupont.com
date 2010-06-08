/**
 * LocalObjectStoreInterface
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.localstore;

import java.lang.String;

public interface LocalObjectStoreInterface {

	/**
	 * Meant to be used as "unique" key in the database
	 */
	public String getKey();
	
	/**
	 * Class type of the object
	 *  Used to "create" the object from its representation
	 */
	public String getType();
	
	/**
	 * Timestamp (ms from 1/1/1970)
	 */
	public long getTimestamp();
	
	/**
	 * Object representation in TEXT format
	 *  The object's true representation is opaque to the LocalStore 
	 */
	public String getTextRepresentation();
	
	/**
	 * Once an object of the target type is instantiated,
	 *  this method can be used to initialize the object
	 *  according to the representation. 
	 */
	public void createFromTextRepresentation(String s);

}//end class