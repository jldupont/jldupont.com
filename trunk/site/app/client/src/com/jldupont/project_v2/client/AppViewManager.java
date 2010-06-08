package com.jldupont.project_v2.client;

import com.web_bloks.content.client.ViewManager;
import com.web_bloks.document.client.Title;
//PAGES

/**
 * 
 * @author Jean-Lou Dupont
 *
 */
public class AppViewManager extends ViewManager {

	static Title wildTitle = null;
	
	protected AppViewManager() {
		super();
		wildTitle = Title.newWild();		
		init();
	}
	/**
	 * 
	 */
	protected void init() {
		
		//addMapping( wildTitle, 								new MainPage() );
		
	}
	
}//END
