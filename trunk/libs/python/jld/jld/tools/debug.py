#!/usr/bin/env python
""" Debug related tools
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: debug.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

import inspect

def lineno():
    """Returns the current line number"""
    return inspect.currentframe().f_back.f_lineno
