/**
 * TagsManagerCommand
 * 
 * @author Jean-Lou Dupont
 * 
 * @example
 * 	// need to set storagename used for localstore
 *  obj.setStorageName("database-name");
 *  
 *  // set the 'next' pointer [optional]
 *  obj.setNext( CommandInterface me, CommandInterface next );
 *  
 *  // call run() method with appropriate parameter
 *  // which include:
 *  //  user: username
 *  //  ttl:  time-to-live for localstore
 *  obj.run(CommandParameters p);
 */
package org.jldupont.delicious;

import com.google.gwt.user.client.Event;

import org.jldupont.command.Command;
import org.jldupont.command.CommandStatus;

import org.jldupont.system.Factory;
import org.jldupont.system.Logger;
import org.jldupont.system.LoggableRuntimeException;

import org.jldupont.web.CallbackResponseObject;

public class TagsManagerCommand 
	extends Command 
	implements TagsChangedListener {

	final static String thisClass = "org.jldupont.delicious.TagsManagerCommand";
	
	TagsManager manager = null;
	
	/*===================================================================
	 * Constructor 
	 ===================================================================*/
	public TagsManagerCommand(String id) {
		super( thisClass, id, true );
		setup();
	}
	public TagsManagerCommand() {
		super( thisClass, "default_id", true );
		setup();
	}
	
	private void setup() {
		this.manager = (TagsManager) Factory.create( "org.jldupont.delicious.TagsManager" );

		// set listening interface
		this.manager.addCallListener(this);
	}
	/*===================================================================
	 * CONFIGURATION - must be set prior to using an instance
	 ===================================================================*/
	/**
	 * @see org.jldupont.delicious.TagsManager#setStorageName(String)
	 */
	public void setStorageName(String name) {
		this.manager.setStorageName(name);
	}
	
	/*===================================================================
	 * Command interface
	 ===================================================================*/
	
	protected void _onError() {
		// TODO Auto-generated method stub
		// not much todo...
	}

	protected void _onPending() {
		// TODO Auto-generated method stub
		// not much todo...
	}
	
	public void setParameterName( String paramName ) {
		//nothing todo
	}

	protected CommandStatus _run( ) throws RuntimeException {
		
		// extract 'user' parameter
		String user = (String) this.param.getParameter("user");
		if ( user == null )
			throw new LoggableRuntimeException( "TagsManagerCommand::_run: parameter 'user' not found" );

		// extract the 'ttl' parameter
		Object o = this.param.getParameter("ttl");
		if ( o == null )
			throw new LoggableRuntimeException( "TagsManagerCommand::_run: parameter 'ttl' not found" );
		
		// Integer are passed in string format...
		int ttl = Integer.parseInt( (String) o);
		
		TagsList tl = null;
		
		// place the request
		try {
			tl = this.manager.get( user, ttl );
		} catch(RuntimeException e) {
			throw new LoggableRuntimeException( "TagsManagerCommand::_run: " + e.getMessage() );			
		} finally {
			this._onError();
		}
		
		// completed right away?  the tags must have been available
		// in the localstore then...
		if ( tl != null ) {
			// queue the result for the chain's benefit
			Logger.logInfo(this.classe+"._run: setting parameterList object with list of tags");
			this.param.setParameter( "taglist", tl );
			return new CommandStatus( /*OK*/ );
		}

		return new CommandStatus( true /*pending*/ );
	}

	/**
	 * @see org.jldupont.command.CommandInterface
	 */
	public void setStatus(CommandStatus s) {
		this.propagateCommandStatus(s);

	}
	/*===================================================================
	 * TagsChangedListener interface 
	 ===================================================================*/
	
	public void fireCallEvent(CallbackResponseObject c) {
		
		// timeout?  => error
		if (c.isError()) {
			this.propagateCommandStatus( new CommandStatus( CommandStatus.TIMEOUT, "TagsFetcher: timeout" ) );
			return;
		}
	
		//TODO what about empty list?
		Logger.logInfo(this.classe+".fireCallEvent: setting parameterList object with 'tagslist' ");
		this.param.setParameter("taglist", c.getResponseObject());
			
		// success? continue chain
		this.runNext();
	}//
	
	/**
	 * Declare here so to help derived classes 
	 */
	public void onBrowserEvent(Event event) {
		// NOTHING todo... i think.
	}
	
	/*===================================================================
	 * Recycling 
	 ===================================================================*/
	public void _clean() {
		super._clean();
	}
	
}//end class
