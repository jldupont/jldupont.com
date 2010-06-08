#! /usr/bin/env python
""" Backup for Gliffy diagrams
    @author: Jean-Lou Dupont
    
     1. Scans the Delicious database for entries with the I{my-diagrams} tag
     2. Imports the 'ids' in the Gliffy database
     3. Searches for diagrams that haven't been exported
     4. Retrieves the diagrams through HTTP
     5. Writes the diagrams (all representations) to the export folder
     6. Updates the Gliffy database with the export information (i.e. time of export)
    
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: gliffy.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $"

import sys
import logging
import os.path
from types import *
from optparse import OptionParser

# ASSUME THAT THE REQUIRED LIBS are available
# RELATIVE to this script => simplified install
# TODO good for testing in dev environment,
#      bad for eggs...
levelsUp = 3
path = os.path.abspath( __file__ )
while levelsUp>0:
    path = os.path.dirname( path )
    levelsUp = levelsUp - 1    
sys.path.append( path )

import jld.api as api
import jld.registry as reg

import jld.backup.gliffy_messages as msg
import jld.backup.gliffy_ui as dui
import jld.backup.gliffy_defaults as ddef
from   jld.backup.gliffy_backup import Backup

from   jld.tools.template import ExTemplate
import jld.tools.logger as dlogger

# ========================================================================================
_options =[
  {'o1':'-d', 'var':'dlc_db_path',   'action':'store',        'help':'config_dlc_db_file',   'reg': True, 'default': None},
  {'o1':'-g', 'var':'glf_db_path',   'action':'store',        'help':'config_glf_db_file',   'reg': True, 'default': None},
    
  {'o1':'-p', 'var':'export_path',   'action':'store',        'help':'config_export_path',   'reg': True, 'default': None},
  {'o1':'-m', 'var':'export_maxnum', 'action':'store',        'help':'config_export_maxnum', 'reg': True, 'default': None, 'type':'int'},
           
  {'o1':'-l', 'var':'syslog',        'action':'store_true',   'help':'syslog',        'reg': False, 'default': False },  
]

def main():
    
    msgs   = msg.Gliffy_Messages()
    ui     = dui.Gliffy_UI()
    
    # == Config UI ==
    # ===============
    ui.setParams( msgs )    
    
    # all the exceptions are handled by 'ui'
    try:
        backup = Backup()
        usage_template = """%prog [options] command
    
version $Id: gliffy.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $ by Jean-Lou Dupont

*** Backup utility for Gliffy diagrams (http://www.gliffy.com/) ***

Usage Notes:
 1- This command line utility is meant to complement the cousin 'dlc' command line
 2- The command 'import' is used to import bookmarks from the dlc (Delicious) database
    The command 'import' is to be used with a parameter that denotes the 'tag' used to bookmark the Gliffy diagrams. 
    E.g. glf import my-diagrams
     Will import from the dlc database all the bookmarks tagged with 'my-diagrams'.
 3- The command 'export' is used to retrieve the diagrams from Gliffy.    

The commands which generate log entries are flagged with (logged) below.

Commands:
^^{commands}"""
            
        commands_help = backup.genCommandsHelp()
            
        tpl = ExTemplate( usage_template )
        usage = tpl.substitute( {'commands' : commands_help} )
    
        # Use OptParse to process arguments
        ui.handleArguments(usage, _options)
        
        # Configure ourselves a logger
        _syslog = False if ui.options.syslog else True        
        logger = dlogger.logger('glf', include_console = False, include_syslog = _syslog )

        backup.logger = logger
        ui.logger = logger

        # == configuration ==
        #
        # Process options from the command line:
        #  If an option is missing from the command line, look for it
        #  in the registry.Use conditional 'setKey' if we have valid 
        #  overriding values (i.e. not None) to update the registry.
        #  Finally, for missing parameters, look for defaults.
        #
        # PRECEDENCE:
        #  1) Command Line
        #  2) Registry
        #  3) Defaults
        # ===================
        r = reg.Registry('gliffy')
        ui.updateRegistry(r, _options, ui.options)
        
        params = {}
        
        # integrate options which aren't subjected to the registry
        ui.integrateOptions(ui.options, params, _options)
        
        # integrate default config
        defs = ddef.Gliffy_Defaults()
        ui.integrateDefaults(defs, r, _options, params)

        # Verify parameter type
        ui.verifyType(params, _options)
        
        # Configure Backup cmd object
        ui.copyOptions(params, backup, _options)
        
        # == command validation ==
        # ========================
        try: command = ui.args[0]
        except: command = None
        
        if command is None:
            sys.exit(0)
               
        backup.validateCommand(command)       
                 
        # get rid of command from the arg list
        ui.popArg()
        
        # == DISPATCHER ==
        # ================
        getattr( backup, "cmd_%s" % command )(ui.args)
        
    except Exception,e:
        ui.handleError( e )
        return 1
        
    return 0
    # === END ===

# =======================================================================

if __name__== "__main__":   
    sys.exit( main() )
