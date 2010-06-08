""" Django filters
    @author: Jean-Lou Dupont
"""
import google.appengine.ext.webapp.template as template
from django.template.defaultfilters import stringfilter

register = template.create_template_register()

@register.filter(name='stripnewlines')
@stringfilter
def stripnewlines(value):
    """ Strips newlines
    """
    value = value.replace("\n",'')
    return value.replace("\r",'')

@register.filter(name='lstriptabs')
@stringfilter
def lstriptabs(value):
    """ Strips leading tabs
    """
    return value.lstrip("\t ")

@register.filter(name='codestrip')
@stringfilter
def codestrip(value):
    """ Removes leading tabs & spaces, trailing newlines
    """
    value = value.replace("\r",'')
    bits = value.split("\n")
    newBits = []
    for bit in bits:
        newBits.append( bit.lstrip("\t ") )
    value = "".join(newBits)
    return value
