/**
 * Liste
 *  Generic list class
 *  
 * @author Jean-Lou Dupont
 */

package org.jldupont.system;

import org.jldupont.system.JLD_Object;

import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONBoolean;
import com.google.gwt.json.client.JSONNull;
import com.google.gwt.json.client.JSONNumber;
import com.google.gwt.json.client.JSONString;
import com.google.gwt.json.client.JSONValue;

import java.util.Iterator;
import java.util.Set;

public class Liste 
	extends JLD_Object 
	implements IteratorEx {

	final static String thisClass = "org.jldupont.system.Liste";

	/**
	 * Main object 
	 */
	protected JSONObject liste = null;
	
	Set<String> keys = null;
	
	Iterator<String> iterator = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public Liste(String classe, String id, boolean recyclable) {
		super(classe,id,recyclable);
		setup();
	}
	public Liste(String classe, String id) {
		super(classe,id);
		setup();
	}
	public Liste(String id) {
		super(thisClass, id);
		setup();
	}
	private void setup() {
		this.liste = new JSONObject();
	}
	/*===================================================================
	 * IteratorEx 
	 ===================================================================*/
	public boolean hasNext() {
		//Logger.logDebug(this.classe+"::Liste.hasNext");
		return this.iterator.hasNext();
	}

	public Object next() {
		
		String key = (String) this.iterator.next();
		//Logger.logDebug(this.classe+"::Liste.next, key["+key+"]");
		
		return key;
		//Object value = this.liste.get(key);
		
		//Logger.logDebug(this.classe+"::Liste.next, value["+value+"]");		
		//return value;
	}
	
	public void remove() {
		this.iterator.remove();
	}
	
	public void reset() {
		//Logger.logDebug(this.classe+"::Liste.reset");		
		this.keys = this.liste.keySet();
		this.iterator = this.keys.iterator();
	}
	
	/*===================================================================
	 * PUBLIC 
	 *  Modeled after the HashMap class
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
	public JSONValue get(String key) {
		return this.liste.get(key);
	}
	/**
	 * Puts a tag in the list
	 * @param key
	 * @param value
	 */
	public void put(String key, String value) {
		this.liste.put(key, new JSONString(value));
	}
	public void put(String key, int value) {
		this.liste.put(key, new JSONNumber(value));
	}
	public void put(String key, long value) {
		this.liste.put(key, new JSONNumber(value));
	}
	public void put(String key, boolean value) {
		this.liste.put(key, JSONBoolean.getInstance(value));
	}
	public void put(String key, JSONValue value) {
		this.liste.put(key, value);
	}
	/**
	 * Removes an entry from the list
	 *  Actually puts a 'null' value instead
	 *  
	 * @param key
	 * @return JSONValue
	 */
	public JSONValue remove(String key) {
		return this.liste.put(key, JSONNull.getInstance());
	}
	/**
	 * toString
	 * Returns a JSON representation of the object.
	 * 
	 * @return String JSON representation
	 */
	public String toString() {
		return this.liste.toString();
	}
	/*===================================================================
	 * ObjectPool
	 *  (recycling) 
	 ===================================================================*/
	/**
	 * TODO find beter implementation
	 */
	public void _clean() {
		super._clean();
		this.liste = null;
		this.liste = new JSONObject();
		this.iterator = null;
		this.keys = null;
	}
	
}//end class
