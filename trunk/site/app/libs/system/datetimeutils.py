#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: datetimeutils.py 879 2009-03-11 17:59:37Z JeanLou.Dupont $"


def datetimeToRFC822(dt):
    """ e.g. Sat, 07 Sep 2002 00:00:01 GMT
    """
    return dt.strftime("%a, %d %b %Y %H:%M:%S %Z")

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import datetime as dt
    
    def tests():
        """
        >>> d= dt.datetime.now()
        >>> print datetimeToRFC822(d)
        """
    
    import doctest
    doctest.testmod()
