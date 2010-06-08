/**
 * FreeMind mindmap viewer
 * 
 * @author Jean-Lou Dupont
 * @version 0.1
 * 
 * Example:
 * 
 * <div id="mm"
 * 		src="URL_to_mindmap"
 * 		height="800"
 * 		width="800"  />
 * <script type="text/javascript" src="URL_to_this_script" ></script>
 * 
 */
var userAgent = navigator.userAgent.toLowerCase();

// Figure out what browser is being used
var which_browser = 
{
	version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
	safari: /webkit/.test(userAgent),
	opera: /opera/.test(userAgent),
	msie: /msie/.test(userAgent) && !/opera/.test(userAgent),
	mozilla: /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent)
};

var for_others = 
		"<object classid='java:freemind.main.FreeMindApplet.class' type='application/x-java-applet'"+
		"archive='http://jeanlou.dupont.googlepages.com/freemindbrowser.jar' height='_height_' width='_width_'>"+
		"<param name='scriptable' value='false'>"+
		"<param name='modes' value='freemind.modes.browsemode.BrowseMode'>"+
		"<param name='browsemode_initial_map' value='_src_'>"+
		"<param name='initial_mode' value='Browse'>"+
		"<param name='selection_method' value='selection_method_direct'></object>";

var for_ie = "<object classid='clsid:8AD9C840-044E-11D1-B3E9-00805F499D93'"+
		"codebase='http://java.sun.com/update/1.5.0/jinstall-1_5_0-windows-i586.cab' height='_height_' width='_width_'>"+
		"<param name='code' value='freemind.main.FreeMindApplet'>"+
		"<param name='archive' value='http://jeanlou.dupont.googlepages.com/freemindbrowser.jar'>"+
		"<param name='scriptable' value='false'>"+
		"<param name='modes' value='freemind.modes.browsemode.BrowseMode'>"+
		"<param name='browsemode_initial_map' value='_src_'>"+
		"<param name='initial_mode' value='Browse'>"+
		"<param name='selection_method' value='selection_method_direct'>"+
		"<b>This browser does not have a Java Plug-in.</b><br><a href='http://java.sun.com/products/plugin/downloads/index.html'>Get the latest Java Plug-in here.</a></object>";

// Find the element
var mindmap_div = document.getElementById('mm');
// Retrieve the attributes
var mindmap_src    = mindmap_div.getAttribute( 'src' );
var mindmap_width  = mindmap_div.getAttribute( 'width' );
var mindmap_height = mindmap_div.getAttribute( 'height' );

// Start with the 'right' HTML
var mindmap_innerHTML = (which_browser.msie) ? for_ie : for_others;
// Replace the parameters
mindmap_innerHTML = mindmap_innerHTML.replace(/_src_/gi, mindmap_src ).
										replace(/_height_/gi, mindmap_height ).
										replace(/_width_/gi, mindmap_width );

// Update the element
mindmap_div.innerHTML = mindmap_innerHTML;