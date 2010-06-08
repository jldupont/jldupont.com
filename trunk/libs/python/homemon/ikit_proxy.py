#!/usr/bin/env python
""" InterfaceKit proxy

    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: ikit_proxy.py 894 2009-03-25 00:56:18Z JeanLou.Dupont $"

__all__ = ['HomeMonInterfaceKitProxy',]

from jld.tools.proxy import ProxyBaseClass

class HomeMonInterfaceKitProxy( ProxyBaseClass ):
    """ homemon proxy class between the InterfaceKit
        object instance and the Controller FSM
        
        The InterfaceKit object instance must be created
        prior to using this proxy.
    """
    wiring = [('setOnAttachHandler',        'ikit_attach'),
              ('setOnDetachHandler',        'ikit_detach'),
              ('setOnErrorhandler',         'ikit_error'),
              ('setOnInputChangeHandler',   'ikit_input'),
              ('setOnOutputChangeHandler',  'ikit_output'),
              ('setOnSensorChangeHandler',  'ikit_sensor')]
    
    def __init__(self, target, source):
        """ The target will be the Controller
        """
        ProxyBaseClass.__init__(self, target)
        self.source = source
        self.wireEventSources(self.source, self.wiring)
        
        