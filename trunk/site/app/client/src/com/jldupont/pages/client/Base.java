package com.jldupont.pages.client;

import com.google.gwt.core.client.GWT;
import com.google.gwt.user.client.Command;
import com.google.gwt.user.client.DeferredCommand;
import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.ui.Composite;
import com.google.gwt.user.client.ui.FlexTable;
//import com.google.gwt.user.client.ui.FocusListenerAdapter;
import com.google.gwt.user.client.ui.HTML;
import com.google.gwt.user.client.ui.HasHorizontalAlignment;
import com.google.gwt.user.client.ui.HasVerticalAlignment;
import com.google.gwt.user.client.ui.HorizontalPanel;
import com.google.gwt.user.client.ui.Hyperlink;
import com.google.gwt.user.client.ui.KeyboardListenerAdapter;
import com.google.gwt.user.client.ui.Label;
import com.google.gwt.user.client.ui.SimplePanel;
import com.google.gwt.user.client.ui.TextBox;
import com.google.gwt.user.client.ui.VerticalPanel;
import com.google.gwt.user.client.ui.Widget;

import com.web_bloks.content.client.BasePage;
import com.web_bloks.content.client.ViewPage;
//import com.web_bloks.system.client.Logger;
import com.web_bloks.user.client.StatusMessageServer;
import com.web_bloks.user.client.User;
import com.web_bloks.user.client.UserManager;
import com.web_bloks.user.client.UserMessageServer;
import com.web_bloks.widget.client.ConfigWidget;
import com.web_bloks.widget.client.GearsStatus;
import com.web_bloks.widget.client.ImgAnchor;
import com.web_bloks.widget.client.LoginLogoutWidget;
import com.web_bloks.widget.client.UserMessageWidget;
import com.web_bloks.system.client.Logger;

/**
 * 
 * @author Jean-Lou Dupont
 *
 */
