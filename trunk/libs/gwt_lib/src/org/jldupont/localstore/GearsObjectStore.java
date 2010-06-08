/**
 * GearsObjectStore
 *  Uses GoogleGears database functionality to store objects 
 *  through a 'BLOB' representation
 * 
 * @author Jean-Lou Dupont
 * 
 */
package org.jldupont.localstore;

import org.jldupont.system.Logger;
import org.jldupont.system.Time;

import com.google.gwt.gears.client.database.Database;
import com.google.gwt.gears.client.database.DatabaseException;
import com.google.gwt.gears.client.database.ResultSet;

public class GearsObjectStore 
	extends BaseObjectStore 
	implements ObjectStoreInterface {

	final static String thisClass = "org.jldupont.localstore.GearsObjectStore";
	
	/**
	 * Follows SQLite datatypes
	 */
	final static String thisSchema = "(key TEXT, type TEXT, ts INTEGER(8), data BLOB )";
	
	/**
	 * Storage name
	 *  i.e. database name
	 */
	String storageName = null;
	
	/**
	 * Database instance
	 */
	Database db = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public GearsObjectStore(String classe, String id) {
		super(classe,id);
		setup();
	}
	public GearsObjectStore(String id) {
		super(thisClass,id);
		setup();
	}
	public GearsObjectStore() {
		super(thisClass,"default_id");
		setup();
	}
	private void setup() {
		
	}
	
	/*===================================================================
	 * ObjectStoreInterface 
	 ===================================================================*/
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#exists()
	 */
	public boolean exists() {
		boolean r = isGearsInstalled();
		
		Logger.log(thisClass+".exists: result: " + r );
		
		return r;
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#isPersistent()
	 */
	public boolean isPersistent() {
		return true;
	}
	/**
	 * Initialize the storage facility for a given
	 *  storage instance. The "name" of the storage
	 *  instance must have been set prior.
	 *  @see org.jldupont.localstore.ObjectStoreInterface#initialize()
	 * @throws LocalStoreException 
	 */
	public void initialize() throws LocalStoreException {
		
		Logger.logInfo("GearsObjectStore::initialize");
		
		if (this.db != null)
			return;
		
		if (this.storageName.length() == 0) {
			throw new LocalStoreException("GearsObjectStore.initialize: database name cannot be null");
		}
		
		createDatabase();
	}
	
	public void setStorageName(String name) {
		this.storageName = new String( name );
	}
	/**
	 * Puts/Replaces an object
	 * 
	 * @see org.jldupont.localstore.ObjectStoreInterface#put(LocalObjectStoreInterface)
	 * @throws LocalStoreException 
	 * TODO type check?
	 */
	public void put(LocalObjectStoreInterface obj) throws LocalStoreException {
		
		String key  = obj.getKey();
		String type = obj.getType();
		String ts   = String.valueOf( obj.getTimestamp() );
		String data = obj.getTextRepresentation();
		
		if ( key.length() == 0 )
			throw new LocalStoreException( thisClass+".put: key cannot be null" );
	
		try {
			this.db.execute("INSERT OR REPLACE INTO localstore (key,type,ts,data) VALUES (?,?,?,?)", 
							new String[] {key,type,ts,data} );	
		} catch(DatabaseException e) {
			throw new LocalStoreException( e.getMessage() );
		}
		//Logger.logInfo(thisClass+".put: rowsAffected " + this.db.rowsAffected );
		Logger.logInfo(thisClass+".put: stored key["+key+"] of type["+type+"] and timestamp["+ts+"]");
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#get(String)
	 */
	public LocalObjectStoreInterface get(String key) throws LocalStoreException {
		return get( key, 0 );
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#get(String)
	 */
	public LocalObjectStoreInterface get(String key, int ttl) throws LocalStoreException {
		Logger.logInfo(thisClass+".get: key["+key+"] and ttl["+ttl+"]");
		
		if ( key.length() == 0 )
			throw new LocalStoreException( thisClass+".get: key cannot be null" );

		ResultSet result = null;
		LocalObjectStoreInterface obj  = null;
		String type = null;
		long    ts   = -1;
		String data = null;
		boolean found = false;
		
		try {
			result = this.db.execute(	"SELECT ts,type,data FROM localstore WHERE key=?", 
										new String[] {key} );
			
			//Logger.logDir(result);
			
			// we should only get one element (if any)
			// and that's what we assume
			if (result.isValidRow()) {
				
				Logger.logInfo(thisClass+".get: row is valid");
				//Logger.logDir(result);
				//Logger.logInfo(thisClass+".get: field count: " + result.getFieldCount());
				//Logger.logInfo(thisClass+".get: field name(0): " + result.getFieldName(0));
				
				ts   = result.getFieldAsLong(0);
				Logger.logInfo(thisClass+".get: ts: " + ts );
				
				type = result.getFieldAsString(1);
				Logger.logInfo(thisClass+".get: type: " + type );
				
				data = result.getFieldAsString(2);
				//Logger.logInfo(thisClass+".get: data: " + data );
				
				found = true;
				
			} else {
				Logger.logWarn(thisClass+".get: row is not valid");
			}

			result.close();
			
		} catch(DatabaseException e) {
			Logger.logError(thisClass+".get: DatabaseException raised whilst SELECTing. Msg= " +e.getMessage());
			throw new LocalStoreException( e.getMessage() );
		} catch( Exception e ) {
			Logger.logError(thisClass+".get: Exception raised whilst SELECTing. Msg= " +e.getMessage());
			throw new LocalStoreException( e.getMessage() );
		}

		if (!found) 
			return null;
		
		// simple check on the 'type' field
		if ( type.length() == 0 ) {
			throw new LocalStoreException( thisClass+".get: type field cannot be null" );
		}
		
		// check expiry
		long currentTime = Time.getTime();
		
		if ( ttl == 0 )
			ttl = DEFAULT_TTL;
		
		// correct some bad entries along the way...
		if ( ts == -1 ) {
			Logger.logWarn(thisClass+".get: object with timestamp (-1)" );
			delete( key );
			return null;
		}
		
		// can the object expire?
		if ( ts != 0 ) {
			if ( (ts + ttl) < currentTime ) {
				Logger.logWarn(thisClass+".get: object expired. TS["+ts+"] currentTime["+currentTime+"] and ttl["+ttl+"]" );
				delete(key);
				return null;
			}	
		}
		
		// use the system factory for creating
		// an empty object of the required type
		// The factory returns 'null' if it wasn't
		// able to comply.
		obj = (LocalObjectStoreInterface) org.jldupont.system.Factory.create(type);
		if (obj==null) {
			Logger.logError(thisClass+".get: exception raised whilst using Factory." );
			throw new LocalStoreException( thisClass+".get: cannot create an object of type["+type+"]" );
		}
		
		try {
			obj.createFromTextRepresentation(data);
		} catch(Exception e) {
			Logger.logError(thisClass+".get: exception raised whilst creating representation. Msg= " +e.getMessage());			
			throw new LocalStoreException( thisClass+".get: cannot createFromTextRepresentation, type["+type+"]" );
		}
		
		Logger.log(thisClass+".get: got key["+key+"] of type["+type+"]");		
		return obj;
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#delete
	 */
	public void delete(String key) throws LocalStoreException {
		Logger.logInfo(thisClass+".delete: key["+key+"]");
		
		if ( key.length() == 0 )
			throw new LocalStoreException( thisClass+".delete: key cannot be null" );

		try {
			this.db.execute(	"DELETE FROM localstore WHERE key=?", new String[] {key} );
		} catch(DatabaseException e) {
			throw new LocalStoreException( e.getMessage() );
		} finally {
		}
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#headKey(String)
	 */
	public int headKey(String key) throws LocalStoreException {

		if ( key.length() == 0 ) {
			throw new LocalStoreException( thisClass+".headKey: key cannot be null" );
		}

		ResultSet result = null;
		int ts = -1; //pessimistic...
		
		try {
			result = this.db.execute("SELECT ts FROM localstore WHERE key=?", 
							new String[] {key} );

			// we should only have 1 result
			// and that's what we are assuming
			if (result.isValidRow()) {
				
				ts = result.getFieldAsInt(0);
			}
			
			//clean-up			
			result.close();
			
		} catch(DatabaseException e) {
			throw new LocalStoreException( e.getMessage() );
		}
		
		Logger.log(thisClass+".headKey: key["+key+"] with ts["+ts+"]");
		
		return ts;
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#containsKey(String)
	 */
	public boolean containsKey(String key) throws LocalStoreException {
		
		return headKey( key ) == -1 ? false:true;
		
	}
	/**
	 * @see org.jldupont.localstore.ObjectStoreInterface#clear()
	 */
	public void clear() throws LocalStoreException {
		try {
			this.db.execute("DELETE FROM localstore" );
		} catch(DatabaseException e) {
			throw new LocalStoreException( e.getMessage() );
		}
		Logger.log(thisClass+".clear");		
	}
	/*===================================================================
	 * PROTECTED 
	 ===================================================================*/
	protected void createDatabase() throws LocalStoreException {
		Logger.logInfo("GearsObjectStore::createDatabase");
		
		com.google.gwt.gears.client.Factory factory = com.google.gwt.gears.client.Factory.getInstance();
		
		this.db = factory.createDatabase();
		this.db.open(this.storageName);
		
		try {
			
			this.db.execute("create table if not exists localstore "+thisSchema);
			
		} catch(DatabaseException e) {
			Logger.logError(thisClass+"::createDatabase: exception raised whilst creating table. Msg= " + e.getMessage());
			throw new LocalStoreException( e.getMessage() );
		}
		Logger.logInfo(thisClass+".createDatabase, created database name["+this.storageName+"]");		
	}
	
	/*===================================================================
	 * PRIVATE 
	 ===================================================================*/
	public static native boolean isGearsInstalled() /*-{
		try {
			return $wnd.google.gears.factory != null;
		} catch (e) {
			return false;
		}
	}-*/;
	
	/*===================================================================
	 * ObjectPool 
	 ===================================================================*/
	public void _clean() {
		try {
			this.db.close();
		} catch( Exception e ) {
			Logger.log(thisClass+"._clean: error closing database");
		}
		this.db = null;
		this.storageName = null;
	}
	
}//end class
