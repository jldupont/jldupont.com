/**
 * ListeUpdaterCommand
 * 
 * @author Jean-Lou Dupont
 * 
 * @example
 *  // need to set the companion widget
 * 	obj.setWidget( w );
 * 
 *  // need to set the parameter to look for
 *  obj.setParameterName( "name-here" );
 *  
 *  // set the 'next' pointer [optional]
 *  obj.setNext( CommandInterface me, CommandInterface next );
 *  
 */
package org.jldupont.widget_commands;

import com.google.gwt.user.client.ui.Widget;

import org.jldupont.system.IteratorEx;
import org.jldupont.system.Logger;

import org.jldupont.command.Command;
import org.jldupont.command.CommandStatus;

import org.jldupont.widget.WidgetUpdateListe;

public class ListeUpdaterCommand 
	extends Command 
	implements WidgetCommandInterface {

	final static String thisClass ="org.jldupont.widget_commands.ListeUpdaterCommand";
	
	/**
	 * The parameter name to look for
	 */
	String paramName = null;
	
	/**
	 * Widget
	 */
	Widget w = null;
	
	/*===================================================================
	 * Constructors 
	 ===================================================================*/
	
	public ListeUpdaterCommand( ) {
		super( thisClass, "default_id", true );
	}
	
	public ListeUpdaterCommand( String id ) {
		super( thisClass, id, true );
	}
	
	/*===================================================================
	 * WidgetCommandInterface
	 ===================================================================*/
	/**
	 * Setter
	 * @param w
	 */
	public void setWidget(Widget w) {
		Logger.logDebug(thisClass+"::setWidget" );		
		this.w = w;
	}
	
	/**
	 * Clears the widget's content
	 */
	public void clearWidget() {
		Logger.logDebug(thisClass+"::clearWidget" );
		try {
			((WidgetUpdateListe) this.w).clear();
		} catch(Exception e) {
			Logger.logError(thisClass+"::clearWidget: exception msg="+e.getMessage());
		}
	}
	/*===================================================================
	 * PROTECTED
	 ===================================================================*/
	protected void appendItem(String key) {
		//Logger.logDebug(thisClass+"::appendItem, key["+key+"]" );		
		try {
			((WidgetUpdateListe) this.w).addItem(key);
		} catch(Exception e) {
			Logger.logError(thisClass+"::appendItem: exception msg="+e.getMessage());
		}
	}
	
	/*===================================================================
	 * CommandInterface
	 ===================================================================*/
	
	/**
	 * Parameter to look for
	 */
	public void setParameterName( String paramName ) {
		this.paramName = paramName;
	}
	
	/**
	 * @pattern Template Method
	 */
	protected CommandStatus _run() {
		
		// retrieve list
		IteratorEx liste = (IteratorEx) this.param.getParameter( this.paramName );
		if (liste==null) {
			Logger.logWarn(this.classe+"._run: received NULL list");
			return new CommandStatus( "received NULL list" );
		}
		// clear widget's view
		this.clearWidget();
		
		//Logger.logDir(liste);
		
		// update widget
		liste.reset();
		while( liste.hasNext() ) {
			appendItem( (String) liste.next() );
		}
		
		return new CommandStatus( /*OK*/ );
	}
	
	protected void _onError() {
		// TODO Auto-generated method stub
	}

	protected void _onPending() {
		// TODO Auto-generated method stub
	}

	public void setStatus(CommandStatus s) {
		// TODO Auto-generated method stub

	}

	/*===================================================================
	 * Recycling 
	 ===================================================================*/
	public void _clean() {
		super._clean();
	}
	
}//endclass
