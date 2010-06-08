package com.jldupont.project_v2.client;

import java.util.Date;

import com.google.gwt.core.client.EntryPoint;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.json.client.JSONValue;
import com.google.gwt.user.client.Cookies;
import com.google.gwt.user.client.History;
import com.google.gwt.user.client.ui.RootPanel;
import com.jldupont.pages.client.Base;

import com.web_bloks.document.client.Namespace;
import com.web_bloks.document.client.Title;
import com.web_bloks.http.client.JSONCall;
import com.web_bloks.http.client.JSONCallback;
import com.web_bloks.storage.client.GearsOffline;
import com.web_bloks.system.client.Logger;
import com.web_bloks.url.client.Helper;
import com.web_bloks.url.client.UrlFields;
import com.web_bloks.user.client.User;
import com.web_bloks.user.client.UserManager;

/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
public class main implements EntryPoint, JSONCallback {

	public void onModuleLoad() {
		
		initGears();
		initCookies();
		initTitleNamespace();
		initMessages();	
		initViewComponents();
		initUser();		
		initViewComponentsBindings();
		
		doInitialNavigation();
	}
	
	/* =====================================================================================================
		INITIALIZATION
	===================================================================================================== */

	/*
	 * Gears related
	 * =============
	 */
	final static String MANAGED_LOCAL_STORE = "jldupont";
	final static String MANIFEST_URL = "org.jldupont.project_v2.main.nocache.manifest";
	GearsOffline go = GWT.create(GearsOffline.class);	
	
	void initGears() {

		go.setup(MANAGED_LOCAL_STORE, MANIFEST_URL);
		
	}//
	
	/*
	 * Cookies related
	 * ===============
	 */
	void initCookies() {
		Date now = new Date();
		long nowLong = now.getTime() + (1000*60*60*24);
		now.setTime( nowLong );
		
		//sets a Gears cookie for the server side
		String ggst = String.valueOf( go.isAvailable() );
		String ggen = String.valueOf( go.isEnabled() );
		Cookies.setCookie( "ggst", ggst, now );
		Cookies.setCookie( "ggen", ggen, now );
	}//
	
	/*
	 * Title & Namespace initialization
	 */
	AppNamespaces appNs	= null;
	
	void initTitleNamespace() {
		
		//Namespaces
		appNs			= GWT.create(AppNamespaces.class);
		Namespace.setNamespaces( appNs );
		Title.setNamespaces( appNs );
	}

	/*
	 * Message related
	 */
	AppMessages			MSG 			= null;	
	
	void initMessages() {
		MSG 			= GWT.create(AppMessages.class);		
	}
	
	
	/*
	 * View Components related
	 */
	// GUI related
	RootPanel rootPanel = null;
	public Base basePanel = null;
	
	AppTitleAccessList		appAcl			= null;
	AppFlowManager			appFlowManager	= null;
	AppViewManager			appViewManager	= null;	
	AppConditionChecker		appCC			= null;
	AppUserMessageManager	appUserMessage = null;
	AppPortalManager		appPortalManager= null;
	
	void initViewComponents() {
		appAcl			= GWT.create(AppTitleAccessList.class);
		appFlowManager	= GWT.create(AppFlowManager.class);
		appViewManager	= GWT.create(AppViewManager.class);	
		appCC			= GWT.create(AppConditionChecker.class);
		appUserMessage	= GWT.create(AppUserMessageManager.class);
		appPortalManager= GWT.create(AppPortalManager.class);
		currentUser		= GWT.create(User.class);
		
		basePanel = GWT.create(Base.class);
		rootPanel = RootPanel.get();
		rootPanel.add(basePanel);
	}
	

	/*
	 * User related
	 */
	User				currentUser		= null;
	
	protected void initUser() {
	
		//UserManager
		UserManager um = GWT.create(UserManager.class);
		
		// init current user ... but we won't have the parameters
		// until the JSON call finishes
		currentUser = um.getCurrentUser();
		
		JSONCall jsc = GWT.create(JSONCall.class);
		jsc.setCallback(this);
	
		try {
			jsc.execute("/api/user/", null);
		} catch(Exception e) {
			Logger.logWarn(getClass()+".onModuleLoad: error performing /api/user/ call, msg="+e.getMessage());
		}
	}

	/**
	 * User initialization related
	 */
	public void onJSONCallError(Request request, Throwable exception) {
		Logger.logWarn(getClass()+".onJSONCallError: error fetching from /api/user");
	}

	/**
	 * User initialization related
	 */
	public void onJSONCallResponseReceived(int code, JSONValue response) {
	
		//UserManager
		UserManager um = GWT.create(UserManager.class);
		
		// init current user ... but we won't have the parameters
		// until the JSON call finishes
		currentUser = um.getCurrentUser();
		
		currentUser.initFromJSON( response );
		
		basePanel.setupTools();
	}
	
	/*
	 * View Components Bindings
	 */
	void initViewComponentsBindings() {
		appFlowManager.setViewManager( appViewManager );
		appFlowManager.setConditionChecker( appCC );
		appFlowManager.setTitleAccessList( appAcl );
		appFlowManager.setCurrentUser( currentUser );
		appFlowManager.doFinalInit();

		appPortalManager.setBasePage( basePanel );
		appPortalManager.setFlowManager( appFlowManager );
		appPortalManager.setUserMessageManager( appUserMessage );
		appPortalManager.setMessageManager( MSG );

		appCC.setCurrentUser(currentUser);
		
		appUserMessage.setMessageWidget( basePanel.getUserMessageWidget() );
		History.addHistoryListener( appPortalManager );		
	}

	
	void doInitialNavigation() {
			
		UrlFields uf = Helper.extractFieldsFromLocation();
		if (null!=uf) {
			appPortalManager.navigateTo(uf);
		} else {
			appPortalManager.navigateToDefault();
		}
	}
	
}//END
