/**
 * addToGoogleReader
 * 
 * @author: Jean-Lou Dupont 
 * 
 * @version: $Id$
 */

 // find out if the script is already loaded on the current page
 
 if ( 'undefined' == typeof jld ) {
 	
 	// object literal
 	var jld = {
 	
 		run: function() {
 			var links = document.getElementsByTagName("link");
 			var found = false;
 			
 			// nothing here
 			if ( null != links ) {
	 			for(var i=0;i<links.length;i++) {
	 				var type = links[i].getAttribute("type");
	 				if (("application/rss+xml" == type) || ("application/atom+xml") == type) {
	 					var href = links[i].getAttribute("href");
	 					
	 					//is it a relative href or an "absolute" one?
	 					if ( "http://" != href.substr(0,7).toLowerCase())
	 						href = window.location + "/" + href;
	 					
	 					document.location = "http://www.google.com/ig/add?feedurl=" + href;
	 					found = true;
	 				}
	 			}
	 			
 			}
 			
 			// fall-through if no feed is found
 			if (!found) {
 				alert( 'No feed link found!' );
 			}
 		}
 	
 	};
 	
 }
 
 //Execute!
 jld.run();
 