""" Registry Exception class
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: exception.py 708 2008-12-04 13:40:35Z JeanLou.Dupont $"

class RegistryException(Exception):
    """ An exception class for Registry
    """
    def __init__(self, value):
        self.value = value
    
    def __str__(self):
        return str(self.value)
