#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestDecorators5.py 829 2009-01-19 14:09:05Z JeanLou.Dupont $"


class deco(object):
    
    def __init__(self, *pargs, **kargs):
        """ Grab the parameters to the decorator here
        """
        print "__init__ pargs[%s] kargs[%s]" % (pargs, kargs)
        self.pargs = pargs
        self.kargs = kargs

    def __call__(self, *pargs, **kargs):
        """ Grab the function to decorate here
        """
        print "__call__ pargs[%s] kargs[%s]" % (pargs, kargs)
        self.original_func = pargs[0]
        return self.new_func
    
    def new_func(self, *pargs, **kargs):
        """ Acts pretty much as a function replacement
        """
        print "new_func: pargs[%s] kargs[%s]" % (pargs, kargs)
        print self.original_func(*pargs, **kargs)
        #print dir(original_func)
        #print original_func.__dict__
        #print original_func.func_globals
        #from_orig = original_func( original_func.im_self, *pargs, **kargs )
        #return "from new_func self.pargs[%s] self.kargs[%s] pargs[%s] kargs[%s]" % (self.pargs, self.kargs, pargs, kargs) 
    
class Foo(object):
    def __init__(self, init):
        self.init = init

    @deco('deco-param1')
    def fnc1(self):
        print "in: Foo:fnc1 init[%s]" % self.init

# DOES NOT WORK AS EXPECTED
# #########################
class Tests():
    """
    >>> f = Foo("allo")
    >>> f.fnc1()
    """

# ==============================================
# ==============================================

if __name__ == "__main__":
    import doctest
    doctest.testmod()#verbose=True, report=True)
