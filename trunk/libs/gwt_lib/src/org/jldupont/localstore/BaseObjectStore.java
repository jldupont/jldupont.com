/**
 * BaseObjectStore
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.localstore;

import org.jldupont.system.JLD_Object;

abstract public class BaseObjectStore 
	extends JLD_Object 
	implements ObjectStoreInterface {
	
	/**
	 * Default TTL for all objects
	 * 1 day (in ms)
	 */
	final static int DEFAULT_TTL = 24*60*60*1000;
	
	public BaseObjectStore(String classe,String id) {
		super(classe,id);
	}
	
	abstract public boolean exists();
	
	abstract public boolean isPersistent() throws LocalStoreException;
	
	abstract public void initialize() throws LocalStoreException;
	
	abstract public void setStorageName(String name);
	
	abstract public void put(LocalObjectStoreInterface obj) throws LocalStoreException;
	
	abstract public void delete(String key) throws LocalStoreException;
	
	abstract public LocalObjectStoreInterface get(String key) throws LocalStoreException;
	
	/**
	 * Gets an object from the localstore making sure
	 *  that its timestamp is within the expiry timeframe i.e.
	 *  timestamp + ttl < currentTime
	 * If ttl=0, then DEFAULT_TTL is used.
	 *  
	 * @param key
	 * @param ttl
	 * @return LocalObjectStoreInterface
	 * @throws LocalStoreException
	 */
	abstract public LocalObjectStoreInterface get(String key, int ttl) throws LocalStoreException;
	
	abstract public boolean containsKey(String key) throws LocalStoreException;
	
	abstract public int headKey(String key) throws LocalStoreException;
	
	abstract public void clear() throws LocalStoreException;
	

}//end 
