package com.jldupont.pages.client;

import com.google.gwt.core.client.GWT;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.HTMLPanel;
import com.google.gwt.user.client.ui.VerticalPanel;
import com.google.gwt.user.client.ui.Widget;
import com.web_bloks.content.client.ViewPage;
import com.web_bloks.document.client.Title;
//import com.web_bloks.system.client.Logger;
import com.web_bloks.user.client.StatusMessageServer;
//import com.web_bloks.user.client.User;
//import com.web_bloks.user.client.UserManager;
import com.web_bloks.user.client.UserMessageServer;
import com.web_bloks.widget.client.EditPageWidget;
import com.web_bloks.widget.client.ViewSourceWidget;

/**
 * Request to display a page
 * - use method 'setRequestedPageParams' to specify input parameters
 * - prepare the 'pagename' for the client: this is useful for the Window title
 *  
 * @author Jean-Lou Dupont
 *
 */
public class MainPage extends Composite
	implements ViewPage {

	MainPageMessages MSG = GWT.create(MainPageMessages.class);

	//Page request related
	String requestPageName = null;
	String requestQuery    = null;
	
	//User feedback related
	UserMessageServer   umsg = null;
	StatusMessageServer smsg = null;
	
	//Page tools
	EditPageWidget editPageWidget;
	ViewSourceWidget viewSourceWidget;
	
	public MainPage() {
		super();

		final VerticalPanel verticalPanel = new VerticalPanel();
		initWidget(verticalPanel);

		final HTMLPanel panelContent = new HTMLPanel(MSG.page());
		verticalPanel.add(panelContent);
		panelContent.setSize("100%", "100%");
		//verticalPanel.setCellVerticalAlignment(panelContent, HasVerticalAlignment.ALIGN_TOP);
		//verticalPanel.setCellHorizontalAlignment(panelContent, HasHorizontalAlignment.ALIGN_LEFT);
		verticalPanel.setCellHeight(panelContent, "100%");
		verticalPanel.setCellWidth(panelContent, "100%");

		
	}
	/*****************************************
	 * ViewPage INTERFACE 
	 *****************************************/
	public Widget getViewWidget() {
		return this;
	}
	
	public String getViewName() {
		return "Main";
	}
	/*
	 * TODO 
	 * @see com.web_bloks.content.client.ViewPage#getPageName()
	 */
	public String getPageName() {
		return requestPageName;
	}
	/**
	 * @see ViewPage
	 */
	public void setViewParams(String key, String value) {
	}
	/**
	 * @see ViewPage
	 */
	public void setRequestedPageParams(String requestedPage, String query) {
		requestPageName = requestedPage;
		requestQuery    = query;
	}
	/**
	 * Invoke DocumentManager for doing the heavy lifting
	 */
	public void doView() {
	}
	
	public void setUserMessageServer(UserMessageServer umsg) {
		this.umsg = umsg;
	}
	public void setStatusMessageServer(StatusMessageServer smsg) {
		this.smsg = smsg;
	}

	/*****************************************
	 * 
	 *****************************************/
	protected void updateTools(Title title) {
		
		//UserManager um = GWT.create( UserManager.class );
		//User user = um.getCurrentUser();
		
	}//
	
	public void doUpdate() {
		
	}
	
}//END
