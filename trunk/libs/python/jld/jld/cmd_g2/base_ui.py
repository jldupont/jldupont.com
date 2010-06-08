#!/usr/bin/env python
""" BaseCmdUI
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: base_ui.py 858 2009-02-26 19:01:29Z JeanLou.Dupont $"

__all__ = ['BaseCmdUI', 'BaseCmdUIConfigError']

import sys
from types import *
from string import Template
from optparse import OptionParser

class BaseCmdUIConfigError(Exception):
    def __init__(self, msg, params = None):
        Exception.__init__(self, msg)
        self.msg = msg
        self.params = params

class BaseCmdUI(object):
    """ Base class for Command Line UI
    
        @see: trns.py for example
    """
    _platform_win32 = sys.platform[:3] == 'win'
    
    def __init__(self, msgs, logger = None):
        """
            @param msgs: dictionary
            @param logger: logger  
        """
        self.logger = logger
        self.msgs = msgs
        self.options = None
        self.args = None
        self.command = None
    
    def setParams(self, msgs):
        """ Generic parameter setting interface
        """
        self.msgs = msgs

    def popArg(self):
        """ Pops one argument from the list
        """
        return self.args.pop(0)

    def getOption(self, key):
        
        if self.options is None:
            return None
        
        try:
            return getattr(self.options, key)
        except:
            pass
        
        return None

    def handleError( self, exc ):
        """ Displays, if required, an appropriate user message
            corresponding to an error condition.
            
            @param exc: Exception being raised
        """
        try:    msg_key = exc.msg    
        except: msg_key = None 

        try:    params = exc.params
        except: params = None            

        if (msg_key):  
            try:    msg = self.msgs.render(msg_key, params)
            except: msg = str(exc)
        else:
            msg = str(exc)
                    
        try:    self.logger.error( msg )
        except: print msg
        
    def _resolveHelp(self, entry):
        
        if (self._platform_win32):
            if ('help_win' in entry):
                return entry['help_win']

        if (not self._platform_win32):
            if ('help_nix' in entry):
                return entry['help_nix']
            
        if ('help' in entry):
            return entry['help']
        
        return None
                
    def handleArguments(self, usage, _options, args = None, help_params = None):
        """ Processes command line options
        """ 
        parser = OptionParser( usage=usage )
        for o in _options:
            help_msg = self.msgs.render( o['help'], params=help_params )
            parser.add_option( o['o1'], 
                               dest=o['var'], 
                               action=o['action'], 
                               help=help_msg, 
                               default=o['default'] )

        (self.options,self.args) = parser.parse_args( args )
        
        try:    self.command = self.args[0]
        except: self.command = None
        
        
    def updateRegistry(self, reg, options, args):
        """Updates the registry from the command-line args"""
        for o in options:
            #if we are told to update the registry
            if ( o['reg'] ):
                key = o['var']
                val = getattr( args, key )
                if (val is not None):
                    #print "updateRegistry: key[%s] val[%s]" % (key, val)
                    reg[key] = val 

    def copyOptions(self, source, target, _options):
        """ Copies all options from source to target

            @param source: the source dictionary
            @param target: the target object with dictionary access
            @param _options: the reference options list
        """
        for o in _options:
            key = o['var']
            val = source[key]
            setattr( target, key, val )

    def integrateDefaults(self, defs, reg, _options, params):
        """Integrates the default values for each option if
            no value can be found in the registry.
            
            @param defs: the defaults dictionary
            @param reg: the registry dictionary
            @param _options: the options list
            @param params: the result dictionary
        """
        for o in _options:
            key = o['var']
            # only options subjected to the registry!
            if (o['reg']):
                val = reg[key]
                if val is None:
                    val = defs[key] if (key in defs) else None                    
                params[key] = val

    def integrateOptions(self, options, params, _options):
        """Integrate options that aren't subjected to the registry
        
            @param options: the current options as parsed from the command line
            @param params: the result dictionary
            @param _options: the reference options list
        """
        for o in _options:
            key = o['var']
            if (not o['reg']):
                val = getattr(options, key)
                params[key] = val

    def verifyType(self, params, _options):
        """ Performs type verification
        """
        for o in _options:
            if ('type' in o):
                key  = o['var']
                tipe = o['type']
                value = params[key]
                #print "key[%s] type[%s] value[%s] type value[%s]" % (key,tipe, value, type(value))
                                
                if (tipe is 'int') and (type(value) is not IntType):
                    try:
                        intVal = int(value)
                        params[key] = intVal
                    except:
                        raise BaseCmdUIConfigError('error_config_type', {'key':key, 'type':tipe})


