#!/usr/bin/env python
"""
    WebApi tools
    
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: __init__.py 883 2009-03-16 11:56:44Z JeanLou.Dupont $"

import logging
from types import *
from string import Template

from google.appengine.ext import webapp

import libs.markup as markup

class metaWebApi(type):
    """ Metaclass for Web based API handler classes
    """
    
    _prefix = 'method_'
    
    def __init__(cls, name, bases, ns):
        cls._scanMethods(ns, cls._prefix)
        cls._convertReSTDoc( ns )
        
        #try:
        #    cls._convertReSTDoc( ns )
        #except Exception,e:
        #    logging.warn("metaWebApi cls[%s] exception[%s]" % (cls, e))

    def _scanMethods(cls, ns, prefix = ''):
        """ 
        """
        cls._all_methods = map(    lambda X: X if type(getattr(cls,X)) is MethodType else None, ns)
        cls._all_methods = filter( lambda X: X is not None, cls._all_methods)
        if cls._all_methods:
            cls._prefix_methods = filter( lambda X: X.startswith(prefix), cls._all_methods )
        
        # drop the prefix
        cls._prefix_methods = map( lambda X: X[len(prefix):], cls._prefix_methods)
        
    
    def _convertReSTDoc(cls, ns):
        """ Converts all the docstrings of the
            class from ReST format to HTML
        """
        _classdocstring = getattr(cls, '__doc__')
        cls.__doc__ = cls.renderDocString(_classdocstring )

        #logging.info( "cls.__dict__ class[%s] >>>> %s" % (cls.__class__, str(cls.__dict__) ))
                    
        for method_name in cls._prefix_methods:
            
            prefixed_method_name = "%s%s" % (cls._prefix,method_name)
            method = getattr(cls, prefixed_method_name)           
            
            try:    doc = getattr(method, '__doc__')
            except: doc = ''

            res = cls.renderDocString(doc)
            #logging.info("_convert method[%s] res[%s]" % ( method_name, res))
            cls.__dict__[prefixed_method_name].__doc__ = res

    def renderDocString(cls, string):
        _processed = cls._preprocessDocString(string)
        rendered = markup.renderReSText(_processed)
        return rendered
         

    def _preprocessDocString(cls, doc):
        """ Trims leading spaces according to the first line.
            Helps keep docstring readable in the original python file.
        """
        if not doc:
            return ''
        
        lines = doc.splitlines()
        try:    
            firstline = lines[0].strip()
            spacer_count = len(lines[0]) - len(firstline)
        except: 
            return ''
        
        result = firstline + "\n"
        lines.pop(0)
        
        for line in lines:
            result = result + line[spacer_count:] + "\n"
        
        return result
        

# ===========================================================
# ===========================================================



class WebApi( webapp.RequestHandler ):
    __metaclass__ = metaWebApi
    
    _mime_html = "text/html"
    
    def renderMethodDocString(self, name, prefix='method_'):
        _doc = self.getDoc(name, prefix)
        return WebApi.renderDocString(_doc)
    
    def getDoc(self, name, prefix = 'method_'):
        return getattr(self, "%s%s" % (prefix, name)).__doc__
    
    @classmethod
    def methodExists(cls, name):
        return name in cls._prefix_methods

    def _output(self, code, content, mime = "text/plain" ):
        self.response.headers["Content-Type"] = mime
        self.response.set_status(code)
        self.response.out.write(content);

    def showServiceHelp(self, params = None):
        """
        """
        self.showHelp(self.__doc__, params)
        
    def showHelp(self, doc, params=None, convert = False):
        if convert:
            doc = WebApi.renderDocString(doc)
        t = Template(doc)
        self._output(200, t.safe_substitute(params), self._mime_html)
        
    def getConditionalHeaders(self):
        """ Retrieves the conditional headers
        
            @return: [if_modified_since, if_none_match]
        """
        try:    if_modified_since = self.request.headers['If-Modified-Since']
        except: if_modified_since = ''
        try:    if_none_match     = self.request.headers['If-None-Match']
        except: if_none_match     = ''

        return [if_modified_since, if_none_match]
    
    def doBaseResponse(self, result, etag, content_type, code): 
        """ Basic response
        """
        self.response.headers['ETag']          = '"' + etag + '"'
        self.response.headers['Content-Type']  = content_type
        self.response.set_status( code );
        self.response.out.write(result)
    