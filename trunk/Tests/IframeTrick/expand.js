/**
 * expand.js
 *
 *  Copies the innerHTML of the document's element of id="content"
 *  in the parent window's element identified through the parameter
 *  passed through the iframe that loaded this document.
 *
 * @author Jean-Lou Dupont
 */
 
/**
 * Locates the iframe that loaded this document
 */
function findMatchingIframe( doc, title ) {

	result = undefined;
	
	iframes = doc.getElementsByTagName("iframe");
	for (i=0; i<iframes.length; i++ ) {
		iframe = iframes[i];
		if ( iframe.src.match( title )) {
			result = iframe;
			break;
		}
	}
	return result;
}
/**
 * Retrieves the element id from the src element of the iframe
 * i.e. after the ? delimiter
 */
function getTargetElementId( src ) {

	parts = src.split("?");
	if ( parts.length < 2 )
		return undefined;
	
	return parts[1];	
}

(function() {
		// the name of this document i.e. the <title> element in the <head> section
		title = window.document.title;
	
		// locate parent document
		pdoc = window.parent.document;
		
		// get all the iframes from the parent window
		iframes = pdoc.getElementsByTagName("iframe");
		
		// find the matching iframe in the parent window
		iframe = findMatchingIframe( pdoc, title );
		if ( iframe == undefined)
			return;

		// get target element id
		tid = getTargetElementId( iframe.src );
		if ( tid == undefined )
			return;

		// get the element per-se
		target = pdoc.getElementById( tid );

		// get the content to copy from this document
		container = document.getElementById( "content" );
					
		target.innerHTML = container.innerHTML;
})();
