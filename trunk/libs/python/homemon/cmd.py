#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: cmd.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $"

import os
import yaml

from   jld.cmd_g2 import CmdG2, BaseCmdException
from   jld.tools  import ytools as ytools

# imports are checked & errors raised later
try:
    import Phidgets
except:
    pass


        
class HomeMonCmdException(BaseCmdException):
    def __init__(self, msg, params = None):
        BaseCmdException.__init__(self, msg, params)




class HomeMonCmd(CmdG2):

    def __init__(self):
        CmdG2.__init__(self)
        
        self.daemon = None
        
        self.config_configfile = None
        self.config = None
        self.devices = None
        self.default_device = None
        self.ios = None
        self.inputs = None
        self.outputs = None
        
        self._checkDependencies()


    def cmd_start(self, *args):
        "Starts the daemon"
        self.process_config()
        
        
    def cmd_stop(self, *args):
        "Stops the daemon"

    def cmd_restart(self, *args):
        "Re-starts the daemon"
        self.cmd_stop()
        self.cmd_start()

    # =================================
    # PRIVATE
    # =================================
    def process_config(self):
        """Load & validate config"""
        self.config = ytools.loadFromFile(self.config_configfile, 
                                          [HomeMonCmdException, 
                                          'error_load_configfile', {'path':self.config_configfile}])

        self.validate_config()

    def validate_config(self):
        """ Find default-device name
            Find Inputs & Outputs definition under default-device
        """
        try:    self.devices = self.config['devices']
        except: raise HomeMonCmdException('error_configfile_missing_devices')
        
        try:    self.default_device = self.devices['default']
        except: raise HomeMonCmdException('error_configfile_missing_default_device')
        
        try:    self.ios = self.config[ self.default_device ]
        except: raise HomeMonCmdException('error_configfile_missing_ios', {'device':self.default_device})
        
        try:    self.inputs = self.ios['inputs']
        except: raise HomeMonCmdException('error_configfile_missing_inputs', {'device':self.default_device})
        
        try:    self.outputs = self.ios['outputs']
        except: raise HomeMonCmdException('error_configfile_missing_outputs', {'device':self.default_device})

    # =================================
    # PRIVATE
    # =================================
    
    def _checkDependencies(self):
        if "Phidgets" not in globals():
            import jld.tools.exceptions as exceptions
            raise exceptions.ErrorMissingDependency('error_missing_dependency', {'location':'http://www.phidgets.com/','dep':'Phidgets'} )                   

