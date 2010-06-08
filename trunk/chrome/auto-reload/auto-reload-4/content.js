/**
 * @title  Chrome Extension: Auto-Reload
 * @file   content.js
 * @author Jean-Lou Dupont
 * @desc   Content Script
 */

var timer_id;
var port;
var inter=30*1000;

function reloader(state) {

	//we already are waiting to be reloaded
	if (state && timer_id) 
		return;
	
	if (state) {
		timer_id=setTimeout(function() {
			window.location.reload(true);
		}, inter );
		
		console.log(" > auto-reload: scheduled in "+inter+" ms.");
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
