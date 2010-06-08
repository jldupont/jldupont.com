/**
 * LocalStoreException
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.localstore;

import java.lang.Exception;

public class LocalStoreException 
	extends Exception {

	final static long serialVersionUID = 0L;
	
	public LocalStoreException(String msg) {
		super(msg);
	}
}
