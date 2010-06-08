package com.jldupont.project_v2.client;

import com.web_bloks.content.client.PortalManager;
import com.web_bloks.document.client.Title;
/**
 * 
 * @author Jean-Lou Dupont
 *
 */
public class AppPortalManager extends PortalManager {

	static Title defaultTitle = null;
	
	protected AppPortalManager() {
		super();
		if (null==defaultTitle) {
			defaultTitle = Title.safeNewFromText("Main");
		}
	}
	
	public void navigateToDefault() {
		navigateTo( defaultTitle );
	}
	
	
}//END