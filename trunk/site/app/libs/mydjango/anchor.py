# @author: Jean-Lou Dupont

import os
import logging

import google.appengine.ext.webapp.template as template
from libs.mydjango import verifyQuotes, unquote
from libs.mydjango.tags import BaseTag

register = template.create_template_register()

@register.tag('a')
def do_anchor(parser, token):
    """
        Anchor
        {% a "url" "text" ["target"] %}
    """
    try:
        liste = token.split_contents()
        tag_name = liste[0]
        url  = liste[1]
        text = liste[2]
    except:
        raise template.django.template.TemplateSyntaxError, "%r tag requires two or three arguments" % token.contents.split()[0]

    target = liste[3] if (len(liste)==4) else None
    if (target):
        verifyQuotes( tag_name, target )
       
    verifyQuotes( tag_name, (url,text) )
    url, text, target = unquote( [url, text, target] )   
    return AnchorNode( url, text, target )

class AnchorNode(template.django.template.Node):
    def __init__(self, url, text, target = None):
        self.url  = url
        self.text = text
        self.target = target
        
    def render(self, context):
        if (self.target):
            return "<a href='%s' target='%s'>%s</a>" % (self.url, self.target, self.text)
        
        return "<a href='%s'>%s</a>" % (self.url, self.text)

# ============================================================
class AnchorImageNode(BaseTag):
    _tpl = "<a href='$href' title='$title' $target_key$target><img src='$src'></a>"
    _paramsRequired = ['href', 'title', 'src']
    _paramsOptional = [['target','target_key','target=',None],]

@register.tag('ai')
def do_anchor_image(parser, token):
    """
        Anchor with Image
        {% ai2 "url" "title" "src" ["target"] %}
    """
    _min = AnchorImageNode.numMinParams()
    _max = AnchorImageNode.numMaxParams()
    
    try:
        liste = token.split_contents()
        _num = len(liste) - 1
        assert( (_num <= _max) and (_num >= _min) )        
    except:
        raise template.django.template.TemplateSyntaxError, "%r tag requires three arguments" % token.contents.split()[0]
    
    tag_name = liste.pop(0)
    verifyQuotes(tag_name, liste)
    uparams = unquote( liste )

    return AnchorImageNode( uparams )
