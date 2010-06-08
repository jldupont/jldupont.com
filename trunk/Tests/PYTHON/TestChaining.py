#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__email     = "python (at) jldupont.com"
__version__ = "x.y.z"
__fileid    = "$Id: TestChaining.py 899 2009-05-04 18:08:51Z JeanLou.Dupont $"

__all__ = ['',]


class Bottom(object):
    def __init__(self):
        print "Bottom.__init__"

    def depthFirst(self):
        print "Bottom"


class Middle(Bottom):
    def __init__(self):
        Bottom.__init__(self)
        print "Middle.__init__"
        
    def depthFirst(self):
        super(Middle, self).depthFirst()
        print "Middle"


class Top(Middle):
    def __init__(self):
        Middle.__init__(self)
        print "Top.__init__"

    def depthFirst(self):
        super(Top, self).depthFirst()
        print "Top"


def test1():
    """
    >>> top=Top()
    Bottom.__init__
    Middle.__init__
    Top.__init__
    >>> top.depthFirst()
    Bottom
    Middle
    Top
    """


# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod(optionflags=doctest.ELLIPSIS)
