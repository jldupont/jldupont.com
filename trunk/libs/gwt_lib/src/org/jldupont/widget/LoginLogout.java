/**
 * LoginLogout widget
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.widget;

public class LoginLogout 
	extends ImgAnchorLink {

	public final static int STATE_LOGIN 	= 0;
	public final static int STATE_LOGOUT	= 1;
	
	// Image URLs
	final static String img_login    = "login.png";
	final static String img_logout   = "logout.png";
	
	// Tooltips
	final static String title_login  = "Login"; 
	final static String title_logout = "Logout";
	
	String href_login = null;
	String href_logout = null;
	
	public LoginLogout() {
		super();
		setState("login");
	}

	public void setLoginHref(String url) {
		this.href_login = url;
	}
	public void setLogoutHref(String url) {
		this.href_logout = url;
	}
	public void setState(String state) {
		if (state=="login") {
			this.setImgUrl(img_login);
			this.setTitle(title_login);
			this.setHref(this.href_login);
		} else {
			this.setImgUrl(img_logout);
			this.setTitle(title_logout);
			this.setHref(this.href_logout);			
		}
	}
		
	public void setState(int state, String url) {
		
		if (STATE_LOGIN == state) {
			this.setImgUrl(img_login);
			this.setTitle(title_login);
			this.setHref(url);
		} else {
			this.setImgUrl(img_logout);
			this.setTitle(title_logout);
			this.setHref(url);
		}
	}
	
}//end