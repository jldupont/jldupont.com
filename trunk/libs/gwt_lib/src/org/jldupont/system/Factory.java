/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.system;

import org.jldupont.browser.URLParamsList;
import org.jldupont.browser.CookieParamsList;
import org.jldupont.delicious.TagsFetcher;
/*
import com.google.gwt.core.client.GWT;
import org.jldupont.delicious.TagsList;
import org.jldupont.delicious.TagsManager;
import org.jldupont.delicious.TagsManagerCommand;
import org.jldupont.web.JSONcallback;
import org.jldupont.web.JSONcall;
import org.jldupont.command.CommandStatus;
import org.jldupont.localstore.LocalObjectStore;
import org.jldupont.localstore.GearsObjectStore;
*/
import java.lang.Object;
import java.util.HashMap;

public class Factory 
	extends Object {

	final static String thisClass = "org.jldupont.system.Factory";
	
	static HashMap<String,String> map = new HashMap<String,String>();
	
	/**
	 * Maps a source class to a destination class
	 * Used to substitute a class for another.
	 * Useful for testing with mock objects.
	 * @param src String
	 * @param dest String
	 */
	public static void map( String src, String dest ) {
		map.put( src, dest );
	}
	/**
	 * Removes a mapping
	 * @param src
	 */
	public static void unmap( String src ) {
		map.remove(src);
	}
	/**
	 * create
	 * 
	 * @param className
	 * @return JLD_Object
	 */
	public static JLD_Object create( String className ) {
		return create( className, null );
	}
	
	/**
	 * create
	 * 
	 * @param className
	 * @return JLD_Object
	 */
	public static JLD_Object create( String className, String id ) {

		ObjectPool pool = new ObjectPool();
		JLD_Object obj;
		String rid = null;
		
		//check the remapping table
		// ------------------------
		String dest = (String) map.get(className);
		if ( dest !=null )
			className = dest;
		
		// check the ObjectPool first
		obj = pool.get( className );
		if ( obj != null ) {
			rid = obj.getId();
			Logger.logDebug( "FACTORY: retrieved object of class: " + className + " id("+rid+")" +" asked id("+id+")" );
			
			// clean-up!
			obj._clean();
			
			return obj;
		}
		
		// no luck, create one from scratch
		Logger.logDebug( "FACTORY: creating object of class: " + className );		
		
		return createInstance( className, id );
	}
	
	/**
	 * createInstance
	 * 
	 * TODO implement this a better way!! 
	 * @param className
	 * @return
	 */
	protected static JLD_Object createInstance( String className, String id ) {

		//return (JLD_Object) ( GWT.create( org.jldupont.system.JLD_Object.class ));
		
		// org.jldupont.system
		// ===================
		if ( className == "org.jldupont.system.StdListe" ) {
			return (JLD_Object) new StdListe(id);
		}
	
		/**
		 * @see com.jldupont.system.ObjectPool
		 */
		if ( className == "ObjectPool" ) {
			return (JLD_Object) new ObjectPool();
		}
		
		// org.jldupont.command
		// ===================
//		if ( className == "org.jldupont.command.CommandStatus" )
//			return (JLD_Object) new org.jldupont.command.CommandStatus( );

		// org.jldupont.widget_commands
		// ===================
		if ( className == "org.jldupont.widget_commands.ListeUpdaterCommand" )
			return (JLD_Object) new org.jldupont.widget_commands.ListeUpdaterCommand( );
		
		// org.jldupont.browser
		// =======================
		/**
		 * @see org.jldupont.browser.URLParamsList
		 */
		if ( className == "org.jldupont.browser.URLParamsList" ) {
			return (JLD_Object) new URLParamsList( );
		}
		/**
		 * @see org.jldupont.browser.CookieParamsList
		 */
		if ( className == "org.jldupont.browser.CookieParamsList" ) {
			return (JLD_Object) new CookieParamsList( );
		}
		
		// org.jldupont.web
		// =======================
		if ( className == "org.jldupont.web.JSONcall" ) {
			return (JLD_Object) new org.jldupont.web.JSONcall(id);
		}
		if ( className == "org.jldupont.web.JSONcallback" ) {
			return (JLD_Object) new org.jldupont.web.JSONcallback(id);
		}
		
		// org.jldupont.delicious
		// =======================
		/**
		 * @see org.jldupont.delicious.TagsFetcher
		 */
		if ( className == "org.jldupont.delicious.TagsFetcher" ) {
			return (JLD_Object) new TagsFetcher( id );
		}
		
		if ( className == "org.jldupont.delicious.mocks.TagsFetcher" ) {
			return (JLD_Object) new org.jldupont.delicious.mocks.TagsFetcher( id );
		}
		
		if ( className == "org.jldupont.delicious.TagsList" ) {
			return (JLD_Object) new org.jldupont.delicious.TagsList(id);
		}
		if ( className == "org.jldupont.delicious.TagsManager" ) {
			return (JLD_Object) new org.jldupont.delicious.TagsManager(id);
		}
		if ( className == "org.jldupont.delicious.TagsManagerCommand" ) {
			return (JLD_Object) new org.jldupont.delicious.TagsManagerCommand(id);
		}
		
		// org.jldupont.localstore
		// =======================
		if ( className == "org.jldupont.delicious.LocalObjectStore" )
			return (JLD_Object) new org.jldupont.localstore.LocalObjectStore(id);

		if ( className == "org.jldupont.localstore.GearsObjectStore" )
			return (JLD_Object) new org.jldupont.localstore.GearsObjectStore(id);
		
		throw new LoggableRuntimeException( thisClass+".createInstance: CREATING INSTANCE OF CLASS: " + className );
	}
	
}//end
