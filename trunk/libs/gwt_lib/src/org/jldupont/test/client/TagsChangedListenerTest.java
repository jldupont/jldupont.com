/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.test.client;

//import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.Event;

import com.google.gwt.user.client.ui.ListBox;

import org.jldupont.delicious.TagsChangedListener;
import org.jldupont.delicious.TagsList;
import org.jldupont.web.CallbackResponseObject;
import org.jldupont.system.Logger;


public class TagsChangedListenerTest

	implements TagsChangedListener {

	ListBox lb = null;
	
	public TagsChangedListenerTest( ListBox lb ) {
		this.lb = lb;
	}
	
	public void fireCallEvent(CallbackResponseObject c) {
		//Window.alert("TagsChangedListenerTest.fireCallEvent: called! " + c.getJSONObject().toString() );
		TagsList o = (TagsList) c.getResponseObject();
		if ( o == null ) {
			Logger.log("TagsChangedListenerTest::fireCallEvent: TagsList == null");
			return;
		}
		o.reset();
		
		while( o.hasNext() ) {
			String key = (String) o.next();
			//JSONValue value = (JSONValue) o.get( key );
			//Logger.log("key: "+key+" value: "+value );
			this.lb.addItem(key);
		}
	}
	
	/**
	 * Declare here so to help derived classes 
	 */
	public void onBrowserEvent(Event event) {
	}
	
}//end class
