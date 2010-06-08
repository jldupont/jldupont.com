""" Specialized String Template
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: template.py 708 2008-12-04 13:40:35Z JeanLou.Dupont $"

from string import Template

class ExTemplate(Template):
    """String Template to ease integration with other string processing modules e.g. OptionParser
    """
    
    # must appear here to shadow the base class
    delimiter = '^^'
    
    def __init__(self, init):
        Template.__init__(self, init)
