/**
 * JLD_parentCopy.js
 *
 * @author Jean-Lou Dupont
 */
if ( typeof JLD_parentCopy == "undefined" || !JLD_parentCopy ) {

	var JLD_parentCopy = {};
	
	/*
	 * Finds the iframe that created this document
	 */
	JLD_parentCopy.findMatchingIframe = function( doc, title ) {

		var result = undefined;
		var i,j,t,iframes,iframe, nodes;

		// must normalize because of IE...
		title = title.toLowerCase();
		
		iframes = doc.getElementsByTagName("iframe");
		for (i=0; i<iframes.length; i++ ) {
			iframe = iframes[i];
			
			// Mozilla
			if ( iframe.contentDocument != undefined ) 
				t = iframe.contentDocument.title;
			else
				t = iframe.document.title;

			// normalize because of IE...
			// IE concatenates hierarchically the titles
			t = t.toLowerCase();
							
			if ( t.indexOf( title ) != -1 ) {
				result = iframe;
				break;
			}
		}
		return result;
	}
	/**
	 * Creates a <div> element before the "element" in the "doc"
	 * @return new-div-element
	 */
	JLD_parentCopy.createDiv = function( doc, element ) {
		var div,e;
		
		div = doc.createElement("div");
		e = doc.body.insertBefore( div, element );
		
		return e;
	}

	JLD_parentCopy.copy = function( id ) {
		var title,pdoc, iframes, iframe, div, content;
		
		// title of this document
		title = window.document.title;
	
		// locate parent document
		pdoc = window.parent.document;
		
		// get all the iframes from the parent window
		iframes = pdoc.getElementsByTagName("iframe");
		
		// find the matching iframe in the parent window
		iframe = JLD_parentCopy.findMatchingIframe( pdoc, title );
		if ( iframe == undefined)
			return;

		// create a <div> in the parent document
		div = JLD_parentCopy.createDiv( pdoc, iframe );
		
		// content from this document to copy
		content = document.getElementById( id );
		
		// copy!
		div.innerHTML = content.innerHTML;
	}
	
}
