/**
 * PostsList
 * Delicious
 * 
 * @author Jean-Lou Dupont
 * 
 * Fields:
 * 	u (url)
 *  d (description)
 *  n (note)
 *  t (tags)
 */

package org.jldupont.delicious;

import java.util.Iterator;
import java.util.Set;

import org.jldupont.localstore.StorableListe;

import com.google.gwt.json.client.JSONObject;
import com.google.gwt.json.client.JSONValue;

public class PostsList 
	extends StorableListe {

	final static String thisClass = "org.jldupont.delicious.PostsList";
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public PostsList() {
		super(thisClass, "default_id", true);
		setup();
	}
	public PostsList(String classe, String id) {
		super(classe,id,true);
		setup();
	}
	public PostsList(String id) {
		super(thisClass, id,true);
		setup();
	}
	private void setup() {
	}
	public PostsList( JSONObject o ) {
		super(thisClass, "default_id", true);
		putAll( o );
	}
	/*===================================================================
	 * PUBLIC 
	 ===================================================================*/
	public void putAll( JSONObject obj ) {

		Set<String> set = obj.keySet();
		Iterator<String> i = set.iterator();
		
		while( i.hasNext() ) {
			String key = (String) i.next();
			JSONValue value = obj.get( key );
			this.put(key, value);
		}

	}
	public String toString() {
		return super.toString();
	}
	/*===================================================================
	 * ObjectPool 
	 ===================================================================*/
	public void _clean() {
		super._clean();
	}
	
}//end class
