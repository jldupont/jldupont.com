""" 
    Django tags
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: tags.py 678 2008-11-27 19:46:38Z JeanLou.Dupont $"

import logging
from string import Template

import google.appengine.ext.webapp.template as template


class BaseTag(template.django.template.Node):
    """ Base class for simple Django tags
        Example of a template:
         _tpl = "Required param=$param, optional $oparam_key$oparam_value"
         _paramsRequired = ['param',]
         _paramsOptional = [['oparam_value','oparam_key','oparam_key_label','default'],]
         
         The list _paramsOptional contains lists where the first item
         specified the optional parameter value to look for; if the
         parameter is not specified in 'params' (in the constructor),
         the corresponding 2nd item of the tuple is pruned.
    """
    # must be subclassed
    _tpl = ""
    _paramsRequired = []
    _paramsOptional = []
    
    def __init__(self, params):
        self.params = {}
        self.template = Template( self._tpl )
        self._formatList( params )
    
    def _formatList(self, params):
        for p in self._paramsRequired:
            self.params[p] = params.pop(0)
            
        for e in self._paramsOptional:
            key = e[0]
            if (not len(params)):
                break
            self.params[key] = params.pop(0) 
    
    def render(self, context):
        #go through the list of optional
        #parameters to see if we need to prune
        #ones without a value
        #   Case 1) value is present => key label
        #     key_label=value
        #   Case 2) value is absent and default is None
        #     (nothing)
        #   Case 3) value is absent and default is present
        #     key_label=default
        for oparam_value, oparam_key, oparam_key_label, default in self._paramsOptional:
            if not self.params.has_key(oparam_value):
                key   = oparam_key_label if (default is not None) else ''
                value = default if (default is not None) else ''
                self.params[oparam_key]   = key
                self.params[oparam_value] = value
            else:
                self.params[oparam_key] = oparam_key_label
            
        return self.template.substitute( self.params )

    @classmethod
    def numMinParams(self):
        return len(self._paramsRequired)
    
    @classmethod
    def numMaxParams(self):
        return len(self._paramsRequired) + len(self._paramsOptional)
        