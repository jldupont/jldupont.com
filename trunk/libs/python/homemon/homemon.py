#!/usr/bin/env python
""" Home Monitoring

    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: homemon.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $"

import sys
import logging
import os.path
from types import *
from optparse import OptionParser

import jld.registry       as reg
from   jld.tools.ytools   import Yattr, Ymsg
from   jld.cmd_g2.base_ui import BaseCmdUI 
from   jld.tools.template import ExTemplate
import jld.tools.logger   as _logger

from cmd import HomeMonCmd
from daemon import HomeMonDaemon

try:
    config = Yattr(__file__, 'config.yaml')
except:
    print "default 'config.yaml' file corrupted"
    sys.exit(1)

# ========================================================================================
_options =[
  {'o1':'-c', 'var':'config_configfile', 'action':'store_true', 'help':'config_configfile', 'reg': False, 'default': config.path },
]

def main():

    try:
        msgs   = Ymsg(__file__)
    except:
        print "default 'messages.yaml' file corrupted"
        sys.exit(1)

 
        
    # == defaults ==
    # ==============
    defaults = {'default_configfile':config.path}
    
    # == Config UI ==
    # =============== 
    ui     = BaseCmdUI(msgs)
    
    # all the exceptions are handled by 'ui'
    try:
        cmd = HomeMonCmd()  
        cmd.msgs = msgs
        
        usage_template = """%prog [options] command
    
version $Id: homemon.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $ by Jean-Lou Dupont

*** Home Monitoring ***

Commands:
^^{commands}"""

        tpl = ExTemplate( usage_template )
        usage = tpl.substitute( {'commands' : cmd.commands_help} )

        # Use OptParse to process arguments
        ui.handleArguments(usage, _options, help_params=defaults)
                        
        # Configure ourselves a logger
        logger = _logger.logger('homemon', include_console = True, include_syslog = False )

        cmd.logger = logger
        ui.logger  = logger

        # == configuration ==
        cmd.config_configfile = ui.getOption('config_configfile')
        
        # == command validation ==
        # ========================
        if ui.command is None:
            sys.exit(0)
               
        cmd.validateCommand(ui.command)       

        # inject a daemon instance 
        cmd.daemon = HomeMonDaemon()
                 
        # get rid of command from the arg list
        ui.popArg()
               
        # == DISPATCHER ==
        # ================
        getattr( cmd, "cmd_%s" % ui.command )(ui.args)
        
    except Exception,e:
        ui.handleError( e )
        sys.exit(1)
        
    sys.exit(0)
    # === END ===

# =======================================================================

if __name__== "__main__":   
    main()
