"""
Namespace

@author: Jean-Lou Dupont
"""

__all__ = ['NamespaceException', 'Namespace', 'Namespaces']

import sys
import os

#CONFIGURATION
#=============
_defaultNamespaces = [  {"domain": "main",      "base":"",      "raw":"raw"    },
                        {"domain": "template",  "base":"tpl",   "raw":"rawtpl"    },
                      ]

class NamespaceException():
    """
    NamespaceException
    """

class Namespace(object):
    """
    Namespace
    """
    def __init__(self, domain='', name=''):
        self._validate(domain,name)
        self.name   = name
        self.domain = domain 

    def isBase(self):
        return Namespaces.isBase(self.name) 
    
    def isRaw(self):
        return Namespaces.isRaw(self.name) 
    
    def _validate(self,domain,name):
        if (not Namespaces.validate(domain,name)):
            raise NamespaceException
    
    def __str__(self):
        return self.name
        
   
class Namespaces(object):
    """
    Namespaces
    """
    _spaces = _defaultNamespaces
    
    @staticmethod
    def add(domain,base,raw):
        Namespaces._spaces.append({"domain":domain, "base":base, "raw":raw})
    
    @staticmethod
    def exists(name):
        """
        Verifies if the 'name' exists in either
        one of the namespaces under any domain
        """
        for entry in Namespaces._spaces:
            if (entry["base"] == name):
                return True
            if (entry["raw"]  == name):
                return True
        return False

    @staticmethod
    def lookup(name):
        """ Returns the 'domain' associated with the given 'name'
        """
        for entry in Namespaces._spaces:
            if (entry["base"] == name):
                return entry["domain"]
            if (entry["raw"] == name):
                return entry["domain"]
        return None

    @staticmethod
    def isRaw(name):
        for entry in Namespaces._spaces:
            if (entry["raw"] == name):
                return entry["domain"]
        return None
            
    @staticmethod
    def isBase(name):
        for entry in Namespaces._spaces:
            if (entry["base"] == name):
                return entry["domain"]
        return None

    @staticmethod
    def fromName(name):
        id = Namespaces.lookup(name)
        if (id is None):
            raise NamespaceException, "Namespaces.fromName"
        return Namespace(id,name)

    @staticmethod
    def validate(domain,name):
        domainid = Namespaces.lookup(name)
        if (domainid is None):
            return False
        return (domainid == domain)

    @staticmethod
    def createRawFromBase(name):
        domain = Namespaces.isBase(name) 
        if (domain is None):
            raise NamespaceException, "name[%s] isn't a 'base'" % name
        raw = Namespaces.getRaw(domain)
        if (raw is None):
            raise NamespaceException, "domain[%s] configuration error " % domain
        return Namespace(domain,raw)
         
    @staticmethod
    def createBaseFromRaw(name):
        domain = Namespaces.isRaw(name) 
        if (domain is None):
            raise NamespaceException, "name[%s] isn't a 'raw'" % name
        base = Namespaces.getBase(domain)
        if (base is None):
            raise NamespaceException, "domain[%s] configuration error " % domain
        return Namespace(domain,base)
         
    @staticmethod
    def getRaw(domain):
        for entry in Namespaces._spaces:
            if (entry["domain"] == domain):
                return entry["raw"]
        return None

    @staticmethod
    def getBase(domain):
        for entry in Namespaces._spaces:
            if (entry["domain"] == domain):
                return entry["base"]
        return None
    
    @staticmethod
    def createRaw(name):
        """
        Takes an arbitrary 'name', finds the 'domain'
        and creates a corresponding 'raw' namespace 
        """
        domain  = Namespaces.lookup(name)
        if (domain is None):
            raise NamespaceException, "invalid name[%s]" % name
        raw = Namespaces.getRaw(domain)
        if (raw is None):
            raise NamespaceException, "configuration error with domain[%s]" % domain
        return Namespace(domain,raw)

    @staticmethod
    def createBase(name):
        """
        Takes an arbitrary 'name', finds the 'domain'
        and creates a corresponding 'base' namespace 
        """
        domain  = Namespaces.lookup(name)
        if (domain is None):
            raise NamespaceException, "invalid name[%s]" % name
        base = Namespaces.getBase(domain)
        if (base is None):
            raise NamespaceException, "configuration error with domain[%s]" % domain
        return Namespace(domain,base)

#======================================================================
#======================================================================


if (__name__ == "__main__" ):
    print "True  >>> " + str( Namespaces.exists("") )
    print "True  >>> " + str( Namespaces.exists("raw") )
    print "False >>> " + str( Namespaces.exists("inexistant") )
        