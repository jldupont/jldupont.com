""" Defaults
    Provides default configuration interface
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: defaults.py 809 2009-01-16 02:27:11Z JeanLou.Dupont $"

import yaml
import os
import sys
import logging

import jld.api as api

class Defaults(object):
    """
    """
    _path = None
    
    def __init__(self, filepath = None):
        self.filepath = filepath
        self.defaults = None
        self._load()
        
    def _load(self):
        
        path = self.filepath if self.filepath else self._path
        
        try:
            file = open(path,'r')
            self.defaults = yaml.load(file)
            file.close()
        except Exception,e:
            raise api.ErrorConfig('error_load_file', {'path':path})
    
