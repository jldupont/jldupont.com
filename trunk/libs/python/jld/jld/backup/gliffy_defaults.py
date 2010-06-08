""" Gliffy Backup utility
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: gliffy_defaults.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

import os
import jld.tools.defaults as Defaults

class Gliffy_Defaults(Defaults.Defaults):
    _path = os.path.dirname(__file__)+os.sep+'gliffy_defaults.yaml'

    def __init__(self):
        Defaults.Defaults.__init__(self)
