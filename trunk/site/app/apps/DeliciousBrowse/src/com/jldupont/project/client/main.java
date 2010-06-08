/**
 * Delicious Browser
 * GWT
 * 
 * @author Jean-Lou Dupont
 */
/**
 * TAGS
 * =============
 * + When USER changes => fetch tags
 * - Store locally
 * - Populate Tag_Tree
 * 
 * TAG selected
 * ============
 * + WHEN tag selection changes => filter posts
 * - GET POSTS with TAG t
 * -- Verify local store
 * -- FETCH remote
 * 
 */
/*
		tagsFetcher = (org.jldupont.delicious.TagsFetcher) Factory.create("org.jldupont.delicious.TagsFetcher");
		tagsFetcher.setUser("jldupont");
		
		this.tagsChangedListener = new TagsChangedListenerTest();
		
		tagsFetcher.addCallListener(this.tagsChangedListener);
		
		clickMeButton.addClickListener(new ClickListener() {
			
			public void onClick(Widget sender) {
				test.tagsFetcher.get();
			}
		});
 */
package com.jldupont.project.client;

import org.jldupont.system.*;
import org.jldupont.widget.GoogleGears;
import org.jldupont.widget.ImgAnchorLink;
import org.jldupont.delicious.TagsFetcher;
import org.jldupont.browser.Param;
import org.jldupont.browser.URLParamsList;

import com.google.gwt.user.client.Element;
import com.google.gwt.user.client.DOM;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.user.client.Command;
import com.google.gwt.user.client.DeferredCommand;
import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.WindowResizeListener;

import com.google.gwt.user.client.ui.ClickListener;
import com.google.gwt.user.client.ui.FlexTable;
import com.google.gwt.user.client.ui.Frame;
import com.google.gwt.user.client.ui.HasHorizontalAlignment;
import com.google.gwt.user.client.ui.HasVerticalAlignment;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.ListBox;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.gwt.user.client.ui.TextBox;
import com.google.gwt.user.client.ui.TextBoxBase;
import com.google.gwt.user.client.ui.Tree;


/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
public class main implements EntryPoint, WindowResizeListener {
	
	/**
	 * Controls
	 */
	final FlexTable flexTable = new FlexTable();
	final ListBox lbPosts = new ListBox();
	final Frame frPost = new Frame("");
	final Frame frTags = new Frame("");
	final Frame frPosts = new Frame("");

	final Label labelUser = new Label("User:");	
	final TextBox tbUser = new TextBox();	
	/**
	 * List of valid Parameters
	 */
	String[] validParams = {
		"u", "tf", "pf",
	};
	
	/**
	 * List of parameters
	 */
	URLParamsList params;
	
	/*
	 * (non-Javadoc)
	 * @see com.google.gwt.core.client.EntryPoint#onModuleLoad()
	 */
	public void onModuleLoad() {
		RootPanel rootPanel = RootPanel.get();

		
		rootPanel.add(flexTable, 0, 0);
		flexTable.setSize("100%", "100%");

		flexTable.getCellFormatter().setWidth(2, 0, "10%");
		flexTable.getCellFormatter().setVerticalAlignment(2, 0, HasVerticalAlignment.ALIGN_TOP);
		
		flexTable.setWidget(2, 1, lbPosts);
		flexTable.getCellFormatter().setWidth(2, 1, "10%");
		flexTable.getCellFormatter().setVerticalAlignment(2, 1, HasVerticalAlignment.ALIGN_TOP);
		lbPosts.setVisibleItemCount(100);
		lbPosts.setSize("100%", "100%");

		flexTable.setWidget(2, 2, frPost);
		flexTable.getCellFormatter().setWidth(2, 2, "80%");
		frPost.setSize("100%", "100%");

		flexTable.getCellFormatter().setHeight(1, 0, "23px");

		flexTable.setWidget(1, 0, labelUser);
		flexTable.getCellFormatter().setHorizontalAlignment(1, 0, HasHorizontalAlignment.ALIGN_RIGHT);
		labelUser.setSize("100%", "100%");
		flexTable.getCellFormatter().setHeight(1, 0, "27px");


		flexTable.setWidget(1, 1, tbUser);
		tbUser.setSize("100%", "100%");
		tbUser.setTitle("UserName");
		tbUser.setTextAlignment(TextBoxBase.ALIGN_LEFT);

		final FlexTable header = new FlexTable();
		flexTable.setWidget(0, 0, header);
		flexTable.getCellFormatter().setHeight(0, 0, "52px");
		flexTable.getFlexCellFormatter().setColSpan(0, 0, 3);

		final GoogleGears googleGears = new GoogleGears();
		flexTable.setWidget(0, 3, googleGears);

		final ImgAnchorLink imgAnchorLink = new ImgAnchorLink();
		flexTable.setWidget(3, 0, imgAnchorLink);
		imgAnchorLink.setListeners(new String[] {"allo", "bonjour"} );

		// Tag Tree
		final Tree tag_tree = new Tree();
		flexTable.setWidget(2, 0, tag_tree);

		
		rootPanel.add(frPosts, 571, 577);
		frPosts.setSize("12px", "6px");
		frPosts.setVisible(false);
		
		this.params = new URLParamsList();
		
		String liste=new String("");
		
		Param param;
		
		while( this.params.hasNext() ) {
			
			param = (Param) this.params.next();
			
			liste += "name: " + param.getName() + " value: " + param.getValue();
		}
		
		if ( liste.length() != 0 ) {
			Logger.log( "Parameters:: " + liste );
		}
		//Window.alert( "Parameters:: " + liste );
		
	    DeferredCommand.addCommand(new Command() {
	        public void execute() {
	          onWindowResized(Window.getClientWidth(), Window.getClientHeight());
	        }
	      });

	    onWindowResized(Window.getClientWidth(), Window.getClientHeight());
	    
	    doTest();

	}
	
	/*TODO
	 * (non-Javadoc)
	 * @see com.google.gwt.user.client.WindowResizeListener#onWindowResized(int, int)
	 */
	public void onWindowResized(int width, int height) {
		
		Element ft = flexTable.getElement();
		
		int ww = this.flexTable.getOffsetWidth();
		
		int l = width - ww;

		DOM.setStyleAttribute(ft, "position", "absolute");
		DOM.setStyleAttribute(ft, "left", l + "px");
	}

	private void doTest() {

		String id;
		
		TagsFetcher t1 = (TagsFetcher) Factory.create( "org.jldupont.delicious.TagsFetcher", "id1" );
		t1.recycle();
		
		TagsFetcher t2 = (TagsFetcher) Factory.create( "org.jldupont.delicious.TagsFetcher", "id2" );
		t2.recycle();
	}
	
}//end class
