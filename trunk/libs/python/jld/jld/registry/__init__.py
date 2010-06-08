""" Cross-platform Registry
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: __init__.py 729 2008-12-11 18:30:12Z jeanlou.dupont $"
__dependencies__ = ['pyyaml>=3.06']

import sys

class Registry(object):
    """Facade for the cross-platform Registry
        Can be accessed like a dictionary: for this
        functionality, an instance must be constructed
        with the 'file' parameter specified.
    """
    reg = None
    
    def __init__(self, file = None):
        self.file = file
        
        if sys.platform[:3] == 'win':
            from jld.registry.windows import WindowsRegistry 
            self.reg = WindowsRegistry(file)
        else:
            from jld.registry.linux import LinuxRegistry
            self.reg = LinuxRegistry(file)
    
    def getKey(self, file, key):
        """GETS the specified key
            @raise RegistryException: exception
        """
        return self.reg.getKey(file, key)
    
    def setKey(self, file, key, value, cond = False):
        """SETS the specified key
            @raise RegistryException: exception
        """
        if (cond):
            if (value is None):
                #print "skipping key[%s]" % key
                return
        return self.reg.setKey(file, key, value)
    
    # DICTIONARY INTERFACE
    # ====================
    
    def __getitem__(self, key):
        """ Returns the value associated with key ELSE None
        """
        if (key not in self.reg):
            return None
        return self.reg[key]
    
    def __setitem__(self, key, value):
        self.reg[key] = value
    
    def __contains__(self, key):
        return (key in self.reg)
        
# ================================================================================

if __name__=="__main__":
    r = Registry()
    
    print 'GET>>>' + r.getKey('mindmeister', 'secret')
    print 'GET>>>' + r.getKey('mindmeister', 'api_key')
    
    r.setKey('mindmeister', 'test', 'TESTING!')
    
    print 'GET>>>' + r.getKey('mindmeister', 'test')
    
    r.setKey('mindmeister', 'secret', 'SECRET!')

    
