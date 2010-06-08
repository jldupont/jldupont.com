/**
 * jld.js
 * 
 * @author Jean-Lou Dupont
 * @dependency jQuery
 */
 
if ( typeof JLD =="undefined" ) {

	// object literal
	//  Static class declaration
	var JLD = {};
	
	/**
	 * DOM elements should be as follows:
	 * <tag class="JLD_FETCH" src="whatever-url"></tag>
	 */
	JLD.doFetches = function() {

		$(".JLD_FETCH").each(
			function() {
				src = this.getAttribute('src');
				$(this).append(
					"<script src='"+src+"' type='text/javascript' ></script>"
				);
			
			}
		);
	}

	/**
	 * Should be called once the DOM is ready
	 */	
	JLD.init = function() {
		JLD.doFetches();
	}
}