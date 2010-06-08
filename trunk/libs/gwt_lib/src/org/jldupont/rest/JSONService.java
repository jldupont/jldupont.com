package org.jldupont.rest;

import java.util.HashMap;

import org.jldupont.system.JLD_Object;
import org.jldupont.system.Liste;

import com.google.gwt.user.client.rpc.ServiceDefTarget;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.Request;

/**
 * JSONService
 *  
 * @author Jean-Lou Dupont
 * 
 * TODO add HTTP header support
 */
public class JSONService 
	extends JLD_Object 
	implements ServiceDefTarget {

	final static String thisClass = "org.jldupont.rest.JSONService";
	
	String entryPoint = null;
	
	Request        req = null;
	
	int				timeout = 0;
	String			user = null;
	String			password = null;
	
	/*===================================================================
	 * CONSTRUCTORS 
	 ===================================================================*/
	public JSONService( String classe, String id ) {
		super(classe, id, true );
		setup();
	}
	
	public JSONService( String id ) {
		super(thisClass, id, true );
		setup();
	}

	public JSONService() {
		super(thisClass, "default_id", true );
		setup();
	}
	private void setup() {

	}	
	/*===================================================================
	 * PUBLIC 
	 ===================================================================*/
	public void setUser(String user) {
		this.user = new String(user);
	}
	public void setPassword(String password) {
		this.password = new String(password);
	}
	public void setTimeout(int timeout) {
		this.timeout = timeout;
	}
	
	public boolean doGET( HashMap<String,String> urlParams, Liste bodyParams, RequestCallback cb ) throws RequestException {
		return this.doRequest( RequestBuilder.GET, urlParams, bodyParams, cb);
	}

	public boolean doPOST( HashMap<String,String> urlParams, Liste bodyParams, RequestCallback cb ) throws RequestException {
		return this.doRequest( RequestBuilder.POST, urlParams, bodyParams, cb);
	}
	public void cancel() {
		if ( req != null )
			req.cancel();
	}
	public boolean isPending() {
		if ( req != null )
			return req.isPending();
		return false;
	}
	/*===================================================================
	 * PRIVATE 
	 ===================================================================*/
	private boolean doRequest(RequestBuilder.Method m, HashMap<String,String> urlParams, Liste bodyParams, RequestCallback cb ) 
		throws RequestException {
		
		// build the URL params list e.g. ?x=y&x2=y2 ...
		String url = URLParamsList.build( entryPoint, urlParams );
		
		RequestBuilder rb = new RequestBuilder( m, url );
		
		rb.setTimeoutMillis(timeout);
		
		if (user != null)
			if ( user.length() != 0 )
				rb.setUser(user);
		
		if (password != null)
			if (password.length() != 0 )
				rb.setPassword(password);
		
		// Put the 'body' parameters in the request
		req = rb.sendRequest( bodyParams.toString()  , cb );
		
		return false;
	}
	
	/*===================================================================
	 * ServiceDefTarget 
	 ===================================================================*/
	public void setServiceEntryPoint(String address) {
		this.entryPoint = new String( address );
	}
	public String getServiceEntryPoint() {
		return this.entryPoint;
	}
	/*===================================================================
	 * Recycle 
	 ===================================================================*/
	public void _clean() {
		super._clean();
		
		entryPoint = null;
		req = null;
		timeout = 0;
	}
}//