"""
    Django tag {% i %} for parametrized page inclusion
    @author: Jean-Lou Dupont
"""

import os
import google.appengine.ext.webapp.template as template
import libs.wiki.page as Page
from libs.mydjango import verifyQuotes


register = template.create_template_register()

def do_include(parser, token):
    """
        include
        {% i "page" "param1-value"... %}
    """
    try:
        liste    = token.split_contents()
        tag_name = liste[0]
        page     = liste[1]
        params   = liste[2:]
    except:
        raise template.django.template.TemplateSyntaxError, "%r tag requires at least 2 arguments" % token.contents.split()[0]

    verifyQuotes( tag_name, page )
    verifyQuotes( tag_name, params )
    
    return IncludeNode( page[1:-1], params )

class IncludeNode(template.django.template.Node):
    """
        {% i "page-name" ["param0-value"] ["param1-value"] ...
        
        where $paramX$ located in "page-name"
    """
    def __init__(self, page, params):
        self.page   = page
        self.params = params
        
    def render(self, context):
        content = str( Page.Page.load( self.page ).content )
        if (content is None or self.params is None):
            return None
        index = 0
        for p in self.params:
            content = content.replace( "$param%i$" % index , p[1:-1] )
            index += 1
            
        return content
    
# REGISTER WITH DJANGO
register.tag( 'i', do_include )

def do_cond_include(parser, token):
    """
        conditional include
        {% ci variable "page" "param1-value"... %}
    """
    try:
        liste    = token.split_contents()
        tag_name = liste[0]
        var      = liste[1]
        page     = liste[2]
        params   = liste[3:]
    except:
        raise template.django.template.TemplateSyntaxError, "%r tag requires at least 3 arguments" % token.contents.split()[0]

    verifyQuotes( tag_name, page )
    verifyQuotes( tag_name, params )
    
    return CondIncludeNode( var, page[1:-1], params )

class CondIncludeNode(template.django.template.Node):
    """
        {% ci variable "page-name" ["param0-value"] ["param1-value"] ...
        
        where $paramX$ located in "page-name"
    """
    def __init__(self, var, page, params):
        self.page   = page
        self.params = params
        self.var    = var
        
    def render(self, context):
        # check condition
        if ( not template.django.template.resolve_variable(self.var, context) ):
            return ''
        
        content = str( Page.Page.load( self.page ).content )
        if (content is None or self.params is None):
            return None
        index = 0
        for p in self.params:
            content = content.replace( "$param%i$" % index , p[1:-1] )
            index += 1
            
        return content

# REGISTER WITH DJANGO
register.tag( 'ci', do_cond_include )

# ==============================================================================================

def do_cond_fork_include(parser, token):
    """
        conditional fork include
        {% cfi variable "page-true" "page-false" "param1-value"... %}
    """
    try:
        liste      = token.split_contents()
        tag_name   = liste[0]
        var        = liste[1]
        page_true  = liste[2]
        page_false = liste[3]
        params     = liste[4:]
    except:
        raise template.django.template.TemplateSyntaxError, "%r tag requires at least 4 arguments" % token.contents.split()[0]

    verifyQuotes( tag_name, page_true  )
    verifyQuotes( tag_name, page_false )
    verifyQuotes( tag_name, params )
    
    return CondForkIncludeNode( var, page_true[1:-1], page_false[1:-1], params )

class CondForkIncludeNode(template.django.template.Node):
    def __init__(self, var, page_true, page_false, params):
        self.page_true  = page_true
        self.page_false = page_false
        self.params     = params
        self.var        = var
        
    def render(self, context):
        # check condition
        try:
            fork = template.django.template.resolve_variable(self.var, context)
        except:
            fork = true
            
        if ( fork ):
            content = str( Page.Page.load( self.page_true ).content )
        else:
            content = str( Page.Page.load( self.page_false ).content )
        
        if (content is None or self.params is None):
            return None
        index = 0
        for p in self.params:
            content = content.replace( "$param%i$" % index , p[1:-1] )
            index += 1
            
        return content

# REGISTER WITH DJANGO
register.tag( 'cfi', do_cond_fork_include )
