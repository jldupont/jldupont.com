/**
 * Base Class for all objects
 *  Provides:
 *  - unique ids
 *  - object pooling / recycling services
 *  - ASYNC operation support (using timers)
 * 
 * @author Jean-Lou Dupont
 *
 */
package org.jldupont.system;

import java.lang.Object;
import org.jldupont.system.Logger;

abstract public class JLD_Object 
	extends Object 
	implements Recycle {

	/**
	 * Instance counter
	 */
	public static long instanceCounter = 0;
	
	/**
	 * Class type
	 */
	public String classe = null;
	
	/**
	 * Identifier
	 */
	String id = null;
	
	/**
	 * UID
	 */
	long uid = 0;
	
	/**
	 * Recyclable capability flag
	 */
	boolean recyclable = false;
	
	/**
	 * Only initialized if used.
	 */
	org.jldupont.system.Timer timer = null;
	
	/**
	 * Operation busy flag
	 */
	boolean isBusy = false;
	
	/**
	 * Name
	 *  useful for key in namespace
	 */
	protected String name = null;
	
	/**
	 * Timestamp (ms since 1/1/1970)
	 */
	long timestamp = -1;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	
	public JLD_Object() {
		super();
		setup(null, null,false);
	}
	
	public JLD_Object( String classe ) {
		super();
		setup(classe,null,false);
	}
	
	public JLD_Object( String classe, String id ) {
		super();		
		setup(classe,id,false);
	}
	
	public JLD_Object( String classe, String id, boolean recyclable ) {
		super();		
		setup(classe,id, recyclable);
	}
	
	protected void setup(String classe, String id, boolean recyclable) {
		instanceCounter++;
		this.uid = instanceCounter;
		this.classe = classe;
		this.id = id;
		this.recyclable = recyclable;
		Logger.logDebug("JLD_Object: creating object class["+classe+"] id["+id+"] uid["+this.uid+"]");		
	}
	/*===================================================================
	 * PUBLIC 
	 ===================================================================*/
	public long getTimestamp() {
		if ( this.timestamp == -1 )
			this.timestamp = Time.getTime();
		return this.timestamp;
	}
	public void setTimestamp(long timestamp) {
		this.timestamp = timestamp;
	}
	/**
	 * getClasse
	 * @return classe
	 */
	public String getClasse() {
		return this.classe;
	}
	/**
	 * getId
	 * @return String
	 */
	public String getId() {
		return this.id;
	}
	
	public void setId(String id) {
		this.id = id;
	}
	
	public String getName() {
		return this.name;
	}
	
	public void setName(String name) {
		this.name = name;
	}
	/*===================================================================
	 * ObjectPool functionality 
	 ===================================================================*/
	
	protected void setRecyclable() {
		this.recyclable = true;
	}
	protected void setRecyclable( boolean state ) {
		this.recyclable = state;
	}
	/**
	 * getRecyclable
	 * @return boolean
	 */
	public boolean getRecyclable() {
		return this.recyclable;
	}
	/**
	 * recycle 
	 *  recycles this object by putting it
	 *  in the ObjectPool
	 */
	public void recycle() {
		
		String exid = _exId();
		String id = new String();
		if ( exid != null )
			id = this.id+ " ["+exid+"]" ;
		else
			id = this.id;
		
		if ( getRecyclable() ) {
			ObjectPool pool = (ObjectPool) Factory.create("ObjectPool", "recycle" );
			Logger.logDebug( "JLD_OBJECT: sending object with id("+id+") to the recycle bin" );
			pool.recycle( this );
		} else {
			Logger.logWarn( "JLD_OBJECT: class (" + getClasse() +") is not recyclable" );
		}
	}

	/*===================================================================
	 * OPERATION functionality 
	 ===================================================================*/
	/**
	 * Used in derived classes.
	 * @param Timeout int (in ms)
	 */
	protected void startOperation(int timeout) {
		
		if ( this.timer == null ) {
			this.timer = new org.jldupont.system.Timer();
			this.timer.setTarget( this );
			this.isBusy = false;
		}

		if ( this.isBusy ) {
			throw new RuntimeException(this.classe+".startOperation : already busy");
		}
		
		this.isBusy = true;
		
		this.timer.schedule( timeout );
	}
	/**
	 * Returns 'busy' status.
	 * @return boolean
	 */
	public boolean getBusy() {
		return this.isBusy;
	}
	/**
	 * TODO override
	 */
	public void timerExpiredEvent() {
		this.isBusy = false;
		Logger.logWarn(this.classe+".timerExpiredEvent: default method called");
	}
	/**
	 * Cancels the current operation
	 */
	public void timerCancel() {
		
		if ( this.timer != null ) {
			this.timer.cancel();
			this.isBusy = false;
			Logger.logWarn(this.classe+".timerCancel: method called");			
		} else {
			Logger.logWarn(this.classe+".timerCancel: NO TIMER active!");	
		}
		
	}
	/*===================================================================
	 * PROTECTED 
	 ===================================================================*/
	/**
	 * Derived classes should handle this
	 *  if special "cleaning" is required when the object is retrieved 
	 *  from the ObjectPool before being handed-off to the requesting
	 *  party.
	 *  
	 * Also, derived classes which use "object composition" 
	 *  should _clean also their composing objects.
	 */
	public void _clean() {
		this.timer = null;
	}
	/**
	 * Used by derived classes to provide
	 * additional "extra" identification information 
	 */
	protected String _exId() {
		return null;
	}
	
}//end class