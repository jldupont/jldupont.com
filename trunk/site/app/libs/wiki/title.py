"""
    Title
    
    Valid Patterns:
    > key
    > :key
    > namespace:key
    > :namespace:key
    > domain:namespace:key
    > domain::key

    Invalid title patterns:
    - no ':' allowed
    - chars: 
    
    Processes:
    - decode 'URI' encoding
    
    @author: Jean-Lou Dupont
"""
import sys
import os

__all__     = [ 'Title', 'TitleException' ]
__version__ = "$Id: title.py 662 2008-11-26 02:34:18Z JeanLou.Dupont $"
__author__  = "Jean-Lou Dupont"

class TitleException(Exception):
    """
    Title Exception
    """

class Title(object):
    """
    Represents a 'title' object
        namespace:key
    """
    
    def __init__(self, text = None, ns=None, key=None):
        self.namespace = ns
        self.key = key
        if ((ns is None) or (key is None)):
            self._extract(text)
    
    @staticmethod
    def fromParts(ns,key):
        return Title(ns=ns, key=key)
    
    def namespacePresent(self):
        return len( self.namespace ) != 0
    
    def _extract(self, text):
        """
        Validates and processes 'text'
        """
        parts = text.split(':')
        l = len(parts)
        if (l == 0):
            raise TitleException
        
        # case  'key'
        if (l == 1):
            self.namespace = ''
            self.key = parts[0]
            return
        
        # cases:
        #   ':key'
        #   'namespace:key'
        if (l == 2):
            self.namespace = parts[0]
            self.key = parts[1]
            return

        if (l > 2):
            raise TitleException
        
        #further validation
        self._validate();
        
    #/_extract
    def __str__(self):
        str = self.namespace + ':' + self.key
        return str.strip(':')
    
    def _validate(self):
        """
        Validates the domain & namespace
        """
        return true
    
#=======================================================
#=======================================================
    
if ( __name__ == "__main__" ):
    t1 = Title('key')
    print 'key => %s' % t1
    
    t1b = Title(':key')
    print ':key => %s' % t1b
    
    t2 = Title('namespace:key')
    print 'namespace:key => %s' % t2

    t2b = Title(':namespace:key')
    print ':namespace:key => %s' % t2b

    t4 = Title(':namespace:key')
    print ':namespace:key => %s' % t4
        
    t5 = Title('::key')
    print '::key => %s' % t5

    t3 = Title('domain:namespace:key')
    print 'domain:namespace:key => %s' % t3 
    
    
    