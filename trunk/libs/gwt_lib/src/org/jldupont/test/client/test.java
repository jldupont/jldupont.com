/**
 * @author Jean-Lou Dupont
 * 
 *  Serves as test for the librairy
 */
package org.jldupont.test.client;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.ClickListener;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.gwt.user.client.ui.TabPanel;
import com.google.gwt.user.client.ui.Widget;

import org.jldupont.command.CommandParameters;
import org.jldupont.command.ParameterList;
import org.jldupont.command.CommandStatus;

import org.jldupont.delicious.TagsFetcher;
import org.jldupont.delicious.TagsList;
import org.jldupont.delicious.TagsManager;
import org.jldupont.delicious.TagsManagerCommand;

import org.jldupont.system.Factory;
import org.jldupont.system.Logger;

import org.jldupont.widget.ListBoxEx;
import org.jldupont.widget_commands.ListeUpdaterCommand;

/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
public class test 
	implements EntryPoint {
	
	private Button clickMeButton;
	
	public static TagsFetcher tagsFetcher;
	public static TagsManager tagsManager;
	public static TagsManagerCommand tagsManagerCommand;
	public static TagsList tagsList;
	public static ParameterList params;
	
	public static ListeUpdaterCommand listeUpdaterCommand;
	
	protected TagsChangedListenerTest tagsChangedListener = null;
	
	public void onModuleLoad() {
		
		RootPanel rootPanel = RootPanel.get();

		clickMeButton = new Button();
		rootPanel.add(clickMeButton, 18, 296);
		clickMeButton.setText("Fetch TagsFetcher");
		
		// TEST
		//Factory.map("org.jldupont.delicious.TagsFetcher", "org.jldupont.delicious.mocks.TagsFetcher");
		
		
		tagsFetcher = (org.jldupont.delicious.TagsFetcher) Factory.create("org.jldupont.delicious.TagsFetcher");
		tagsFetcher.setUser("jldupont");

		final ListBoxEx listBox = new ListBoxEx();
		rootPanel.add(listBox, 5, 48);
		listBox.setSize("87px", "228px");
		listBox.setVisibleItemCount(5);
		
		this.tagsChangedListener = new TagsChangedListenerTest( listBox );
		
		tagsFetcher.addCallListener(this.tagsChangedListener);
		
		clickMeButton.addClickListener(new ClickListener() {
			
			public void onClick(Widget sender) {
				test.tagsFetcher.get();
			}
		});

		final Button testTagsmanagerButton = new Button();
		rootPanel.add(testTagsmanagerButton, 18, 338);
		testTagsmanagerButton.setText("Test TagsManager");

		tagsManager = (org.jldupont.delicious.TagsManager) Factory.create("org.jldupont.delicious.TagsManager");		
		tagsManager.addCallListener( this.tagsChangedListener );				
		tagsManager.setStorageName( "test" );
		
		testTagsmanagerButton.addClickListener(new ClickListener() {
			public void onClick( Widget sender ) {
				tagsList = tagsManager.get("jldupont");
				Logger.logDir( tagsList );
			}
		});

		// TagsManagerCommand
		// ==================
		final Button testTagsmanagercommandButton = new Button();
		rootPanel.add(testTagsmanagercommandButton, 18, 390);
		testTagsmanagercommandButton.setWidth("169px");
		testTagsmanagercommandButton.setText("Test TagsManagerCommand");
		
		params = new ParameterList();
		params.setParameter("user", "jldupont");
		params.setParameter("ttl", "0" );
		
		tagsManagerCommand = (org.jldupont.delicious.TagsManagerCommand) Factory.create("org.jldupont.delicious.TagsManagerCommand");
		tagsManagerCommand.setStorageName("test");
		
		listeUpdaterCommand = (org.jldupont.widget_commands.ListeUpdaterCommand) Factory.create("org.jldupont.widget_commands.ListeUpdaterCommand");
		listeUpdaterCommand.setWidget(listBox);
		listeUpdaterCommand.setParameterName("taglist");
		
		tagsManagerCommand.setNext(tagsManagerCommand, listeUpdaterCommand);
		
		testTagsmanagercommandButton.addClickListener(new ClickListener() {
			public void onClick( Widget sender ) {
				CommandStatus cs = tagsManagerCommand.run( (CommandParameters) params);
				Logger.logDir( cs );
			}
		});

		final TabPanel tabPanel = new TabPanel();
		rootPanel.add(tabPanel, 116, 48);
		tabPanel.setSize("447px", "237px");
		tabPanel.selectTab(0);
		
		//testToSource();
		//testObjectLiteral();
		
	}//[onModuleLoad]
	
}//end class
