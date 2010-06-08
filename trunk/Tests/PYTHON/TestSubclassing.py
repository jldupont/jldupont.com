#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestSubclassing.py 861 2009-02-27 15:14:29Z JeanLou.Dupont $"

class Base(object):
    ""
    def __init__(self):
        pass
    
    def before_m1(self):
        print "Base::before_m1"
    
    def m1(self):
        print "Base::m1"
        
    def run(self):
        self.before_m1()
        self.m1()
        

class Derived(Base):
    def __init__(self):
        Base.__init__(self)

    def before_m1(self):
        print "Derived::before_m1"
        
    def m1(self):
        print "Derived::m1"
        

def tests():
    """
    >>> b=Base()
    >>> b.run()
    Base::before_m1
    Base::m1
    >>> d=Derived()
    >>> d.run()
    Derived::before_m1
    Derived::m1
    """



# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod()
