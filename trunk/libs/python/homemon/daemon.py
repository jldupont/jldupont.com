#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
    
    States:
        Init:     daemon is being initialized
        Waiting:  daemon is waiting to serve
        Serving:  daemon is serving 
        Error:    daemon can't serve because of an error
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: daemon.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $"

from Phidgets.PhidgetException import *
from Phidgets.Events.Events import *
from Phidgets.Devices.InterfaceKit import *


import jld.tools.daemon as daemon

# - Receive config
# - Open device
# -- Wait for attach
# - Loop
# - Lookup event
# - Write event to log


states = {  'init':     0,     
            'waiting':  1,
            'serving':  2,
            'error':    3,
          }

class HomeMonDaemon(daemon.BaseDaemon):
    """ Serves one device
    """
    def __init__(self):
        daemon.BaseDaemon.__init__(self)
        self.ikit = None
        
        self.deviceName = None
        self.inputs = None
        self.outputs = None
    
        self.state = states['init']
    
    def init(self):
        """ Init 
        """
        try:
            self.ikit = InterfaceKit()
            
            self.ikit.setOnAttachHandler(self.inferfaceKitAttached)
            self.ikit.setOnDetachHandler(self.interfaceKitDetached)
            self.ikit.setOnErrorhandler(self.interfaceKitError)
            self.ikit.setOnInputChangeHandler(self.interfaceKitInputChanged)
            self.ikit.setOnOutputChangeHandler(self.interfaceKitOutputChanged)
            self.ikit.setOnSensorChangeHandler(self.interfaceKitSensorChanged)
        except Exception,e:
            pass
        
    #=========================
    # interface kit callbacks
    #=========================
    def interfaceKitAttached(self):
        """
        """

    def interfaceKitDetached(self):
        """
        """
        
    def interfaceKitError(self):
        """
        """
    def interfaceKitInputChanged(self):
        """
        """
    def interfaceKitOutputChanged(self):
        """
        """
    def interfaceKitSensorChanged(self):
        """ 
        """
        
    def loop(self):
        pass
    
    def before_run(self):
        self.init()
    
    def run(self):
        pass
