#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__email     = "python (at) jldupont.com"
__fileid    = "$Id: TestInheritance.py 899 2009-05-04 18:08:51Z JeanLou.Dupont $"


class X(object):
    pass

class Y(X):
    def getClass(self):
        return self.__class__
    

y=Y()
print y.getClass()
