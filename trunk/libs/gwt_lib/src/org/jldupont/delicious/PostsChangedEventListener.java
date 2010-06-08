/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.delicious;

import com.google.gwt.user.client.EventListener;

public interface PostsChangedEventListener 
	extends EventListener {

	public void addPostsChangedListener(PostsChangedListener s);
	public void removePostsChangedListener(PostsChangedListener s);

}
