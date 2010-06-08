#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestDecorators4.py 821 2009-01-17 12:08:03Z JeanLou.Dupont $"


class deco(object):
    
    def __init__(self, *pargs, **kargs):
        """ Grab the parameters to the decorator here
        """
        #print "__init__ params[%s]" % params
        self.pargs = pargs
        self.kargs = kargs

    def __call__(self, func):
        """ Grab the function to decorate here
        """
        self.original_func = func
        #print "in-call func[%s]" % func
        return self.new_func
    
    def new_func(self, *pargs, **kargs):
        """ Acts pretty much as a function replacement
        """
        from_orig = self.original_func( *pargs, **kargs )
        return "from new_func self.pargs[%s] self.kargs[%s] pargs[%s] kargs[%s]" % (self.pargs, self.kargs, pargs, kargs) 
    

@deco('param1')
def test(*args):
    return "in-test %s" % args


class Tests():
    """
    >>> result = test("p2")
    >>> print result
    in-test
    """

# ==============================================
# ==============================================

if __name__ == "__main__":
    import doctest
    doctest.testmod()#verbose=True, report=True)
