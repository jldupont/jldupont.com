/**
 * ObjectPool
 *  Object Recycling facility
 *  
 * @author Jean-Lou Dupont
 * 
 * TODO implement limits e.g. limit the number of instance of class X
 * 
 */
package org.jldupont.system;

import org.jldupont.system.Logger;
import java.util.HashMap;

/**
 * @author Jean-Lou Dupont
 *
 */
public class ObjectPool 
	extends JLD_Object {

	/**
	 * Recycle bin
	 */
	static HashMap<String,Object> bin = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	/**
	 * Borg pattern
	 */
	public ObjectPool() {
		super( "ObjectPool" );
		
		if ( bin == null )
			bin = new HashMap<String, Object>();
	}
	
	/**
	 * recycle 
	 * @param obj
	 */
	public void recycle( JLD_Object obj ) {
		
		String classe = obj.getClasse();
		
		// do we already have an object of this class?
		if ( bin.containsKey(classe) )
			return;
		
		bin.put( classe, obj );
		
		Logger.logInfo("OBJECTPOOL: recycling an object of class("+ classe +") and id("+obj.getId()+")" );		
	}
	
	public JLD_Object get( String classe ) {
		
		JLD_Object obj;
		
		if ( bin.containsKey(classe) ) {
			// get it
			obj = (JLD_Object) bin.get( classe );
			
			// remove from bin
			bin.remove( classe );
			
			return obj;
		}
		
		return null;
	}
	
}//endclass
