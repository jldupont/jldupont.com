""" Command Line tools, generation2

    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: __init__.py 856 2009-02-24 00:09:05Z JeanLou.Dupont $"
__dependencies__ = []

__all__ = ['BaseCmd', 'Hook', 'BaseCmdException']

import os
import sys
from types import *
import subprocess

import jld.tools.printer as printer

class BaseCmdException(Exception):
    def __init__(self, msg, params = None):
        Exception.__init__(self, msg)
        self.msg = msg
        self.params = params

class metaBaseCmd(type):
    
    _prefix = 'cmd_'
    
    def __init__(cls, name, bases, ns):
        cls._extractCommands(ns)

    def _extractCommands(cls, ns):
        ""
        cls.commands = filter( lambda X: str(X).startswith(cls._prefix), ns )
        cls.cmds = map( lambda X: str(X)[len(cls._prefix):], cls.commands )



class BaseCmd(object):
    """ Base class for command line utilities
    
    >>> b = BaseCmd()
    >>> print b.commands
    ['cmd_listconfig']
    >>> print b.cmds
    ['listconfig']
    """

    __metaclass__ = metaBaseCmd
    
    _platform_win32 = sys.platform[:3] == 'win'
    _prefix = 'cmd_'
    
    def __init__(self):
        """ Scans through all the methods of this instance
            and extracts all the ones prefixed with 'cmd_'
        """
        self._genCommandsHelp()
        self._extractCommandsFromClasse()
        
    def _extractCommandsFromClasse(self):
        #commands = filter( lambda X: str(X).startswith(self._prefix) and X not in self.commands, BaseCmd.__dict__)
        commands = filter( lambda X: str(X).startswith(self._prefix), self.__dict__)
        self.commands.extend( commands )
        cmds = map( lambda X: str(X)[len(self._prefix):], commands )
        self.cmds.extend( cmds )


    def _genCommandsHelp(self, padding=15):
        """ Generates the list of commands and their corresponding docstring.
            Methods with prefix 'test' are ignored.
        """
        self.commands_help = ''
        for cmd in self.commands:
            if (cmd.startswith('test')):
                continue

            name = str(cmd)[len(self._prefix):]
            method = getattr(self, cmd)
            try:    doc = getattr(method, '__doc__')
            except: doc = ''

            line = "%*s : %s\n" % (padding,name,doc)
            self.commands_help = self.commands_help + line


    def validateCommand(self, command):
        """ Validates the specified command
        """ 
        if (command not in self.cmds):
            raise BaseCmdException( 'error_invalid_command', {'cmd':command} )

    def _fireEvent(self, path, environ):
        """ Fires the associated Event Manager
        
            @return: (True, None) if the path is not available
            @raise ErrorPopen 
        """
        try:
            em = EventMgr(path, environ)
            if not em.exists():
                return True
            return em.run()
        except:
            raise BaseCmdException('error_eventmgr', {'path':path, 'environ':environ})

    def iterconfig(self):
        """ Iterator for the config_ parameters
        
        >>> b = BaseCmd()
        >>> b.config_a = 'configa'
        >>> b.config_b = 'configb'
        >>> for c in b.iterconfig(): print c # doctest:+ELLIPSIS 
        ('config_...', 'config...')
        ('config_...', 'config...')
        """
        for name in self.__dict__:
            if name.startswith('config_'):
                value = getattr(self, name)
                yield (name, value) 

    def cmd_listconfig(self, *args):
        """Lists the current configuration"""
        p = printer.PrinterConfig( self.msgs, list=self.iterconfig() )
        return p.run()
        
        
class NullLog(object):
    """ Placeholder Null Log
    """
    def __init__(self):
        self.debug_flag = True if __name__=="__main__" else False

    def info(self,msg):
        self._dolog("info: %s" % msg)
    def debug(self,msg):
        self._dolog("debug: %s" % msg)
    def warn(self,msg):
        self._dolog("warn: %s" % msg)
    def error(self,msg):
        self._dolog("error: %s" % msg)
    def _dolog(self,msg):
        if self.debug_flag:
            print msg
        
class CmdG2(BaseCmd):
    """ Base Cmd class with Logging support.
        A Message object must be provided.
    """
    def __init__(self, msgs = None):
        BaseCmd.__init__(self)
        self.logger = NullLog()
        self.msgs = msgs
        
    def logdebug(self, msg, params=None):
        return self._dolog("debug", msg, params)
    
    def loginfo(self, msg, params=None):
        return self._dolog("info", msg, params)
    
    def logwarn(self, msg, params=None):
        return self._dolog("warn", msg, params)
    
    def logerror(self, msg, params=None):
        return self._dolog("error", msg, params)
    
    def _dolog(self, fnc, msg, params):
        if self.msgs:
            txt = self.msgs.render(msg, params)
        else:
            txt = msg
        return getattr(self.logger, fnc)(txt)
        

# ==============================================
# ==============================================


# ==============================================
# ==============================================

class EventMgr(object):
    """ For dispatching events to Event Manager scripts
    
        Windows Test, change to $HOOK_VAR for Linux
        >>> h = EventMgr("echo %HOOK_VAR%", {"HOOK_VAR":"test!"} , shell=True)
        >>> h.run()
        0
        >>> h.exists()
        False
    """
    def __init__(self, path, env_vars, shell = False):
        ""
        self.shell    = shell        
        self.env_vars = self._adjustEnvVars( env_vars )
        self.path     = os.path.expanduser(path)
    
    def _adjustEnvVars(self, vars):
        """ All environment variables must be string
        """
        for k,v in vars.iteritems():
            vars[k] = str(v)
        return vars
    
    def exists(self):
        """ Verifies if the target shell command exists.
            Note that this method only verifies the existence
            of filesystem path and not shell built-in commands
            e.g. *echo*
        """
        return os.path.exists(self.path)
    
    def run(self):
        return subprocess.call(self.path, env=self.env_vars, shell=self.shell)


# ==============================================
# ==============================================

if __name__ == "__main__":

    class TestCmd(BaseCmd):
        def __init__(self):
            BaseCmd.__init__(self)
            
        def cmd_a(self):
            """help cmd_a"""

        def cmd_b(self):
            """help cmd_b"""
            
    class TestCmdG2(CmdG2):
        def __init__(self):
            CmdG2.__init__(self)
            
    def tests(self):
        """ Tests
        
        >>> c = TestCmd()
        >>> print c.commands
        ['cmd_a', 'cmd_b']
        >>> g = TestCmdG2()
        >>> g.logdebug("Test")
        debug: Test
        """
        
    
    
    import doctest
    doctest.testmod()

