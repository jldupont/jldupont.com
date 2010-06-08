#!/usr/bin/env python
""" Template utilities
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: __init__.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"
__dependencies__ = ['mako',]

import os

from mako.template import Template
from mako.lookup import TemplateLookup

class Tpl(object):
    """ Template based on the Mako engine
    """
    def __init__(self, input, dirs = None):
        """ The directory path of the input file
            serves as configuration for the template
            directory by default. If *dirs* is specified,
            it takes precedence.
            
            @param input: the input file (complete file path)
            @param dirs: the template directory list   
            
        """
        self.input = input
        if dirs:
            self.dirs = dirs
        else:
            self.dirs = [ os.path.dirname( input ) ]
            
    def render(self, **params):
        """ Performs the preprocessing.
        
            **Example**
            
            >>> f = os.path.dirname( __file__ ) + '/test.tpl' 
            >>> t = Tpl(f)
            >>> t.render(var='variable')
            'Test Template: variable'
            >>> f2 = os.path.dirname( __file__ ) + '/test2.tpl'
            >>> t2 = Tpl(f2)
            >>> t2.render()
            'Simple template.'
            
            @param params: the input parameters
            @return: rendered text            
        """
        lookup = TemplateLookup(directories = self.dirs) if self.dirs else None
        tpl = Template(filename=self.input, lookup=lookup)
        return tpl.render(**params)
            
            
# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod()
