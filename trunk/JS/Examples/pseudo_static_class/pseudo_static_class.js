/*
 * pseudo_static_class.js
 * 
 * Example definition of a 'pseudo' static class in Javascript.
 * 
 * @author Jean-Lou Dupont
 * http://jldupont.com
 */

/*
 * FORM1:
 */

	// Function declaration
	//  Serves as "class" declaration
	JLD = function() {
	
		// static variable
		//  with only scope 'JLD'
		//  i.e. can't be access publically 
		//  from outside	
		var var1 = 666;
		
		alert("JLD function called")
	
	}
	
	// Static Function declaration
	//  under the "class" JLD
	JLD.fnc1 = function() {
		
		// can't access variable 'var1' declared inside
		// function JLD
		alert( "JLD.fnc1 called. var1= " + JLD.var1 );
	}
	
/*
 * FORM2:
 */

	JLD2 = function() {
		
		alert("JLD2 function called")		
		
		var var1 = 999;
		
		/*
		 * Function 'fnc1' isn't available
		 * outside of JLD2 
		 */
		fnc1 = function() {
			// can access from here
			alert( "JLD2.fnc1 called. var1= " + var1 );
		}
		
		fnc1();
		
	}
