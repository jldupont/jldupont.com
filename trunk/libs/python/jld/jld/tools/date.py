#!/usr/bin/env python
"""Date related tools
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: date.py 728 2008-12-11 16:47:54Z jeanlou.dupont $"

import datetime as datetime

def convertDate( date ):
    """ Converts a date in string format
        to a datastore compatible format
        
        Input format:   YYYY-MM-DD HH:MM:SS.ssssss
        Output format:  datetime
        
        throws: Exception
    """
    sdate = date.split('.')    
    return datetime.datetime.strptime(sdate[0], "%Y-%m-%d %H:%M:%S")

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    print "test!"