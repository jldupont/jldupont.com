/*
 * static_class.js
 * 
 * Example definition of a static class in Javascript.
 * 
 * @author Jean-Lou Dupont
 * http://jldupont.com
 */

// Function declaration
//  Serves as "static class" declaration
//  Will be attached to the 'window' object
//  by default.
JLD = function() {
  // Static variable
  //  Only accessible within 'JLD'		
  var var1 = 666;
}
	
// Static Function declaration
//  under the "static class" JLD
//  Typeof JLD.fnc1 = "function"
JLD.fnc1 = function() {

  // can't access var1 inside function JLD
  alert( "JLD.fnc1 called. var1= " + JLD.var1 );
}

// 'fields' of JLD
JLD.var2 = 777;             //typeof JLD.var2 = "number"
JLD.var3 = "some string";   //typeof JLD.var3 = "string"

// Static Function declaration
//  under the "class" JLD
JLD.fnc2 = function() {
	
  // can access var2 though
  alert( "JLD.fnc2 called. var2= " + JLD.var2 );
}

