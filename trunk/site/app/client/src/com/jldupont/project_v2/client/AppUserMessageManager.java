package com.jldupont.project_v2.client;

import com.web_bloks.content.client.UserMessageManager;
import com.web_bloks.widget.client.UserMessageWidget;
import com.web_bloks.system.client.Logger;

/**
 * Use 'sendMessage'
 * 
 * @author Jean-Lou Dupont
 *
 */
public class AppUserMessageManager extends UserMessageManager {

	UserMessageWidget msgWidget = null;
	
	protected AppUserMessageManager() {
	}
	
	public void setMessageWidget(UserMessageWidget mw) {
		msgWidget = mw;
	}
	
	public void sendMessage(String msg) {
		Logger.logDebug(this.getClass()+".sendMessage: msg="+msg);
	}
	
	/**
	 * Event: cleanup
	 * @see UserMessageManager
	 */
	public void tick() {
		msgWidget.sendMessage("");
	}
	
	/**
	 * @see UserMessageManager
	 */
	public void pushMessage(String msg) {
		msgWidget.sendMessage( msg );
	}

}//END