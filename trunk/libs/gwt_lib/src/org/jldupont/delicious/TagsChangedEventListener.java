/**
 * @author Jean-Lou Dupont
 */
package org.jldupont.delicious;

import com.google.gwt.user.client.EventListener;

public interface TagsChangedEventListener 
	extends EventListener {

	public void addTagsChangedListener(TagsChangedListener s);
	public void removeTagsChangedListener(TagsChangedListener s);

}
