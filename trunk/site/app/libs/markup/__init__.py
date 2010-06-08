"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: __init__.py 799 2009-01-14 20:16:50Z JeanLou.Dupont $"

from docutils.core import publish_parts
import libs.cache as cache

def getReSText(dirs, fragment_path):
    """ Returns a ReStructuredText rendered file
    
        @raise Warning: if the file isn't found in the dirs 
    """
    content = cache.fetchpage( dirs, fragment_path )
    if content:
        return content
    
    content, abs_path, from_cache_flag = cache.fetchfile( dirs, fragment_path )  
    rendered_page = renderReSText( content )
    cache.storepage( abs_path, rendered_page )
    
    return rendered_page
    
def renderReSText(input, justBody = False):
    """ Returns a rendered ReSTructuredText string
    """
    parts = publish_parts(source=input, writer_name="html4css1")
    
    if justBody:
        rendered_page = parts["fragment"]
    else:
        rendered_page = parts["html_title"] + parts["html_subtitle"] + parts["fragment"]
    
    return rendered_page