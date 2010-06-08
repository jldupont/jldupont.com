/**
 * TagsManager
 * 
 * @author Jean-Lou Dupont
 * 
 * The client of this class must register itself 
 */
package org.jldupont.delicious;

import org.jldupont.system.JLD_Object;
import org.jldupont.system.Factory;
import org.jldupont.system.Logger;
import org.jldupont.system.Time;
import org.jldupont.localstore.LocalObjectStore;
import org.jldupont.localstore.LocalStoreException;
import org.jldupont.localstore.LocalObjectStoreInterface;
import org.jldupont.web.CallEventListener;
import org.jldupont.web.CallListener;
import org.jldupont.web.CallbackResponseObject;

import com.google.gwt.user.client.Event;

public class TagsManager 
	extends JLD_Object 
	implements CallEventListener, TagsChangedListener {

	/**
	 * Storage name
	 */
	String nameStorage = null;
	
	final static String thisClass = "org.jldupont.delicious.TagsManager";
	
	TagsFetcher tf = null;
	
	String currentUser = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public TagsManager() {
		super( thisClass, "default_id", true );
		setup();
	}
	
	public TagsManager( String id ) {
		super( thisClass, id, true );
		setup();
	}
	
	public TagsManager( String classe, String id, boolean recyclable ) {
		super( classe, id, recyclable );
		setup();		
	}

	protected void setup() {
		
		this.tf = (TagsFetcher) Factory.create("org.jldupont.delicious.TagsFetcher");	
		this.tf.addCallListener(this);
	}
	
	/*===================================================================
	 * PUBLIC INTERFACE 
	 ===================================================================*/
	/**
	 * setStorageName
	 * 
	 * @param name String
	 */
	public void setStorageName(String name) {
		this.nameStorage = new String( name );
	}
	/**
	 * Get with default ttl
	 * If TTL == -1  ==> fetch remote copy
	 * 
	 * @param user
	 * @param ttl
	 * @return TagsList
	 * @throws RuntimeException
	 */
	public TagsList get( String user, int ttl ) throws RuntimeException {
		Logger.logInfo("TagsManager::get: trying localFetch");
		
		this.currentUser = new String( user );
		
		if ( ttl != -1 ) {
			// try the local copy
			TagsList tl = this.localFetch(user, ttl);
			if ( tl != null )
				return tl;
		}
		
		Logger.logInfo("TagsManager::get: trying remoteFetch");
		
		// remoteFetch returns null to signal 'pending'
		return this.remoteFetch(user);
	}
	
	/**
	 * Returns the tag list for a specified user
	 * + Verifies the localstore
	 * + else performs remote fetch
	 * 
	 *  @return TagsList or NULL to signify 'pending'
	 */
	public TagsList get( String user ) throws RuntimeException {

		return get( user, 0 );
	}
	/**
	 * Sets a tag list
	 * 
	 * @param user
	 * @param liste
	 * @param timestamp
	 */
	public void set( String user, TagsList liste, long timestamp ) throws LocalStoreException {
		
		Logger.logInfo("TagsManager::set");
		LocalObjectStore store = (LocalObjectStore) Factory.create("org.jldupont.delicious.LocalObjectStore");
		
		store.setStorageName(this.nameStorage);
		String key = this.generateKey(user);
		
		liste.setName(key);
		
		try {
			store.put( liste );
		} catch (LocalStoreException e) {
			throw e;
		} finally {
			store.recycle();			
		}
	}

	/*===================================================================
	 * CallEventListener 
	 ===================================================================*/
	
	public void addCallListener(CallListener s) {
		this.tf.addCallListener(s);
	}
	public void removeCallListener(CallListener s) {
		this.tf.removeCallListener(s);		
	}

	/*===================================================================
	 * PRIVATE INTERFACE 
	 ===================================================================*/
	/**
	 * @param user
	 */
	private TagsList remoteFetch( String user ) throws RuntimeException {
		
		this.tf.setUser(user);
		
		try {
			this.tf.get();
		} catch(RuntimeException e) {
			throw new RuntimeException("TagsManager::remoteFetch");
		} finally {
			//this.tf.recycle();
		}
		return null;
	}
	/**
	 * localFetch
	 * 
	 * @param user
	 * @return TagsList or NULL
	 */
	private TagsList localFetch( String user, int ttl ) {
		
		LocalObjectStore store = (LocalObjectStore) Factory.create("org.jldupont.delicious.LocalObjectStore", "TagsManager::localFetch" );
		LocalObjectStoreInterface obj = null;
		
		store.setStorageName(this.nameStorage);
		
		String key = this.generateKey(user);
		
		try {
			obj = store.get( key, ttl );
		} catch (LocalStoreException e) {
			Logger.logError("TagsManager::localFetch: LocalStoreException raised. Msg= " + e.getMessage());
			obj = null;
		} catch(Exception e) {
			Logger.logError("TagsManager::localFetch: unknown exception raised. Msg=" + e.getMessage() );
			obj = null;
		} finally {
			store.recycle();
		}
		
		return (TagsList) obj;
	}
	
	/*===================================================================
	 * TagsChangedListener 
	 ===================================================================*/
	public void fireCallEvent(CallbackResponseObject c) {
		
		// don't try to update needlessly
		if ( c.isError() )
			return;
		
		// update our local store
		try {
			set( this.currentUser, (TagsList) c.getResponseObject(), Time.getTime() );
		} catch( LocalStoreException e ) {
			Logger.logError("TagsManager::fireCallEvent: LocalStoreException raised. Msg= " + e.getMessage());
		}
	}
	/**
	 * Declare here so to help derived classes 
	 */
	public void onBrowserEvent(Event event) {
	}
	
	protected String generateKey( String user ) {
		return new String( this.nameStorage + '/' + user );
	}
	
	/**
	 * Recycling
	 */
	public void _clean() {
		super._clean();
		this.nameStorage = null;
		this.currentUser = null;
	}
	
}//end class