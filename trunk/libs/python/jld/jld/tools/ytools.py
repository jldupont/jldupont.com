#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id$"

import os
import logging
import yaml
from string import Template

class YattrException(Exception):
    ""
    def __init__(self, msg, params = None):
        Exception.__init__(self, msg)
        self.msg = msg
        self.params = params

class Yfile(object):
    """ The absolute path is available.
    """
    def __init__(self, src_file, name):
        self.path = None
        
        attrs = self._load(src_file, name)
        self._process(attrs)
        
    def _load(self, src_file, name):
        _base = os.path.dirname(src_file)
        self.path = os.path.join(_base, name)

        try:
            file = open(self.path,'r')
            defaults = yaml.load(file)
            file.close()
            
        except Exception,e:
            raise YattrException('error_load_file', {'path':self.path})
        
        return defaults

    def _process(self, attrs):
        ""
        if attrs:
            self.__dict__.update( attrs )
    
    
    # =========================================================
    # Iteration & Dict access interfaces
    #  Used for the command ''listconfig''
    # =========================================================
    def __contains__(self, key):
        return key in self.__dict__
    
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

    

class Yattr(Yfile):
    """
    >>> y= Yattr(__file__, 'tests/tests.yaml')
    >>> y.var0
    666
    >>> print y.var1
    value1
    """
    def __init__(self, src_file, name = 'defaults.yaml'):
        Yfile.__init__(self, src_file, name)

class Ymsg(Yfile):
    """
    >>> m = Ymsg(__file__, 'tests/tests.yaml')
    >>> print m.render('msg0')
    message0
    >>> print m.render('msg1', {'msg':'test'} )
    message1 test
    >>> print m.render2('msg2', ('test',) )
    message2 test
    """
    def __init__(self, src_file, name = 'messages.yaml'):
        Yfile.__init__(self, src_file, name)
        
    def render(self, key, params = None):
        """ Renders a message template with optional parameters
        
            @param key: the message key
            @param params: the optional parameters
            @return: the rendered message (string)
        """
        if not key in self.__dict__:
            logging.warn("Ymsg: missing message key[%s]" % key)
            return ''
            
        tpl = Template( self.__dict__[key] )
        return tpl.substitute( params ).lstrip()
    
    def render2(self, key, params = None):
        """ Renders a template through the 'old' python string templating engine
        
            @param key: the message key
            @param params: the parameters
            @return: rendered string
        """
        if not key in self.__dict__:
            logging.warn("Ymsg: missing message key[%s]" % key)
            return ''
        
        tpl = self.__dict__[key]
        if (params):
            return tpl % params
        
        return tpl
        

# ==============================================
# ==============================================

def loadFromFile(path, excp):
    """ Loads a yaml file.
        Raises a configurable exception upon any exception.
        
    >>> dr = os.path.dirname(__file__)
    >>> file = os.path.join(dr,'tests/tests.yaml')
    >>> data = loadFromFile(file,[TestExcp,'err'])
    >>> print data
    {'msg1': 'message1 $msg', 'var0': 666, 'msg2': 'message2 %s', 'var1': 'value1', 'var2': 'value2', 'msg0': 'message0'}
    """
    try:
        file = open(path,'r')
        data = yaml.load(file)
        file.close()
    except Exception,e:
        raise excp[0](excp[1], {'exc':e,'path':path})
    return data

    
# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    class TestExcp(Exception):
        def __init__(self, msg, params = None):
            Exception.__init__(self, msg)
            self.msg = msg
            self.params = params
        def __str__(self):
            return "%s: %s" % (self.msg, self.params)
    
    import doctest
    doctest.testmod()
