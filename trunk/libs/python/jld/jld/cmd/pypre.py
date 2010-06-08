#!/usr/bin/env python
""" A simple preprocessor based on the Mako template engine
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: pypre.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $"

import os
import sys
from optparse import OptionParser

import jld.template as tpl

_options =[]

_usage = """%prog [options] input_file   

Preprocessor based on the Mako template engine
version $LastChangeRevision$ by Jean-Lou Dupont
"""


def main():
    parser = OptionParser( usage=_usage )
    for o in _options:
        parser.add_option( o['o1'], 
                           dest=o['var'], 
                           action=o['action'], 
                           default=o['default'] )
    
    (options,args) = parser.parse_args()

    if len(args) < 1:
        print "Error: not enough arguments"
        return 0

    input  = args[0]
    
    if (not os.path.isfile(input)):
        print "Error: invalid input_file parameter"
        return 0
    
    try:    
        template = tpl.Tpl( input )
        result = template.render()
    except Exception,e:
        print "Error: template processing failed [%s]" % e
        return 0
        
    print result
    
# ==============================================
# ==============================================

if __name__ == "__main__":
    sys.exit( main() )