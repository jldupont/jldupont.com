/**
 * @author Jean-Lou Dupont
 *
 */
package org.jldupont.delicious;

import org.jldupont.system.Logger;
import org.jldupont.web.BaseFetcher;
import org.jldupont.web.CallListener;

import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.json.client.JSONObject;

public class TagsFetcher 
	extends BaseFetcher 
	implements TagsChangedEventListener {

	final static String thisClass = "org.jldupont.delicious.TagsFetcher";
	
	/**
	 * Delicious feed REST 
	 */
	final static String feedUrl = "http://del.icio.us/feeds/json/tags/";
	
	/**
	 * Username
	 */
	String username = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public TagsFetcher( String classe, String id ) {
		super(classe, id, true );
		setup();
	}
	
	public TagsFetcher( String id ) {
		super(thisClass, id, true );
		setup();
	}

	public TagsFetcher() {
		super(thisClass, "default_id", true );
		setup();
	}
	private void setup() {

	}

	/*===================================================================
	 * TagsChangedListener
	 ===================================================================*/
	
	public void addTagsChangedListener(TagsChangedListener s) {
		this.addCallListener((CallListener)s);
	}
	public void removeTagsChangedListener(TagsChangedListener s) {
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
	 * Sets an URL parameter
	 * @param param
	 */
	public void setParam(String param) {
		
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
	 * LISTENERS 
	 ===================================================================*/
	// handled by BaseFetcher
	//  callback & timeout events
	
	/*===================================================================
	 * TIMED OPERATION
	 ===================================================================*/
	
	/*===================================================================
	 * CALLBACK
	 ===================================================================*/
	/**
	 * Transforms the JSON object to a 'TagsList' type object
	 */
	protected Object transformJSONObject( JSONObject o ) {
		return new TagsList( o );
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
}//end class

/**
 * @example
 http://feeds.delicious.com/feeds/json/tagsFetcher/jldupont?callback=CALLBACK
 
CALLBACK({".net":1,"_canadian":1,"_mtlcompanies":2,"_Musique":1,"_ottawacompanies":1,"accelerator":2,"actionscript":4,"adobe":1,"adobeair":2,"aggregator":9,"ajax":18,"akismet":1,"amazon":4,"AmazonWebServices":1,"analysts-reports":1,"analytics":1,"android":1,"annotation":1,"anonymity":1,"ant":1,"antipatterns":1,"antispam":2,"antivirus":1,"aol":1,"aop":2,"apache":2,"apc":2,"API":23,"applet":1,"application":2,"aptana":1,"arc":2,"archive":2,"assurance":1,"atca":7,"atom":1,"audio":1,"authentication":2,"auto":1,"avatar":1,"aws":1,"backup":2,"bbs":3,"biology":3,"blog":5,"book":2,"bookmark":1,"bookmarking":2,"bookmarks":1,"Brain":1,"browser":2,"BT":1,"Bugzilla":1,"business":7,"business-diagrams":1,"c":1,"cache":2,"caching":1,"calendar":1,"calendars":1,"captcha":2,"CDN":6,"cgi":1,"cheatsheet":3,"cloud":2,"cloud-computing":2,"cms":2,"code":3,"code-construction":1,"collaboration":8,"collaborative":1,"companies":254,"compiler":7,"compilers":1,"compostage":1,"compressor":2,"computer":5,"continuousintegration":1,"control-plane":2,"couchdb":1,"crockford":2,"cross-domain":2,"css":8,"daemon":2,"database":6,"datamining":1,"del.icio.us":2,"design":6,"design-patterns":1,"development":12,"diagram":2,"diagrams":2,"dictionary":9,"digg":1,"directory":2,"distributed":1,"distributed_computing":1,"dna":1,"DNS":3,"documentation":4,"dojo":3,"dpi":1,"drupal":1,"DTA":1,"DynDns":1,"E-ink":2,"ebook":1,"ec2":5,"eclipse":3,"EditGrid":1,"editor":4,"electronics":1,"elite":1,"email":3,"emulation":1,"encyclopedia":3,"environment":1,"ericsson":1,"ethernet":31,"evolution":1,"extension":1,"fabrics":1,"favicon":1,"fcoe":2,"Fedora":4,"FeedBurner":1,"feynman":1,"fibrechannel":1,"filesystem":1,"finance":1,"firebug":1,"firefox":11,"fitness":1,"flash":5,"flickr":1,"flowchart":1,"forms":1,"fp":1,"fpga":2,"fractals":1,"framework":18,"free":1,"ftp":1,"G.ufatn":1,"gadgets":2,"gae":2,"gears":1,"generator":1,"gmail":2,"google":22,"google-charts":1,"GoogleAPI":2,"googlecode":2,"GoogleGears":1,"googlereader":1,"graph":1,"graphics":2,"graphs":1,"greasemonkey":6,"grid":3,"gwt":12,"hardware":1,"hosting":9,"hotkeys":1,"howto":2,"html":7,"human-interface":1,"ical":1,"icon":1,"icons":8,"identity":2,"ietf":1,"iframe":1,"im":1,"image-sharing":1,"immobilier":1,"index":1,"industry-alliance":2,"Industry-Association":1,"installer":2,"intellectual-property":2,"internet":2,"investing":1,"ip":5,"ipv6":1,"irc":1,"issues":1,"ITU-T":1,"java":10,"javascript":70,"jobs":1,"jquery":16,"jsmin":1,"json":1,"kernel":1,"keyboard":1,"klm":1,"language":4,"last.fm":2,"launcher":1,"learning":1,"libraries":1,"library":7,"links":1,"Linux":11,"LISP":4,"lists":1,"livejournal":1,"log":1,"mailing":1,"map":1,"maps":4,"marketing":1,"mashup":7,"math":1,"MathLab":1,"md5":1,"media":1,"Mediawiki":10,"mediawiki_sites":28,"memristor":1,"microsoft":1,"Mind":1,"mind-mapping":2,"mindmap":11,"mindmapping":4,"minifier":1,"mobile":5,"monitoring":3,"montreal":1,"mozilla":2,"multimedia":1,"music":1,"my-contracts":2,"my-diagrams":17,"my-mindmaps":6,"my-pipes":1,"my-projects":8,"my-stuff":5,"mylyn":1,"mysql":4,"netvibes":1,"network-management":1,"network-operator":27,"networking":3,"news":2,"no_tag":31,"nwa":1,"oauth":1,"obfuscator":1,"office":1,"ontology":1,"open":1,"open-source":1,"opencalais":1,"openid":7,"opensource":43,"opml":1,"optimization":4,"organizer":1,"orm":2,"OS":1,"p2p":1,"paper":1,"Password":1,"patterns":1,"PC":1,"pdo":1,"pear":6,"pear-channel":19,"pecl":1,"performance":3,"persistence":1,"Personal-Computer":1,"PHING":1,"photography":1,"photos":2,"photoshop":1,"PHP":83,"PHPUnit":1,"physics":1,"pipes":1,"platform":1,"plugin":2,"pollution":1,"pon":1,"printing":1,"productivity":2,"profiling":1,"programming":73,"propel":1,"proposals":1,"protection-intellectual-property":1,"proxy-fight":1,"ptr_companies":1,"publishing":1,"purl":1,"python":9,"quebec":2,"red5":1,"RedHat":1,"redirect":1,"reference":9,"regex":1,"research":1,"resources":4,"rest":1,"reuters":1,"rpm":1,"rss":14,"ruby-on-rails":1,"s3":6,"scheme":1,"science":1,"screensaver":1,"scripts":1,"sdo":2,"search":2,"security":8,"semanticweb":7,"semiconductor":50,"semiconductors":1,"seo":2,"seotools":1,"server":4,"Server-Software":1,"service":2,"services":2,"sha1":1,"sharing":2,"shopping":1,"shortcuts":1,"SimpleDb":1,"skyteam":1,"slimserver-related":1,"sniffer":1,"social":7,"socket":1,"software":14,"spam":3,"specification":1,"spider":1,"spreadsheet":2,"sprite":1,"spy":1,"standard":1,"standards":6,"startup":11,"state-machine":1,"statistics":2,"storage":16,"subversion":4,"svg":1,"svn":3,"swf":1,"tagcloud":3,"tax":1,"tech":1,"technology":4,"telecom":3,"telecomms":172,"template":2,"templating":1,"testing":5,"textmining":1,"tinyurl":1,"tips":1,"toolkit":1,"tools":20,"tor":1,"traffic":1,"travel":1,"tutorial":12,"tutorials":1,"twisted":1,"ubuntu":1,"uml":1,"Unicode":1,"UnifiedModeling":1,"unix":1,"url":1,"usb":1,"utca":9,"UTF-8":1,"utilities":1,"validation":1,"video":3,"videos":1,"virtual_machine":1,"virtualization":5,"visualization":9,"voice":7,"voip":1,"VPS":1,"wcdma":1,"wdm":24,"web":38,"web2.0":81,"webdesign":34,"webhosting":1,"webos":1,"webserver":1,"webservices":29,"webtools":1,"whiteboard":2,"widget":4,"widgets":5,"wiki":4,"WiMax":1,"windows":7,"wireless":19,"wysiwyg":2,"xcache":1,"xen":1,"xhtml":1,"xml":3,"XSS":1,"yahoo":4,"yahoopipe":8,"Yum":1,"Zend":4,"zendframework":2})
*/