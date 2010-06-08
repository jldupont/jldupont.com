#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestWildMethods.py 791 2009-01-13 02:51:39Z JeanLou.Dupont $"

class CX(object):
    
    def __init__(self, method = None):
        self.method = method
    
    def __getattr__(self, name):
        try:
            return object.__getattr__(self, name)
        except AttributeError:
            return CX(name)
            
    def __call__(self, *args):
        return "method [%s] #args[%s]" % (self.method, len(args))
        
        

class CY(CX):
    
    def __init__(self, method=None):        
        CX.__init__(self, method)
        

def tests():
    """
        >>> y = CY()
        >>> z = y.some_method("allo")
        >>> print z
        method [some_method] #args[1]
    """        


# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod()
