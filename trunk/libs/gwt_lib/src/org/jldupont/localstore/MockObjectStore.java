/**
 * MockObjectStore
 *  Used when no other appropriate local database is available
 *  
 * @author Jean-Lou Dupont
 */
package org.jldupont.localstore;

import org.jldupont.system.Logger;

public class MockObjectStore 
	extends BaseObjectStore 
	implements ObjectStoreInterface {
	
	final static String thisClass = "org.jldupont.localstore.MockObjectStore";

	/**==================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	
	public MockObjectStore(String classe, String id) {
		super(classe,id);
	}
	public MockObjectStore(String id) {
		super(thisClass,id);
	}
	public MockObjectStore() {
		super(thisClass,"default_id");
	}
	
	/**==================================================================
	 * @see org.jldupont.localstore.ObjectStoreInterface 
	 ===================================================================*/
	public boolean exists() {
		return true;
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#isPersistent()
	 */
	public boolean isPersistent() {
		return false;
	}
	/**
	 * not much todo :-)
	 */
	public void initialize() {
		Logger.logInfo(thisClass+".initialize: called");
	}
	public void setStorageName(String name) {
		Logger.logInfo(thisClass+".setStorageName: called");		
	}
	
	public void put(LocalObjectStoreInterface obj) {
		Logger.logInfo(thisClass+".put: called");		
	}
	
	public LocalObjectStoreInterface get(String key) {
		Logger.logInfo(thisClass+".get: called");		
		return null;
	}
	public LocalObjectStoreInterface get(String key, int ttl) {
		Logger.logInfo(thisClass+".get: called");		
		return null;
	}
	
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#delete
	 */
	public void delete(String key) throws LocalStoreException {
		Logger.logInfo(thisClass+".delete: called");
	}
	
	public int headKey(String key) {
		Logger.logInfo(thisClass+".headKey: called");		
		return -1;
	}
	
	public boolean containsKey(String key) {
		Logger.logInfo(thisClass+".containsKey: called");	
		return false;
	}
	
	public void clear() {
		Logger.log(thisClass+".clear: called");		
	}
	
}//end 
