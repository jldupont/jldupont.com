/*
 * 
 * @author Jean-Lou Dupont
 * 
 */

// Customization section.
// {{
var baseUri = 'http://jldupont.googlecode.com/svn/scripts';
var timeBase = 1000; // in ms.
// }}

var DOMloaded = false;
var ChiliDone = false;

// Grab our timer
jQuery.getScript( baseUri+'/timer/jquery.timer.js',
	function() 
	{ 
		$.timer( timeBase, schedulerTick );
	}
);

function schedulerTick( timer )
{
	var stop_timer;
	
	stop_timer = doChili();
		
	if (stop_timer === true)
		timer.stop();
}

// Load Chili Syntax Highlighter
// -----------------------------
var ChiliBook = {};
ChiliBook.recipesLoaded = false;
ChiliBook.loaded = false;

jQuery.getScript( baseUri+'/chili/jquery.chili-1.9.js',
	function() 
	{ 
		ChiliBook.automaticSelector = 'source';
		// don't start without having swapped 
		// the 'lang' attribute for 'class' ones! (see below)
		ChiliBook.automatic = false;
		ChiliBook.loaded = true;
		doChili();		
	}
);
(function(){
	var s = document.createElement ('link');
	s.type = 'text/css';
	s.rel = 'stylesheet';
	s.href = baseUri+"/chili/recipes.css";
	document.getElementsByTagName('head')[0].appendChild(s);
})(); 

jQuery.getScript( baseUri+'/chili/recipes.js',
	function()
	{
		ChiliBook.recipesLoaded = true;
		doChili();		
	}
);

$(document).ready(
	function()
	{
		$("source[lang]").
			each( function()
			{
				a = this.getAttributeNode('lang').nodeValue; 
				this.setAttribute("class", a);
			}
		);

		DOMloaded = true;
		doChili();
	}// document.ready
);

function doChili()
{
	if (ChiliDone == true)
		return true;

	if ((DOMloaded == false) || (DOMloaded == undefined ))
		return false;
		
	if ((ChiliBook.recipesLoaded == false) || (ChiliBook.recipesLoaded == undefined))
		return false;

	if ((ChiliBook.loaded == false) || (ChiliBook.loaded == undefined))
		return false;

	ChiliDone = true;
		
	$('source').chili();

	return true;
}

// END chili initialization

$(document).ready(
	function()
	{
	}
);
