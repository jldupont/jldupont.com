#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestCallGetAttr.py 885 2009-03-17 00:18:11Z JeanLou.Dupont $"

# ==============================================
# ==============================================

class X(object):
    
    def __init__(self):
        self.attribute = 888
    
    def __getattr__(self, name):
        #print "__getattr__: name[%s]" % name
        return 666
        
    def __call__(self, input):
        #print "__call__: input[%s]" % input
        result = getattr(self, input)
        return result



# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    
    def tests(self):
        """
        >>> x= X()
        >>> print x('attribute')
        888
        >>> x('allo')
        666
        """
    
    import doctest
    doctest.testmod()
