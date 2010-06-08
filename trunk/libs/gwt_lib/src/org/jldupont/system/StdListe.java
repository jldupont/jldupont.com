/**
 * StdListe
 *  Standard list class
 *  
 * @author Jean-Lou Dupont
 */
package org.jldupont.system;

import org.jldupont.system.JLD_Object;
import org.jldupont.system.Logger;
import org.jldupont.system.IteratorEx;

import java.util.HashMap;
import java.util.Set;
import java.util.Iterator;

public class StdListe 
	extends JLD_Object 
	implements IteratorEx {

	final static String thisClass = "org.jldupont.command.StdListe";
	
	HashMap<String,Object> liste = null;
	
	Set<String> keys = null;
	
	Iterator<String> keysIterator = null;
	
	/*===================================================================
	 * CONSTRUCTORS  
	 ===================================================================*/
	
	public StdListe(String classe, String id ) {
		super( classe, id, true );
		setup();		
	}
	
	public StdListe( String id ) {
		super( thisClass, id, true );
		setup();
	}
	
	private void setup() {
	
		this.liste = new HashMap<String,Object>();
	}
	
	/*===================================================================
	 * PUBLIC  
	 ===================================================================*/
	public boolean isEmpty() {
		return this.liste.size()!=0;
	}
	/**
	 * containsKey
	 */
	public boolean containsKey(String key) {
		return this.liste.containsKey(key);
	}
	
	/**
	 * Getter
	 * @param key
	 * @return Object
	 */
	public Object get(String key) {
		assert( this.liste != null );
		Logger.logDebug(this.classe+"::StdListe.get: key["+key+"]");
		return this.liste.get(key);
	}
	/**
	 * Setter
	 * @param key
	 * @param value
	 */
	public void put(String key, Object value) {
		assert( this.liste != null );		
		Logger.logDebug(this.classe+"::StdListe.set: key["+key+"]");		
		this.liste.put(key, value);
	}
	/**
	 * Clear
	 */
	public void clear() {
		Logger.logDebug(this.classe+"::StdListe.clear");
		this.liste.clear();
	}
	/**
	 * Remove
	 * 
	 * @param key
	 */
	public void remove(String key) {
		Logger.logDebug(this.classe+"::StdListe.remove: key["+key+"]");		
		this.liste.remove(key);
	}
	/*===================================================================
	 * IteratorEx interface  
	 ===================================================================*/
	
	public boolean hasNext() {
		Logger.logDebug(this.classe+"::StdListe.hasNext");		
		return this.keysIterator.hasNext();
	}
	public Object next() {
		
		String key = (String) this.keysIterator.next();
		Logger.logDebug(this.classe+"::StdListe.next: key["+key+"]");
		
		Object value = this.liste.get(key);
		
		Logger.logDebug(this.classe+"::StdListe.next: value["+value+"]");		
		return value;
	}
	public void remove() {
		//TODO
	}
	/**
	 * Reset the iterator interface
	 *  Addition to the standard Iterator interface.
	 *  This method shall be called *before* using the Iterator interface
	 */
	public void reset() {
		this.keys = this.liste.keySet();
		this.keysIterator = this.keys.iterator();
	}
	/*===================================================================
	 * PROTECTED - helpers for Iterator interface  
	 ===================================================================*/
	
	
	/*===================================================================
	 * RECYCLING  
	 ===================================================================*/
	public void _clean() {
		super._clean();
		this.keys = null;
		this.keysIterator = null;
		this.liste.clear();
		
	}
	
}//endclass
