package com.jldupont.project.client;

import com.google.gwt.user.client.Window;
import com.google.gwt.user.client.ui.Button;
import com.google.gwt.user.client.ui.ClickListener;
import com.google.gwt.user.client.ui.RootPanel;
import com.google.gwt.user.client.ui.Widget;

import com.google.gwt.gadgets.client.Gadget.ModulePrefs;
import com.google.gwt.gadgets.client.Gadget;
import com.google.gwt.gadgets.client.IntrinsicFeature;
import com.google.gwt.gadgets.client.NeedsIntrinsics;
import com.google.gwt.gadgets.client.NeedsSetPrefs;
import com.google.gwt.gadgets.client.NeedsSetTitle;
import com.google.gwt.gadgets.client.SetPrefsFeature;
import com.google.gwt.gadgets.client.SetTitleFeature;

/**
 * Entry point classes define <code>onModuleLoad()</code>.
 */
@ModulePrefs(title = "__UP_title__", author = "Jean-Lou Dupont", author_email = "jeanlou.dupont@gmail.com")
public class main extends Gadget<MainPreferences> implements NeedsIntrinsics, NeedsSetPrefs, NeedsSetTitle  {
	
	IntrinsicFeature intrinsics;
	SetPrefsFeature setPrefs;
	SetTitleFeature setTitle;
	  
	private Button clickMeButton;
	
	protected void init(MainPreferences preferences) {
		RootPanel rootPanel = RootPanel.get();

		clickMeButton = new Button();
		rootPanel.add(clickMeButton);
		clickMeButton.setText("Click me!");
		clickMeButton.addClickListener(new ClickListener() {
			public void onClick(Widget sender) {
				Window.alert("Hello, GWT World!");
			}
		});
	}
	
	public void initializeFeature(IntrinsicFeature feature) {
	    intrinsics = feature;
	  }

	  public void initializeFeature(SetPrefsFeature feature) {
	    this.setPrefs = feature;
	  }

	  public void initializeFeature(SetTitleFeature feature) {
	    this.setTitle = feature;
	  }
}//
