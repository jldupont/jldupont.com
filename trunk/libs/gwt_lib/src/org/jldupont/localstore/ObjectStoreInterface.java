/**
 * LocalObjectStoreInterface
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.localstore;

import org.jldupont.localstore.LocalObjectStoreInterface;

public interface ObjectStoreInterface {

	/*===================================================================
	 * Modeled after the HashMap class 
	 ===================================================================*/
	/**
	 * Returns the availability status of the storage facility
	 *  and *not* a particular instance of storage.
	 */
	public boolean exists();
	
	/**
	 * Indicates whether the storage is persistent across
	 *  browser sessions.
	 *  
	 * @return boolean
	 * @throws LocalStoreException
	 */
	public boolean isPersistent() throws LocalStoreException;
	
	/**
	 * Used to name the storage instance
	 * 
	 * @param name
	 */
	public void setStorageName(String name);

	/**
	 * Initialize the storage facility for a given
	 *  storage instance. The "name" of the storage
	 *  instance must have been set prior.
	 */
	abstract void initialize() throws LocalStoreException;
	
	/**
	 * Puts an object in the store
	 * @param obj
	 */
	public void put(LocalObjectStoreInterface obj) throws LocalStoreException;
	
	/**
	 * Gets an object from the store
	 * @param key
	 * @return object
	 * @return null if none
	 */
	public LocalObjectStoreInterface get(String key) throws LocalStoreException;

	/**
	 * Gets an object from the store
	 * - if TTL == 0  ==> use DEFAULT_TTL
	 * 
	 * @param key
	 * @param ttl
	 * @return object
	 * @return null if none
	 */
	public LocalObjectStoreInterface get(String key, int ttl) throws LocalStoreException;
	
	/**
	 * Verifies if an object is in the store
	 * @param key
	 * @return boolean
	 */
	public boolean containsKey(String key) throws LocalStoreException;

	/**
	 * Deletes the specified key
	 * 
	 * @param key
	 * @throws LocalStoreException
	 */
	public void delete(String key) throws LocalStoreException;
	
	/**
	 * Returns the timestamp information
	 * 
	 * @param key
	 * @return int Timestamp (ms since 1/1/1970)
	 * @return -1 if key is not found
	 */
	public int headKey(String key) throws LocalStoreException;
	
	/**
	 * Erases all elements from the store
	 */
	public void clear() throws LocalStoreException;
	
}// end
