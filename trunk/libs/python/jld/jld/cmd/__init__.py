""" Command Line tools
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: __init__.py 811 2009-01-16 03:21:29Z JeanLou.Dupont $"
__dependencies__ = []

__all__ = ['BaseCmd', 'Hook']

import os
import sys
import subprocess

import jld.api as api
import jld.tools.klass as tclass

class BaseCmd(object):
    """ Base class for command line utilities
    """
    
    _platform_win32 = sys.platform[:3] == 'win'
    
    def __init__(self):
        """ Scans through all the methods of this instance
            and extracts all the ones prefixed with 'cmd_'
        """
        self.cmds, self.commands = tclass.searchForMethods( self, 'cmd_' )
        self.commands_help = ''
        
    # =========================================================
    # Iteration & Dict access interfaces
    #  Used for the command ''listconfig''
    # =========================================================
    def __contains__(self, key):
        return key in self._configParams
    
    def __getitem__(self, key):
        return getattr(self, key)
    
    def __setitem(self, key, value):
        setattr(self, key, value)
    
    def __iter__(self):
        self.iter = True
        return self
    
    def next(self):
        if (self.iter):
            self.iter = False
            return self
        else:
            raise StopIteration
        
    def genCommandsHelp(self, padding=15):
        """ Generates the list of commands and their corresponding docstring.
            Methods with prefix 'test' are ignored.
        """
        if (self.commands_help==''):
            for name, doc in self.commands:
                if (not name.startswith('test')):
                    line = "%*s : %s\n" % (padding,name,doc)
                    self.commands_help = self.commands_help + line #+ "  " + name + ' :  ' + doc + "\n"

        return self.commands_help
    
    def configure(self):
        """ Configures various parameters
        """

    def validateCommand(self, command):
        """ Validates the specified command
        """ 
        if (command not in self.cmds):
            raise api.ErrorInvalidCommand( 'invalid command', {'cmd':command} )

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
            raise api.ErrorPopen('', {'path':path, 'environ':environ})


# ==============================================
# ==============================================

class EventMgr(object):
    """ For dispatching events to Event Manager scripts
    
        Windows Test, change to $HOOK_VAR for Linux
        >>> h = EventMgr("echo %HOOK_VAR%", {"HOOK_VAR":"test!"} , shell=True)
        >>> h.run()
        (0, None)
        >>> h.exists()
        False
    """
    def __init__(self, path, env_vars, shell = False):
        ""
        self.env_vars = self._adjustEnvVars( env_vars )
        self.shell = shell
        self.path = os.path.expanduser(path)
    
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
    """ Tests
    """
    import doctest
    doctest.testmod()

