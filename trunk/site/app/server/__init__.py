"""
    Fix paths
    
    @author: Jean-Lou Dupont
"""
import os
import sys

curdir=os.path.dirname(__file__)
libsdir=os.path.join(curdir, "..", "libs")
apath=os.path.abspath(libsdir)
sys.path.insert(0, apath)
