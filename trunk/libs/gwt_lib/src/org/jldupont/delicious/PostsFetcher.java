package org.jldupont.delicious;

import org.jldupont.system.Logger;
import org.jldupont.web.BaseFetcher;
import org.jldupont.web.CallListener;

import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.json.client.JSONObject;

/**
 * PostsFetcher
 *  
 * @author Jean-Lou Dupont
 * 
 * Without the 'count' parameter, only 15 posts are returned by the Delicious Service
 * 
 */
public class PostsFetcher 
	extends BaseFetcher 
	implements PostsChangedEventListener {

	final static String thisClass = "org.jldupont.delicious.PostsFetcher";

	String username = null;
	
	/**
	 * Delicious feed REST 
	 */
	final static String feedUrl = "http://del.icio.us/feeds/json/"; // username  OR username/tags-list
	
	/*===================================================================
	 * CONSTRUCTORS
	 ===================================================================*/
	
	/**
	 * Constructor
	 * @param classe class literal
	 * @param id class-scope unique id
	 */
	public PostsFetcher( String classe, String id ) {
		super(classe, id, true );
		setup();
	}
	
	public PostsFetcher( String id ) {
		super(thisClass, id, true );
		setup();
	}

	public PostsFetcher() {
		super(thisClass, "default_id", true );
		setup();
	}
	private void setup() {

	}
	/*===================================================================
	 * 
	 ===================================================================*/

	
	/*===================================================================
	 * PostsChangedListener
	 ===================================================================*/
	public void addPostsChangedListener(PostsChangedListener s) {
		this.addCallListener((CallListener)s);
	}
	public void removePostsChangedListener(PostsChangedListener s) {
		this.removeCallListener((CallListener)s);		
	}
	
	/*===================================================================
	 * PUBLIC interface
	 ===================================================================*/
	/**
	 * setUser
	 *  Delicious Username
	 */
	public void setUser( String username ) {
		this.username = new String( username );
	}
	/**
	 * Fetches a up-to-date copy of the tags for user
	 */
	public void get() throws RuntimeException {
		
		if (this.username.length() == 0)
			throw new RuntimeException(this.classe+".get: username is empty");
		
		this.setUrl( feedUrl + this.username );
		
		// this is specific to Delicious
		this.setCallbackParameterName("callback");
		
		//go fetch them
		this.fetch();
	}
	
	
	/*===================================================================
	 * CALLBACK
	 ===================================================================*/

	protected Object transformJSONObject(JSONObject o) {
		// TODO Auto-generated method stub
		return null;
	}
	public void handleCallbackEvent(int id, JavaScriptObject obj) {
		Logger.logInfo(this.classe+".handleCallbackEvent: called.");		
		super.handleCallbackEvent(id, obj);
	}
	/*===================================================================
	 * RECYCLING
	 ===================================================================*/
	/**
	 * Don't forget to clean the composing objects 
	 */
	public void _clean() {
		super._clean();
		this.username = null;
	}

}//end







/*
 * http://feeds.delicious.com/feeds/json/jldupont/companies?callback=callback
	callback( [ {"u":"http:\/\/www.startupwarrior.com\/","d":"Startup Warrior","t":["startups","companies"],"dt":"2008-06-26T03:54:38Z","n":""},
				{"u":"http:\/\/www.bridgewave.com\/","d":"BridgeWave Communications","t":["companies","telecomms","wireless","Ethernet"],"dt":"2008-06-26T10:21:31Z","n":"Gigabit Point-To-Point Wireless Bridges"},
				{"u":"http:\/\/www.rightscale.com\/m","d":"RightScale: Launch scalable Amazon EC2 instances","t":["companies","webservices"],"dt":"2008-06-26T10:20:44Z","n":""},
				{"u":"http:\/\/www.sigmadesigns.com\/public\/index.html","d":"Sigma Designs, Inc.","t":["companies","telecomms","Ethernet","semiconductors"],"dt":"2008-06-25T03:27:35Z","n":""},
				{"u":"http:\/\/www.parascale.com\/","d":"Parascale Cloud Storage Platform - Automatic Data File Migration and Replication","t":["companies","telecomms","cloud_computing"],"dt":"2008-06-25T03:19:24Z","n":""},
				{"u":"http:\/\/www.alaxala.com\/en","d":"ALAXALA Networks","t":["companies","telecomms","Ethernet"],"dt":"2008-06-25T03:16:53Z","n":""},
				{"u":"http:\/\/www.funambol.com\/","d":"Funambol Mobile 2.0 Messaging Powered by Open Source","t":["companies","wireless","mobile","telecomms"],"dt":"2008-06-17T07:21:32Z","n":""},
				{"u":"http:\/\/www.iblast.com\/","d":"iBlast","t":["telecomms","companies","wireless"],"dt":"2008-06-02T05:55:11Z","n":"iBlast is a nationwide digital distribution network that uses the powerful transmitters of local TV stations to broadcast rich media content directly to home computers, digital set-top boxes, and other receiving devices."},
				{"u":"http:\/\/www.napera.com\/","d":"Napera","t":["telecomms","companies","ethernet"],"dt":"2008-06-02T01:19:37Z","n":"Provides network solutions that enable small and medium enterprises to build safe, secure and healthy networks. Napera solutions leverage Microsoft's Network Access Protection (NAP) architecture"},
				{"u":"http:\/\/www.zeugmasystems.com\/","d":"Zeugma Systems","t":["companies","telecomms"],"dt":"2008-05-27T01:28:50Z","n":""},
				{"u":"http:\/\/www.tail-f.com\/","d":"Tail-f Systems","t":["companies","telecomms","software","network-management"],"dt":"2008-05-22T08:02:00Z","n":"XML-based Network Management Software"},
				{"u":"http:\/\/www.arteris.net\/","d":"Arteris - The Network-on-Chip Company Home Page","t":["companies","semiconductors"],"dt":"2008-05-22T01:06:19Z","n":""},
				{"u":"http:\/\/www.aria-networks.com\/","d":"Aria Networks intelligent solutions for Next Generation Networks and Services","t":["companies","telecomms"],"dt":"2008-05-14T02:12:10Z","n":""},
				{"u":"http:\/\/www.tpack.com\/","d":"TPACK","t":["companies","telecomms","ethernet"],"dt":"2008-04-15T07:30:18Z","n":"TPACK provides embedded software solutions to Telecom Equipment Manufacturers that enable them to build systems faster and with lower risk, but also allowing new functionality to be added both during development and in the field."},
				{"u":"http:\/\/www.sibeam.com\/","d":"SiBEAM","t":["companies","startup","telecomms","wireless"],"dt":"2008-04-07T02:42:10Z","n":"Milimeter wavelength wireless products"}])
 */