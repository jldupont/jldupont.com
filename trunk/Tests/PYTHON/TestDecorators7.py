#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestDecorators7.py 831 2009-01-19 18:53:11Z JeanLou.Dupont $"

class metadeco(type):
    ""
    def __init__(self, *pargs, **kargs):
        ""
        print "metadeco.__init__: pargs[%s] kargs[%s]" % (pargs,kargs)
    
    def __call__(self, *pargs, **kargs):
        print "deco.decor: pargs[%s] kargs[%s]" % (pargs,kargs)
        
        def wrapper(*wpargs, **wkargs):
            print "__call__.wrapper: pargs[%s] kargs[%s]" % (wpargs,wkargs)
            
        return wrapper
    
    @classmethod
    def decor(cls, *pargs, **kargs):
        print "deco.decor: pargs[%s] kargs[%s]" % (pargs,kargs)


class deco(object):
    
    __metaclass__ = metadeco
    
    """
    >>> d = deco()
    >>> d.method1()
    """
    def __init__(self, *pargs, **kargs):
        ""
        print "deco.__init__: pargs[%s] kargs[%s]" % (pargs,kargs)
        
    @metadeco.decor
    def method1(self, *pargs, **kargs):
        ""
        print "deco.method1: pargs[%s] kargs[%s]" % (pargs,kargs)

        
        


# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """

    import doctest
    doctest.testmod()
