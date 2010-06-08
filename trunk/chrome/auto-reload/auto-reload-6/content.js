/**
 * @title  Chrome Extension: Auto-Reload
 * @file   content.js
 * @author Jean-Lou Dupont
 * @desc   Content Script
 */

var timer_id;
var port;
var default_inter=30;
var default_randomize=0;
var default_active_time_range=false;
var default_begin_range=7*60;
var default_end_range=20*60;

function checkTimeRange(tr) {
	var cdate=new Date();
	var ch= cdate.getHours();
	var cm= cdate.getMinutes();
	var ct=(ch*60)+cm;

	console.log("current: "+ ch + ":"+ cm + "("+ct+")");
	console.log("begin range: "+tr.begin_range+" , end_range: "+tr.end_range);
	
	return (ct >= tr.begin_range) && (ct <= tr.end_range);
}


function reloader(cmd) {

	var state     = cmd.state     || false;
	var timeout   = cmd.timeout   || default_inter;
	var randomize = cmd.randomize || default_randomize;
	var active_time_range = cmd.active_time_range || default_active_time_range;
	
	var time_range = {
		begin_range: cmd.begin_range || default_begin_range,
		end_range:   cmd.end_range   || default_end_range
	};
	
	console.log("Active Time Range: "+active_time_range);
	
	timeout = timeout * 1000;
	console.log("Base timeout: " + timeout + ", randomize (%): "+ randomize);
	
	timeout = timeout + timeout * (randomize/100 * Math.random());
	console.log("Randomize timeout: "+timeout);
	
	console.log("begin_range: "+time_range.begin_range+", end_range: "+time_range.end_range);
		
	
	//we already are waiting to be reloaded
	if (state && timer_id) 
		return;
	
	if (state) {
		timer_id=setTimeout(function() {
			
			// is there a "disabled_time_range" active?
			if (active_time_range != false) {
				if (checkTimeRange(time_range))
					window.location.reload(true);
			} else {
				window.location.reload(true);	
			}
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
