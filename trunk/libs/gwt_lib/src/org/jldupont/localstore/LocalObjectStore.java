/**
 * LocalObjectStore
 * 
 * @author Jean-Lou Dupont
 * 
 *  Uses the most "appropriate" local storage.
 *  Works with GoogleGears only at the moment.
 */
/**
 * Schema:
 *  key TEXT , type TEXT, timestamp INTEGER, data BLOB
 *  
 *  key:       text string with / denoting hierarchy
 *  type:      Java object type
 *  timestamp: milliseconds since 1/1/1970
 *  data:      object representation
 */
package org.jldupont.localstore;

import org.jldupont.system.JLD_Object;
import org.jldupont.system.Logger;
import org.jldupont.system.Factory;

public class LocalObjectStore 
	extends JLD_Object 
	implements ObjectStoreInterface {
	
	final static String thisClass = "org.jldupont.localstore.LocalObjectStore";
	
	/**
	 * Database name
	 */
	String storageName = null;
	
	/**
	 * Initialization status
	 *  Lazy initialization strategy
	 */
	boolean initialized = false;
	
	/**
	 * Storage
	 */
	BaseObjectStore store = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public LocalObjectStore(String classe, String id) {
		super(classe,id,true);
		setup();
	}
	public LocalObjectStore(String id) {
		super(thisClass,id,true);
		setup();
	}
	/**
	 * setup
	 *  Only GoogleGears support at the moment.
	 */
	private void setup() {
		
		// try GoogleGears...
		//this.store = new GearsObjectStore(getId());
		this.store = (BaseObjectStore) Factory.create("org.jldupont.localstore.GearsObjectStore", getId() );
		if (this.store.exists())
			return;
		
		Logger.logDebug("LocalObjectStore::setup: fallback to mock");
		
		// fallback to Mock ...
		this.store = new MockObjectStore(getId());
	}
	
	/*===================================================================
	 * PUBLIC - ObjectStoreInterface
	 ===================================================================*/
	/**
	 * We always have Mock database ...
	 */
	public boolean exists() {
		return true;
	}
	/**
	 * @throws LocalStoreException 
	 * @see org.jldupont.localstore.ObjectStoreInterface#isPersistent()
	 */
	public boolean isPersistent() throws LocalStoreException {
		if (this.store == null) {
			throw new LocalStoreException(thisClass+".initialize: storage name cannot be null");
		}
		return this.store.isPersistent();
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#setStorageName(String)
	 */
	public void setStorageName(String name) {
		this.storageName = new String( name );
		Logger.logInfo("LocalObjectStore::setStorageName: name=" + this.storageName );
	}
	/**
	 * This method shouldn't be called by client's
	 *  of this class.
	 */
	public void initialize() throws LocalStoreException {
		
		if (this.initialized)
			return;
		
		Logger.logInfo( "LocalObjectStore::initialize" );
		
		if ( this.storageName == null )
			throw new LocalStoreException(thisClass+".initialize: storage name cannot be null");
		if (this.storageName.length() == 0)
			throw new LocalStoreException(thisClass+".initialize: storage name cannot be empty");
		
		this.store.setStorageName(this.storageName);
		this.store.initialize();
		
		this.initialized = true;		
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#put(LocalObjectStoreInterface)
	 */
	public void put(LocalObjectStoreInterface obj) throws LocalStoreException {
		Logger.logInfo( "LocalObjectStore::put" );
		this.initialize();
		this.store.put(obj);
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#get(String)
	 */
	public LocalObjectStoreInterface get(String key) throws LocalStoreException {
		Logger.logInfo( "LocalObjectStore::get" );		
		this.initialize();
		
		return this.store.get(key);
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#get(String)
	 */
	public LocalObjectStoreInterface get(String key, int ttl) throws LocalStoreException {
		Logger.logInfo( "LocalObjectStore::get" );		
		this.initialize();
		
		return this.store.get(key,ttl);
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#delete
	 */
	public void delete(String key) throws LocalStoreException {
		Logger.logInfo(thisClass+".delete: key["+key+"]");
		this.initialize();
		this.store.delete(key);
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#containsKey(String)
	 */
	public boolean containsKey(String key) throws LocalStoreException {
		Logger.logInfo( "LocalObjectStore::containsKey" );		
		this.initialize();
		
		return this.store.containsKey(key);
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#headKey(String)
	 */
	public int headKey(String key) throws LocalStoreException{
		Logger.logInfo( "LocalObjectStore::headKey" );
		return this.store.headKey(key);
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#clear()
	 */
	public void clear() throws LocalStoreException {
		this.store.clear();
	}
	/*===================================================================
	 * ObjectPool
	 ===================================================================*/
	public void _clean() {
		super._clean();
		this.store._clean();
		this.name = null;
	}
	
}//end class
