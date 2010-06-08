/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.web;

import com.google.gwt.core.client.JavaScriptObject;
import org.jldupont.system.JLD_Object;
import org.jldupont.system.Logger;

public class BaseCallback 
	extends JLD_Object {

	/**
	 * Instance Counter
	 */
	static int instanceCounter = 0;
	
	/**
	 * Callback fired status
	 */
	boolean callbackFired = false;

	/**
	 * Target for the callback
	 */
	BaseCallbackEvent target=null;
	
	/**
	 * Current callback id
	 */
	int currentCallback = -1;
	
	/*===================================================================
	 * CONSTRUCTORS
	 ===================================================================*/
	public BaseCallback(String classe, String id) {
		super(classe, id);
		setup();
	}
	private void setup() {
		_clean();
	}
	/*===================================================================
	 * PUBLIC
	 ===================================================================*/
	/**
	 * create
	 */
	public void create() {

		if ( this.target == null ) {
			throw new RuntimeException( this.classe + ".create: target is null" );
		}
		
		if (this.currentCallback > 0) {
			deleteCallback( this.currentCallback );
		}
		
		this.callbackFired = false;
		this.currentCallback = instanceCounter++;
		Logger.log(this.classe+".create: creating callback name["+getCallbackName()+"]");
		this.createCallback( this.currentCallback );
	}
	/**
	 * 
	 * @param target
	 */
	public void setTarget(BaseCallbackEvent target) {
		this.target = target;
	}
	/**
	 * 
	 * @return String
	 */
	public String getCallbackName() {
		return "BaseCallbackFnc"+String.valueOf(this.currentCallback)+".handler";
	}
	/*===================================================================
	 * CALLBACK
	 ===================================================================*/
	public void callback(int id, JavaScriptObject obj) {
		
		this.callbackFired = true;
		Logger.log(this.classe+": callback called! id["+id+"]");
		
		// fire-off the event
		// @see 
		this.target.handleCallbackEvent(id, obj);
	}
	/**
	 * BLACK MAGIC at work!!!
	 *  this$static is a parameter passed to this method as the "this" pointer
	 *  of the current object. Hopefully, this won't change in future versions...
	 *  I had to look at the generated "detailed" code to figure this one out.
	 */
	protected native void createCallback(int id) /*-{

		var fncName = "BaseCallbackFnc"+id;
		eval( "var obj = $wnd."+fncName+" = { id: '" + id + "' };"	);
		obj.handler = function(jsObj) {
			this$static.@org.jldupont.web.BaseCallback::callback(ILcom/google/gwt/core/client/JavaScriptObject;)(this.id, jsObj);
		};
		
	}-*/;
	/**
	 * Deletes a callback
	 */
	protected native void deleteCallback(int id) /*-{
		var fncName = "BaseCallbackFnc"+id;	
		eval( "$wnd."+fncName+" = null;" );
	}-*/;
	
	/*===================================================================
	 * ObjectPool
	 ===================================================================*/
	public void _clean() {
		
		super._clean();
		
		if (this.currentCallback > 0) {
			deleteCallback( this.currentCallback );
		}
		this.currentCallback = -1;
		this.callbackFired = false;
		this.target = null;
	}
	
}//end class
