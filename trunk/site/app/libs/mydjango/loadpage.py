""" Django page loader
    @author: Jean-Lou Dupont
"""

import os
import logging

from django.template import resolve_variable
from django.template.loader import get_template, get_template_from_string, find_template_source

from google.appengine.api import memcache
import google.appengine.ext.webapp.template as template
from google.appengine.api import urlfetch

from libs.mydjango import verifyQuotes, unquote
from libs.mydjango.tags import BaseTag
from libs.tools import web

register = template.create_template_register()

@register.tag('lp')
def do_loadpage(parser, token):
    """
        Loads a page in a variable 
        {% lp "page" "var" %}
    """
    try:
        liste = token.split_contents()
        tag_name = liste[0]
        page = liste[1]
        var  = liste[2]
    except:
        raise template.django.template.TemplateSyntaxError, "%r tag requires two arguments" % token.contents.split()[0]
      
    verifyQuotes( tag_name, (page,var) )
    page, var = unquote( [page, var] )   
    return LoadPageNode( page, var )

class LoadPageNode(template.django.template.Node):
    def __init__(self, page, var):
        self.page = page
        self.var = var
        
    def render(self, context):
        try:    template_name = resolve_variable(self.page, context)
        except: template_name = self.page
            
        template = get_template(template_name)
        page = template.render(context)
        context[self.var] = page
        return ''

# =======================================================================

@register.tag('lpu')
def do_loadpageurl(parser, token):
    """
        Loads a page (through an HTTP URL accessible resource) in a variable 
        {% lpu "url" "var" %}
    """
    try:
        liste = token.split_contents()
        tag_name = liste[0]
        url = liste[1]
        var = liste[2]
    except:
        raise template.django.template.TemplateSyntaxError, "%r tag requires two arguments" % token.contents.split()[0]
      
    verifyQuotes( tag_name, (url,var) )
    url, var = unquote( [url, var] )   
    return LoadPageURLNode( url, var )

class LoadPageURLNode(template.django.template.Node):

    def __init__(self, url, var):
        self.url = url
        self.var = var

    def render(self, context):
        try:    template_name = resolve_variable(self.url, context)
        except: template_name = self.url
            
        tpl,name = web.get( template_name )
        if tpl:
            t = template.Template(tpl)
            page = t.render(context)
        else:
            page = None
        context[self.var] = page
        return ''

