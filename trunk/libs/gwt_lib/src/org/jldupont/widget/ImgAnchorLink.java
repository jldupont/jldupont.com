/**
 * ImgAnchorLink
 * 
 * @author Jean-Lou Dupont
 */
package org.jldupont.widget;

import com.google.gwt.user.client.DOM;
import com.google.gwt.user.client.Element;
import com.google.gwt.user.client.ui.HasHTML;
import com.google.gwt.user.client.ui.HasName;
import com.google.gwt.user.client.ui.Widget; 

/**
 * Basic implementation of raw Anchor widget
 * @see http://www.w3schools.com/tagsFetcher/tag_a.asp
 * @author Peter Blazejewicz
 * @author Jean-Lou Dupont
 */

public class ImgAnchorLink extends Widget
	implements HasHTML, HasName {

  /* ===============================================================================
   *  PROPERTIES
   ===============================================================================*/
	
  public ImgAnchorLink() {
	  super();
	  setElement(DOM.createAnchor());
  }
  /**
   * Get Href from anchor element
   * @return DOM element 
   */
  public String getHref() {
    return DOM.getElementAttribute(getElement(), "href");
  }
  /**
   * Get HTML from anchor element
   * @return String
   */
  public String getHTML() {
    return DOM.getInnerHTML(getElement());
  }

  public String getId() {
    return DOM.getElementAttribute(getElement(), "id");
  }

  public String getName() {
    return DOM.getElementAttribute(getElement(), "name");
  }

  public int getTabIndex() {
    return $getTabIndex(getElement());
  }

  public String getTarget() {
    return DOM.getElementAttribute(getElement(), "target");
  }

  public String getText() {
    return DOM.getInnerText(getElement());
  }

  public void setHref(String href) {
    DOM.setElementAttribute(getElement(), "href", href == null ? "" : href);
  }

  public void setHTML(String html) {
    DOM.setInnerHTML(getElement(), html == null ? "" : html);
  }
  public void setClass( String classe ) {
	  DOM.setElementAttribute(getElement(), "class", classe == null ? "" : classe);
  }
  public void setId(String id) {
    DOM.setElementAttribute(getElement(), "id", id == null ? "" : id);
  }

  public void setName(String name) {
    DOM.setElementAttribute(getElement(), "name", name == null ? "" :name);
  }
  
  public void setTitle(String title) {
	    DOM.setElementAttribute(getElement(), "title", title == null ? "" : title);
  }

  public void setTabIndex(int index) {
    $setTabIndex(getElement(), index);
  }

  public void setTarget(String target) {
    DOM.setElementAttribute(getElement(), "target", target == null ? "" : target);
  }

  public void setText(String text) {
    DOM.setInnerText(getElement(), text == null ? "" : text);
  }
  /* ===============================================================================
   *  Listener interface
   ===============================================================================*/
  public void setListeners(String[] liste) {
	  
  }
  
  /* ===============================================================================
   *  IMG child node
   ===============================================================================*/
  
  public void setImgUrl(String url) {
	  Element img = createImgElement();
	  
	  // set the img tag src attribute
	  DOM.setImgSrc(img, url);
  }
  
  public void setImgClass( String classe ) {
	  Element img = createImgElement();
	  
	  DOM.setElementAttribute( img, "class", classe == null ? "" : classe);
  }
  
  private Element createImgElement() {
	  Element $this = getElement();
	  Element img = DOM.getChild( $this, 0);
	  
	  if ( img == null ) {
		  img = DOM.createImg();
		  DOM.insertChild($this, img, 0);
	  }
	  
	  return img;
  }
  /* ===============================================================================
   *  PRIVATE
   ===============================================================================*/
  
  private native int $getTabIndex(Element element) /*-{
      return element.tabIndex;
  }-*/;

  private native void $setTabIndex(Element element, int index) /*-{
      element.tabIndex = index;
  }-*/;

}
