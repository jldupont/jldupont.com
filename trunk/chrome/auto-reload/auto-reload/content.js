/**
 * @title  Chrome Extension: Auto-Reload
 * @file   content.js
 * @author Jean-Lou Dupont
 * @desc   Content Script
 */

var timer_id;

function reloader(msg) {
	
	params = msg || {};
	state  = params["state"] || false;
	inter  = (params["timer"] || 0) * 1000;
	
	if (state && timer_id) 
		return;
	
	if (state) {
		timer_id=setTimeout(function() {
			window.location.reload(true);
		}, inter );
		
		console.log("auto-reload: scheduled in "+inter+" ms.");
	} else {
		if (timer_id) {
			clearInterval(timer_id);
			timer_id=undefined;
		}
		
		console.log("auto-reload: none scheduled");
	}
}//

port=chrome.extension.connect();
port.onMessage.addListener(reloader);
