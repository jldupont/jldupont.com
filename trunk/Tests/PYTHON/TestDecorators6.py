#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestDecorators6.py 842 2009-01-23 19:29:02Z JeanLou.Dupont $"

class deco(object):
    
    def __init__(self, *pargs, **kargs):
        """ Grab the parameters to the decorator here
        """
        print "deco.__init__ pargs[%s] kargs[%s]" % (pargs, kargs)        

    def __call__(self, *pargs, **kargs):
        """ Grab the function to decorate here
        """
        func = pargs[0]
        print "deco.__call__ pargs[%s] kargs[%s]" % (pargs, kargs)
        
        def new_func( *pnargs, **knargs):
            """ Grab the the target's "self" parameter as pnargs[0]
            """
            this = pnargs[0]
            #print "new_func:  this.param[%s] pnargs[%s] knargs[%s]" % (this.param, pnargs, knargs)
            print "new_func: this.param[%s]" % this.param
            return func(*pnargs, **knargs)
            
        return new_func        


class Test(object):
    
    def __init__(self, param):
        self.param = param
        
    def __call__(self, *pargs, **kargs):
        print "Test.__call__ pargs[%s] kargs[%s]" % (pargs, kargs)
    
    @deco('deco-to-func1')
    def func1(self, *pargs, **kargs):
        print "Test.func1: pargs[%s] kargs[%s] param[%s]" % (pargs, kargs, self.param)

    @deco('deco-to-func2')
    def func2(self, *pargs, **kargs):
        print "Test.func2: pargs[%s] kargs[%s] param[%s]" % (pargs, kargs, self.param)

    @deco('deco-to-func3')
    def func3(self, param31, param32):
        print "func3: param31[%s] param32[%s]" % (param31,param32)

def tests():
    """

>>> t= Test("param1")
>>> t.func1("fnc1.param")
new_func: this.param[param1]
Test.func1: pargs[('fnc1.param',)] kargs[{}] param[param1]
>>> t.func2("fnc2.param")
new_func: this.param[param1]
Test.func2: pargs[('fnc2.param',)] kargs[{}] param[param1]
>>> t.func3("value1", "value2")
new_func: this.param[param1]
func3: param31[value1] param32[value2]
"""

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod()