public class Base extends Composite 
	implements BasePage {
	
	final static BaseMessages MSG = (BaseMessages) GWT.create(BaseMessages.class);

	MainPage mp = null;
	
	ViewPage currentView = null;
	Widget currentWidget = null;
	
//	public VerticalPanel verticalPanel = null;
	public HorizontalPanel horizontalPanel = null;
	public SimplePanel contentWrapper = null;
	
	public FlexTable flexTableHeader = null;
	public FlexTable flexTableFooter = null;
	
	public Label webbloksTitle = new Label();
	
	// Frame Widget
	public LoginLogoutWidget loginLogoutWidget = null;
	public GearsStatus googleGears = null;
	public ConfigWidget configWidget = null;
	

	// Text Input
	TextBox textBoxInput;
	
	// User Message
	UserMessageWidget userMessageWidget;
	
	public ImgAnchor imgAnchorLink = null;
	
	
	protected Base() {
		super();
		
		VerticalPanel verticalPanelBase = new VerticalPanel();
		initWidget( verticalPanelBase );		

		verticalPanelBase.setSpacing(5);
		verticalPanelBase.setSize("100%", "100%");

		final FlexTable flexTableHeader = new FlexTable();
		verticalPanelBase.add(flexTableHeader);
		flexTableHeader.setWidth("100%");
		verticalPanelBase.setCellVerticalAlignment(flexTableHeader, HasVerticalAlignment.ALIGN_TOP);
		verticalPanelBase.setCellHorizontalAlignment(flexTableHeader, HasHorizontalAlignment.ALIGN_CENTER);
		verticalPanelBase.setCellWidth(flexTableHeader, "100%");

		final ImgAnchor imgAnchorLogo = new ImgAnchor();
		imgAnchorLogo.setImgUrl("logo.gif");		
		flexTableHeader.setWidget(0, 0, imgAnchorLogo);
		imgAnchorLogo.setHref( Window.Location.getPath() );
		imgAnchorLogo.setSize("50", "50");
		flexTableHeader.getCellFormatter().setHeight(0, 0, "50");
		flexTableHeader.getCellFormatter().setWidth(0, 0, "50");

		flexTableHeader.getCellFormatter().setWidth(0, 0, "100%");
		flexTableHeader.getCellFormatter().setWordWrap(0, 0, false);
		flexTableHeader.getCellFormatter().setHorizontalAlignment(0, 0, HasHorizontalAlignment.ALIGN_CENTER);
		flexTableHeader.getCellFormatter().setWidth(0, 0, "300");
		
		configWidget = GWT.create( ConfigWidget.class );
		flexTableHeader.setWidget(0, 2, configWidget);
		configWidget.setSize("35", "35");
		flexTableHeader.getCellFormatter().setHorizontalAlignment(0, 2, HasHorizontalAlignment.ALIGN_CENTER);
		flexTableHeader.getCellFormatter().setHeight(0, 2, "35");
		flexTableHeader.getCellFormatter().setWidth(0, 2, "35");
		configWidget.setVisible(false);

		loginLogoutWidget = GWT.create(LoginLogoutWidget.class);
		flexTableHeader.setWidget(0, 3, loginLogoutWidget);
		loginLogoutWidget.setSize("35", "35");
		flexTableHeader.getCellFormatter().setHeight(0, 3, "35");
		flexTableHeader.getCellFormatter().setWidth(0, 3, "35");
		flexTableHeader.getCellFormatter().setHorizontalAlignment(0, 3, HasHorizontalAlignment.ALIGN_RIGHT);

		final VerticalPanel verticalPanelHeader = new VerticalPanel();
		flexTableHeader.setWidget(0, 1, verticalPanelHeader);
		verticalPanelHeader.setWidth("100%");
		flexTableHeader.getCellFormatter().setWidth(0, 1, "100%");

		userMessageWidget = new UserMessageWidget("");
		verticalPanelHeader.add(userMessageWidget);

		textBoxInput = new TextBox();
		verticalPanelHeader.add(textBoxInput);
		//textBoxInput.setTitle(MSG.input_box_title());
		textBoxInput.setTabIndex(0);
		textBoxInput.setCursorPos(0);
		textBoxInput.setStylePrimaryName("rekrunch-Input");
		verticalPanelHeader.setCellVerticalAlignment(textBoxInput, HasVerticalAlignment.ALIGN_MIDDLE);
		verticalPanelHeader.setCellHorizontalAlignment(textBoxInput, HasHorizontalAlignment.ALIGN_CENTER);
		textBoxInput.setWidth("400");
		verticalPanelHeader.setCellWidth(textBoxInput, "400");

		final HTML htmlTop = new HTML("<hr>");
		verticalPanelBase.add(htmlTop);
		verticalPanelBase.setCellVerticalAlignment(htmlTop, HasVerticalAlignment.ALIGN_TOP);
		verticalPanelBase.setCellHorizontalAlignment(htmlTop, HasHorizontalAlignment.ALIGN_CENTER);
		verticalPanelBase.setCellWidth(htmlTop, "100%");

		contentWrapper = new SimplePanel();
		verticalPanelBase.add(contentWrapper);
		contentWrapper.setSize("100%", "100%");
		//verticalPanelBase.setCellVerticalAlignment(contentWrapper, HasVerticalAlignment.ALIGN_TOP);
		//verticalPanelBase.setCellHorizontalAlignment(contentWrapper, HasHorizontalAlignment.ALIGN_LEFT);
		verticalPanelBase.setCellHeight(contentWrapper, "100%");
		verticalPanelBase.setCellWidth(contentWrapper, "100%");

		final HTML htmlBottom = new HTML("<hr>");
		verticalPanelBase.add(htmlBottom);
		verticalPanelBase.setCellVerticalAlignment(htmlBottom, HasVerticalAlignment.ALIGN_BOTTOM);
		verticalPanelBase.setCellHorizontalAlignment(htmlBottom, HasHorizontalAlignment.ALIGN_CENTER);
		verticalPanelBase.setCellWidth(htmlBottom, "100%");

		final HorizontalPanel horizontalPanelFooterLinks = new HorizontalPanel();
		verticalPanelBase.add(horizontalPanelFooterLinks);
		verticalPanelBase.setCellVerticalAlignment(horizontalPanelFooterLinks, HasVerticalAlignment.ALIGN_MIDDLE);
		verticalPanelBase.setCellHorizontalAlignment(horizontalPanelFooterLinks, HasHorizontalAlignment.ALIGN_CENTER);

		//Hyperlinks
		final Hyperlink hyperlinkAboutPage = new Hyperlink( MSG.about(), MSG.about_page() );
		horizontalPanelFooterLinks.add(hyperlinkAboutPage);
		horizontalPanelFooterLinks.setCellVerticalAlignment(hyperlinkAboutPage, HasVerticalAlignment.ALIGN_MIDDLE);

		final HTML htmlLinkSpacer1 = new HTML("New <i>HTML</i> panel");
		horizontalPanelFooterLinks.add(htmlLinkSpacer1);
		horizontalPanelFooterLinks.setCellWidth(htmlLinkSpacer1, "15");
		htmlLinkSpacer1.setText("");

		final Hyperlink hyperlinkDisclaimerPage = new Hyperlink(MSG.disclaimer(), MSG.disclaimer_page());
		horizontalPanelFooterLinks.add(hyperlinkDisclaimerPage);

		final FlexTable flexTable = new FlexTable();
		verticalPanelBase.add(flexTable);
		flexTable.setWidth("100%");
		verticalPanelBase.setCellVerticalAlignment(flexTable, HasVerticalAlignment.ALIGN_BOTTOM);
		verticalPanelBase.setCellHorizontalAlignment(flexTable, HasHorizontalAlignment.ALIGN_CENTER);
		verticalPanelBase.setCellWidth(flexTable, "100%");

		final GearsStatus gearsStatus = new GearsStatus();
		flexTable.setWidget(0, 0, gearsStatus);
		
		final HorizontalPanel horizontalPanelFooterSources = new HorizontalPanel();
		flexTable.setWidget(0, 1, horizontalPanelFooterSources);
		flexTable.getCellFormatter().setHorizontalAlignment(0, 1, HasHorizontalAlignment.ALIGN_CENTER);
		flexTable.getCellFormatter().setWidth(0, 1, "100%");
		horizontalPanelFooterSources.setSpacing(1);
				
		
		DeferredCommand.addCommand(new Command(){
			public void execute() {
				textBoxInput.setFocus( true );						
			}
		});

		setupTextBoxInput();
	}

	protected void setupTextBoxInput() {

/*		
		textBoxInput.addFocusListener( new FocusListenerAdapter() {
			public void onLostFocus(Widget sender) {
				DeferredCommand.addCommand(new Command(){
					public void execute() {
						textBoxInput.setFocus( true );						
					}
				});
			}
		} );
*/	
		textBoxInput.addKeyboardListener( new KeyboardListenerAdapter() {
			public void onKeyPress(Widget sender, char keyCode, int modifiers) {
				if ( (char) KEY_ENTER == keyCode ) {
					Window.alert("ENTER PRESSED");
				}
			}
		} );
		
	}//
	
	
	public void setupTools() {
		
		//UserManager
		UserManager um = GWT.create(UserManager.class);
		
		// init current user
		User user = um.getCurrentUser();
		
		if (null==user) {
			throw new RuntimeException(this.getClass()+".initUser: user is null");
		}
		
		// LOGIN /LOGOUT icon
		String login_url  = user.getParam("login_url");
		String logout_url = user.getParam("logout_url");

		this.loginLogoutWidget.setLoginUrl(login_url);
		this.loginLogoutWidget.setLogoutUrl(logout_url);
		
		String username = user.getParam("name");
		
		// if username is not empty, then we are logged-in
		if (username != null && username.length() != 0) {
			configWidget.setVisible( true );
			user.setLoginState( true );
			this.loginLogoutWidget.setState(LoginLogoutWidget.State.logout);
		} else {
			configWidget.setVisible( false );
			user.setLoginState( false );
			this.loginLogoutWidget.setState(LoginLogoutWidget.State.login);
		}
	}
	/**
	 * @see BasePage
	 */
	public boolean isCurrentView(String viewname) {
		
		if (null!=currentView) {
			return currentView.getViewName() == viewname ;
		}
		return false;
	}
	/**
	 * @see BasePage
	 */
	public void onBeforeChangingBody() {
	}
	/**
	 * @see BasePage
	 */
	public void setBody(ViewPage cp) {
		Logger.logDebug(this.getClass()+".setBody: BEGIN, cp name="+cp.getPageName());
		
		currentView = cp;
		currentWidget = cp.getViewWidget();
		contentWrapper.setWidget( currentWidget );
		
		//Logger.logDebug(this.getClass()+".setBody: END");
	}
	

	public StatusMessageServer getStatusMessageServer() {
		return null;
	}
	
	public UserMessageWidget getUserMessageWidget() {
		return userMessageWidget;
	}


	public UserMessageServer getUserMessageServer() {
		return null;
	}

}//END
