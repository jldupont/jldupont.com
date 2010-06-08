/**
 * @title  Chrome Extension: Auto-Reload
 * @file   content.js
 * @author Jean-Lou Dupont
 * @desc   Content Script
 */

var timer_id;
var port;
var default_inter=30;

function reloader(cmd) {

	var state  = cmd.state   || false;
	var timeout= cmd.timeout || default_inter; 
	
	timeout = timeout * 1000;
	
	//we already are waiting to be reloaded
	if (state && timer_id) 
		return;
	
	if (state) {
		timer_id=setTimeout(function() {
			window.location.reload(true);
		}, timeout );
		
		console.log(" > auto-reload: scheduled in "+timeout+" ms.");
	} else {
		if (timer_id) {
			clearInterval(timer_id);
			timer_id=undefined;
		}
		console.log(" > auto-reload: none scheduled");
	}
}//

port=chrome.extension.connect();
port.onMessage.addListener(reloader);
