/**
 * TagsFetcher mock
 * @author Jean-Lou Dupont
 * 
 * Simulate:
 * - JSON call timeout
 */
package org.jldupont.delicious.mocks;

public class TagsFetcher 
	extends org.jldupont.delicious.TagsFetcher {
	
	final static String thisClass = "org.jldupont.delicious.mocks.TagsFetcher";
	
	public TagsFetcher(String id) {
		super( thisClass, id );
	}
	
	public void get() throws RuntimeException {
		
		//just start operation and a timeout will be generated in x ms
		this.startOperation( 1000 );
	}

	
}//end