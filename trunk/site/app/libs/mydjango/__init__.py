"""
    @author: Jean-Lou Dupont
"""
import logging
from types import *

import django
import django.conf


try:
  django.conf.settings.configure(
    DEBUG=False,
    TEMPLATE_DEBUG=False,
  )
except (EnvironmentError, RuntimeError):
  pass


import django.template
import django.template.loader

def render(template_name, template_dict, debug=False):
    #logging.info("template_name: %s" % template_name)
    #logging.info("template dict: %s" % str(template_dict))
    tpl = django.template.loader.get_template(template_name)
    try:
        rendered = tpl.render( django.template.Context(template_dict) )
    except Exception,e:
        logging.warn('Rendering error template[%s] msg[%s]' % (template_name, e) )
    return rendered

def makelist(target):
    def wrapper(*arg):
        assert(len(arg)==1)
        obj = arg[0]
        if (type(obj) is not ListType):
            obj = [obj,]
        return target(obj)
    return wrapper    

@makelist
def unquote(obj):
    """ Remove single/double quotes from the list of strings
    """
    liste = []
    for o in obj:
        item = o
        if (o is not None):
            item = o.lstrip("'\"").rstrip("'\"")
        liste.append(item)

    return liste

def verifyQuotes(tag_name, obj):
    """ Verifies that the input object (either list or string)
        is single or double quoted.
        @param tag_name: context information
        @param obj: input obj (either list of strings or single string) 
    """
    if (obj is None):
        return
    
    if (type(obj) is StringType):
        obj = [obj]
    
    t = type(obj)
    if (t is ListType or t is TupleType):       
        for o in obj:
            if not (o[0] == o[-1] and o[0] in ('"', "'")):
                logging.warn('verifyQuotes: error')
                raise django.template.TemplateSyntaxError, "%r tag's parameter[%s] be in quotes" % (tag_name, o)
    else:
        raise django.template.TemplateSyntaxError, "%r tag's: verifyQuotes only handles string, list of strings and tuple of strings" % tag_name
                
def setConfig( name, value ):
    try:
        setattr(django.conf.settings, name, value )
    except (EnvironmentError, RuntimeError):
        logging.error('libs.mydjango: error setConfig[%s, %s]' % (name, value))   

# =====================================================

# === TAGS ===
django.template.add_to_builtins( "libs.mydjango.anchor" )
django.template.add_to_builtins( "libs.mydjango.include" )
django.template.add_to_builtins( "libs.mydjango.vars" )
django.template.add_to_builtins( "libs.mydjango.loadpage" )

# === FILTERS ===
django.template.add_to_builtins( "libs.mydjango.filters" )

