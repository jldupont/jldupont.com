"""
    Django database driver template loader
    @author: Jean-Lou Dupont
"""
import os
import logging

from libs.wiki.page import page
from libs.wiki.title import title

from django.template import TemplateDoesNotExist

class Loaded():
    """
        Template accumulator
    """
    list = []
    
    @staticmethod    
    def reset():
        Loaded.list = []
        
def load_template_source(template_name, dirs = None):
    #logging.info("libs.wiki.template_loader.load_template_source: name[%s]" % template_name)
    title = title.Title(template_name)
    page = page.Page.load(title)
    if (page.valid()):
        Loaded.list.append( template_name )
        return (page.content, template_name)
    
    #logging.info("libs.wiki.template_loader.load_template_source: name[%s] NOT FOUND" % template_name)    
    raise TemplateDoesNotExist, "Tried [%s]" % template_name
    
load_template_source.is_usable = True
